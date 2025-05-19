<?php

use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ExpenseController::class, 'expenses_index'])->name('expenses_index');
Route::get('/expenses/create', [ExpenseController::class, 'expenses_create'])->name('expenses_create');
Route::post('/expenses', [ExpenseController::class, 'expenses_store'])->name('expenses_store');
Route::get('/expenses/load-more', [ExpenseController::class, 'expenses_load_more'])->name('expenses_load_more');
Route::get('/expenses/{date}', [ExpenseController::class, 'expenses_showByDate'])->name('expenses_showByDate');

Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'expenses_edit'])->name('expenses_edit');
Route::put('/expenses/{expense}', [ExpenseController::class, 'expenses_update'])->name('expenses_update');
Route::delete('/expenses/{expense}', [ExpenseController::class, 'expenses_destroy'])->name('expenses_destroy');