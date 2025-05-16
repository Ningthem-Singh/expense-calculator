<?php

use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ExpenseController::class, 'expenses_index'])->name('expenses_index');
Route::get('/expenses/create', [ExpenseController::class, 'expenses_create'])->name('expenses_create');
Route::post('/expenses', [ExpenseController::class, 'expenses_store'])->name('expenses_store');
Route::get('/expenses/{date}', [ExpenseController::class, 'expenses_showByDate'])->name('expenses_showByDate');
