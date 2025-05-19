<?php

use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ExpenseController::class, 'expenses_index'])->name('expenses_index');
Route::get('expenses_create', [ExpenseController::class, 'expenses_create'])->name('expenses_create');
Route::post('expenses_store', [ExpenseController::class, 'expenses_store'])->name('expenses_store');
Route::get('expenses_load_more', [ExpenseController::class, 'expenses_load_more'])->name('expenses_load_more');
Route::get('/expenses_showByDate/{date}', [ExpenseController::class, 'expenses_showByDate'])->name('expenses_showByDate');

Route::get('/expenses_edit/{expense}', [ExpenseController::class, 'expenses_edit'])->name('expenses_edit');
Route::put('/expenses_update/{expense}', [ExpenseController::class, 'expenses_update'])->name('expenses_update');
Route::delete('/expenses_destroy/{expense}', [ExpenseController::class, 'expenses_destroy'])->name('expenses_destroy');

Route::get('expenses_calendar', [ExpenseController::class, 'expenses_calendar'])->name('expenses_calendar');
Route::get('expenses_calendar_data', [ExpenseController::class, 'expenses_calendar_data'])->name('expenses_calendar_data');
