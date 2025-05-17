<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    // Show all expenses grouped by date
    public function expenses_index()
    {
        $expenses = Expense::orderBy('date', 'desc')->orderBy('id', 'desc')->cursorPaginate(10);
        $groupedExpenses = $expenses->groupBy('date');
        return view('expenses.index', compact('expenses', 'groupedExpenses'));
    }

    // Show form to create a new expense
    public function expenses_create()
    {
        return view('expenses.create');
    }

    // Store a new expense
    public function expenses_store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0|max:9999999999.99',
            'date' => 'required|date',
            'expense_proof' => 'nullable|file|mimes:jpeg,png,jpg,webp,gif,pdf|max:10240', // Max 10MB
        ]);

        try {
            DB::beginTransaction();

            // Handle file upload
            if ($request->hasFile('expense_proof')) {
                $filePath = $request->file('expense_proof')->store('expense_proofs', 'public');
                $validatedData['expense_proof'] = $filePath;
            }

            Expense::create($validatedData);
            DB::commit();

            return redirect()->route('expenses_index')->with('success', 'Expense added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding expense: ' . $e->getMessage());

            return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred while adding the expense. Please try again.']);
        }
    }

    // Show expenses for a specific date
    public function expenses_showByDate($date)
    {
        $expenses = Expense::where('date', $date)->get();
        return view('expenses.show', compact('expenses', 'date'));
    }

    public function expenses_update(Request $request, Expense $expense)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'expense_proof' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:10240', // Max 10MB
        ]);

        try {
            DB::beginTransaction();

            // Handle file upload
            if ($request->hasFile('expense_proof')) {
                // Delete old file if it exists
                if ($expense->expense_proof) {
                    Storage::disk('public')->delete($expense->expense_proof);
                }

                $filePath = $request->file('expense_proof')->store('expense_proofs', 'public');
                $validatedData['expense_proof'] = $filePath;
            }

            $expense->update($validatedData);

            DB::commit();

            return redirect()->route('expenses_index')->with('success', 'Expense updated successfully!');
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Log the error
            Log::error('Error updating expense: ' . $e->getMessage());

            // Return an error message to the user
            return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred while updating the expense. Please try again.']);
        }
    }

    public function expenses_edit(Expense $expense)
    {
        // Log::info('Expense Data:', $expense->toArray());
        if (!$expense) {
            abort(404, 'Expense not found');
        }
        return view('expenses.edit', compact('expense'));
    }

    public function expenses_destroy(Expense $expense)
    {
        try {
            DB::beginTransaction();

            // Delete the associated proof file if it exists
            if ($expense->expense_proof) {
                Storage::disk('public')->delete($expense->expense_proof);
            }

            // Delete the expense
            $expense->delete();

            // Commit the transaction
            DB::commit();

            return redirect()->route('expenses_index')->with('success', 'Expense deleted successfully!');
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();

            // Log the error
            Log::error('Error deleting expense: ' . $e->getMessage());

            // Return an error message to the user
            return redirect()->back()->withErrors(['error' => 'An error occurred while deleting the expense. Please try again.']);
        }
    }

    public function expenses_load_more(Request $request)
    {
        $cursor = $request->query('cursor');

        // Fetch cursor-paginated expenses starting from the given cursor
        $expenses = Expense::orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->cursorPaginate(10, ['*'], 'cursor', $cursor);

        // Group expenses by date
        $groupedExpenses = $expenses->groupBy('date');

        // Return the partial view with the new expenses
        return view('expenses.partials.expense-list', compact('expenses', 'groupedExpenses'))->render();
    }
}
