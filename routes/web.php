<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');

Route::post('/loan', [LoanController::class, 'store']);
Route::post('/sparepart', [SparepartController::class, 'store']);
