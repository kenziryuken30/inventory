<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\InvConsumableController;
use App\Http\Controllers\InvTransactionController;
use App\Http\Controllers\ConsumableTransactionController;

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


    Route::get('/consumable', [InvConsumableController::class, 'index'])->name('consumable.index');
    Route::post('/consumable', [InvConsumableController::class, 'store']);
    Route::put('/consumable/{id}', [InvConsumableController::class, 'update']);
    Route::delete('/consumable/{id}', [InvConsumableController::class, 'destroy']);

    
   Route::prefix('transaksi')->name('transaksi.')->group(function () {

        Route::get('/', [ConsumableTransactionController::class, 'index'])->name('index');
        Route::get('/create', [ConsumableTransactionController::class, 'create'])->name('create');
        Route::post('/', [ConsumableTransactionController::class, 'store'])->name('store');

        Route::get('/{id}/edit', [ConsumableTransactionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ConsumableTransactionController::class, 'update'])->name('update');

        Route::put('/item/{itemId}', [ConsumableTransactionController::class, 'updateItem'])
            ->name('item.update');

        Route::delete('/item/{itemId}', [ConsumableTransactionController::class, 'destroyItem'])
            ->name('item.destroy');
    });


    Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
        
        Route::get('/', [InvTransactionController::class, 'index'])->name('index');
        Route::get('/create', [InvTransactionController::class, 'create'])->name('create');
        Route::post('/', [InvTransactionController::class, 'store'])->name('store');

        Route::get('/{id}/edit', [InvTransactionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [InvTransactionController::class, 'update'])->name('update');
        Route::delete('/{id}', [InvTransactionController::class, 'destroy'])->name('destroy');

        Route::post('/{id}/confirm', [InvTransactionController::class, 'confirm'])->name('confirm');
        Route::post('/{id}/return', [InvTransactionController::class, 'return'])->name('return');

        Route::delete(
            '/item/{id}',
            [InvTransactionController::class, 'destroyItem']
        )->name('item.destroy');
    });

});
