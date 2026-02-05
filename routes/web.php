<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\InvConsumableController;
use App\Http\Controllers\InvTransactionController;

Route::get('/', function () {
    return redirect('/login');
});


Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/data-tools', [ToolController::class, 'index'])->name('tools.index');
    Route::post('/data-tools', [ToolController::class, 'store'])->name('tools.store');
    Route::put('/data-tools/{id}', [ToolController::class, 'update'])->name('tools.update');
    Route::delete('/data-tools/{id}', [ToolController::class, 'destroy'])->name('tools.destroy');

    Route::post(
        '/data-tools/{id}/finish-maintenance',
        [ToolController::class, 'finishMaintenance']
    )->name('tools.finishMaintenance');


    Route::get('/consumable', [InvConsumableController::class, 'index']);
    Route::post('/consumable', [InvConsumableController::class, 'store']);
    Route::put('/consumable/{id}', [InvConsumableController::class, 'update']);
    Route::delete('/consumable/{id}', [InvConsumableController::class, 'destroy']);

    Route::prefix('peminjaman')->group(function () {
        Route::get('/', [InvTransactionController::class, 'index'])->name('peminjaman.index');
        Route::post('/store', [InvTransactionController::class, 'store'])->name('peminjaman.store');
        Route::get('/create', [InvTransactionController::class, 'create'])->name('peminjaman.create');
        Route::post('/{id}/confirm', [InvTransactionController::class, 'confirm'])->name('peminjaman.confirm');
        Route::post('/{id}/return', [InvTransactionController::class, 'return'])->name('peminjaman.return');
    });
});
