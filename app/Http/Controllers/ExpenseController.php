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

        // Convert the next cursor to a string
        $nextCursor = $expenses->nextCursor()?->encode(); // Encode the cursor to a string
        // $nextCursor = $expenses->nextCursor();
        // dd($nextCursor);
        return view('expenses.index', compact('expenses', 'groupedExpenses', 'nextCursor'));
    }

    public function expenses_load_more(Request $request)
    {
        $cursor = $request->query('cursor');

        $expenses = Expense::orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->cursorPaginate(10, ['*'], 'cursor', $cursor);

        $groupedExpenses = $expenses->groupBy('date');

        $nextCursor = $expenses->nextCursor()?->encode(); // Encode the cursor to a string

        return view('expenses.partials.expense_list', compact('expenses', 'groupedExpenses', 'nextCursor'))->render();
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
        // Validate the date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            abort(404, 'Invalid date format.');
        }
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
            'remove_proof' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('expense_proof')) {
                // Delete old file if it exists
                if ($expense->expense_proof) {
                    Storage::disk('public')->delete($expense->expense_proof);
                }
                $filePath = $request->file('expense_proof')->store('expense_proofs', 'public');
                $validatedData['expense_proof'] = $filePath;
            }

            if ($request->input('remove_proof')) {
                if ($expense->expense_proof) {
                    Storage::disk('public')->delete($expense->expense_proof);
                }
                $validatedData['expense_proof'] = null; // Set proof to null
            }

            $expense->update($validatedData);
            DB::commit();

            return redirect()->route('expenses_index')->with('success', 'Expense updated successfully!');
        } catch (\Exception $e) {

            DB::rollBack();
            Log::error('Error updating expense: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred while updating the expense. Please try again.']);
        }
    }

    public function expenses_edit(Expense $expense)
    {
        if (!$expense) {
            abort(404, 'Expense not found');
        }
        return view('expenses.edit', compact('expense'));
    }

    public function expenses_destroy(Expense $expense)
    {
        try {
            DB::beginTransaction();

            if ($expense->expense_proof) {
                Storage::disk('public')->delete($expense->expense_proof);
            }

            $expense->delete();
            DB::commit();

            return redirect()->route('expenses_index')->with('success', 'Expense deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting expense: ' . $e->getMessage());

            return redirect()->back()->withErrors(['error' => 'An error occurred while deleting the expense. Please try again.']);
        }
    }

    public function expenses_calendar()
    {
        return view('expenses.calendar');
    }

    public function expenses_calendar_data()
    {
        // Fetch all expenses and format them for FullCalendar
        $expenses = Expense::all();

        $events = $expenses->map(function ($expense) {
            return [
                // 'id' => $expense->id,
                'title' => $expense->title,
                'start' => $expense->date, // FullCalendar expects dates in 'YYYY-MM-DD' format
                'amount' => $expense->amount, // Custom property to display the amount
                'description' => $expense->description,
            ];
        });

        return response()->json($events);
    }
}
