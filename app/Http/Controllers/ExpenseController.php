<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    // Show all expenses grouped by date
    public function index()
    {
        $expenses = Expense::orderBy('date', 'desc')->get()->groupBy('date');
        return view('expenses.index', compact('expenses'));
    }

    // Show form to create a new expense
    public function create()
    {
        return view('expenses.create');
    }

    // Store a new expense
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        Expense::create($validatedData);

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully!');
    }

    // Show expenses for a specific date
    public function showByDate($date)
    {
        $expenses = Expense::where('date', $date)->get();
        return view('expenses.show', compact('expenses', 'date'));
    }
}
