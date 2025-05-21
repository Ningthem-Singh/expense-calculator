<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Expense extends Model
{
    protected $fillable = [
        'title',
        'description',
        'amount',
        'date',
        'expense_proof',
    ];

    /**
     * Fetch all expenses formatted for FullCalendar.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getCalendarData()
    {
        return self::all()->map(function ($expense) {
            return [
                'id' => $expense->id,
                'title' => $expense->title,
                'start' => $expense->date, // FullCalendar expects dates in 'YYYY-MM-DD' format
                'formattedDate' => formatDate($expense->date), // Custom property to display date
                'formattedAmount' => formatAmount($expense->amount), // Custom property to display amount
                'description' => $expense->description,
                'expenseProof' => $expense->expense_proof ? asset('storage/' . $expense->expense_proof) : null,
            ];
        });
    }

    /**
     * Store a new expense.
     *
     * @param array $data
     * @return \App\Models\Expense
     */
    public static function storeExpense(array $data)
    {
        if (isset($data['expense_proof'])) {
            $filePath = $data['expense_proof']->store('expense_proofs', 'public');
            $data['expense_proof'] = $filePath;
        }

        return self::create($data);
    }

    /**
     * Update an existing expense.
     *
     * @param array $data
     * @return bool
     */
    public function updateExpense(array $data)
    {
        if (isset($data['expense_proof'])) {
            if ($this->expense_proof) {
                Storage::disk('public')->delete($this->expense_proof);
            }
            $filePath = $data['expense_proof']->store('expense_proofs', 'public');
            $data['expense_proof'] = $filePath;
        }

        if (isset($data['remove_proof']) && $data['remove_proof']) {
            if ($this->expense_proof) {
                Storage::disk('public')->delete($this->expense_proof);
            }
            $data['expense_proof'] = null; // Set proof to null
        }

        return $this->update($data);
    }

    /**
     * Delete an expense and its associated file.
     *
     * @return bool
     */
    public function deleteExpense()
    {
        if ($this->expense_proof) {
            Storage::disk('public')->delete($this->expense_proof);
        }

        return $this->delete();
    }
}
