<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\InvConsumableController;
use App\Http\Controllers\ToolTransactionController;
use App\Http\Controllers\ConsumableTransactionController;
use App\Http\Controllers\ReportToolController;

Route::get('/', function () {
    return redirect('/login');
});


Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Consumable
    Route::get('/consumable', [InvConsumableController::class, 'index'])->name('consumable.index');
    Route::post('/consumable', [InvConsumableController::class, 'store']);
    Route::put('/consumable/{id}', [InvConsumableController::class, 'update']);
    Route::delete('/consumable/{id}', [InvConsumableController::class, 'destroy']);
    
    // Transaksi Consumable
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
    Route::get('/', [ConsumableTransactionController::class, 'index'])->name('index');
    Route::get('/create', [ConsumableTransactionController::class, 'create'])->name('create');
    Route::post('/', [ConsumableTransactionController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ConsumableTransactionController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ConsumableTransactionController::class, 'update'])->name('update');
    Route::delete('/{id}', [ConsumableTransactionController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/confirm',[ConsumableTransactionController::class, 'confirm'])->name('confirm');
    Route::post('/{id}/return', [ConsumableTransactionController::class, 'kembali'])->name('return');
    Route::post('transaksi/{id}/kembali', [ConsumableTransactionController::class, 'kembali'])->name('transaksi.kembali');
    });

    // Data Tools 
    Route::get('/data-tools', [ToolController::class, 'index'])->name('tools.index');
    Route::post('/data-tools', [ToolController::class, 'store'])->name('tools.store');
    Route::put('/data-tools/{id}', [ToolController::class, 'update'])->name('tools.update');
    Route::delete('/data-tools/{id}', [ToolController::class, 'destroy'])->name('tools.destroy');

    Route::post('/data-tools/{id}/finish-maintenance',
    [ToolController::class, 'finishMaintenance']
)->name('tools.finishMaintenance');

    // Peminjaman Tools
    Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
    Route::get('/', [ToolTransactionController::class, 'index'])->name('index');
    Route::get('/create', [ToolTransactionController::class, 'create'])->name('create');
    Route::post('/', [ToolTransactionController::class, 'store'])->name('store');

    Route::get('/{id}/edit', [ToolTransactionController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ToolTransactionController::class, 'update'])->name('update');
    Route::delete('/{id}', [ToolTransactionController::class, 'destroy'])->name('destroy');

    Route::post('/{id}/confirm', [ToolTransactionController::class, 'confirm'])->name('confirm');

    Route::post('/{id}/return', [ToolTransactionController::class, 'returnProcess'])
        ->name('return.process');

    Route::post('/{id}/add-item', [ToolTransactionController::class, 'addItem'])
        ->name('item.add');

    Route::delete('/item/{id}', [ToolTransactionController::class, 'destroyItem'])
        ->name('item.destroy');
});

    
Route::get('/laporan/transaksi-tools', 
    [ReportToolController::class, 'index']
)->name('laporan.tools.transaksi');
});
