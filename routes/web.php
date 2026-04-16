<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\InvConsumableController;
use App\Http\Controllers\ToolTransactionController;
use App\Http\Controllers\ConsumableTransactionController;
use App\Http\Controllers\ReportToolController;
use App\Http\Controllers\ReportConsumableController;
use App\Http\Controllers\InvCategoryController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==================== DATA CONSUMABLE ====================
    Route::get('/consumable', [InvConsumableController::class, 'index'])->name('consumable.index');
    Route::post('/consumable', [InvConsumableController::class, 'store']);
    Route::put('/consumable/{id}', [InvConsumableController::class, 'update']);
    Route::delete('/consumable/{id}', [InvConsumableController::class, 'destroy']);
    Route::post('/consumable/{id}/restock', [InvConsumableController::class, 'restock']);
    Route::get('/consumable/check-name', [InvConsumableController::class, 'checkName'])->name('consumable.check-name');

    // ==================== TRANSAKSI CONSUMABLE ====================
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('/', [ConsumableTransactionController::class, 'index'])->name('index');
        Route::get('/create', [ConsumableTransactionController::class, 'create'])->name('create');
        Route::post('/', [ConsumableTransactionController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ConsumableTransactionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ConsumableTransactionController::class, 'update'])->name('update');
        Route::delete('/{id}', [ConsumableTransactionController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/confirm', [ConsumableTransactionController::class, 'confirm'])->name('confirm');
        Route::post('/{id}/return', [ConsumableTransactionController::class, 'kembali'])->name('return');
    });

    // ==================== DATA TOOLS ====================
    Route::get('/data-tools', [ToolController::class, 'index'])->name('tools.index');
    Route::post('/data-tools', [ToolController::class, 'store'])->name('tools.store');
    Route::put('/data-tools/{id}', [ToolController::class, 'update'])->name('tools.update');
    Route::delete('/data-tools/{id}', [ToolController::class, 'destroy'])->name('tools.destroy');
    Route::post('/data-tools/{id}/finish-maintenance', [ToolController::class, 'finishMaintenance'])->name('tools.finishMaintenance');

    // ==================== PEMINJAMAN TOOLS ====================
    Route::prefix('peminjaman')->name('peminjaman.')->group(function () {
        Route::get('/', [ToolTransactionController::class, 'index'])->name('index');
        Route::get('/create', [ToolTransactionController::class, 'create'])->name('create');
        Route::post('/', [ToolTransactionController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ToolTransactionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ToolTransactionController::class, 'update'])->name('update');
        Route::delete('/{id}', [ToolTransactionController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/confirm', [ToolTransactionController::class, 'confirm'])->name('confirm');
        Route::post('/{id}/return', [ToolTransactionController::class, 'returnProcess'])->name('return.process');
        Route::post('/{id}/add-item', [ToolTransactionController::class, 'addItem'])->name('item.add');
        Route::delete('/item/{id}', [ToolTransactionController::class, 'destroyItem'])->name('item.destroy');
    });

    // ==================== LAPORAN TOOLS ====================
    Route::get('/laporan/transaksi-tools', [ReportToolController::class, 'index'])->name('laporan.tools.transaksi');
    Route::get('/laporan/tools/export-pdf', [ReportToolController::class, 'exportPDF'])->name('laporan.tools.export.pdf');
    Route::get('/laporan/tools/export/excel', [ReportToolController::class, 'exportExcel'])->name('laporan.tools.export.excel');

    // ==================== LAPORAN CONSUMABLE ====================
    Route::get('/laporan/consumable/transaksi', [ReportConsumableController::class, 'transaksi'])->name('laporan.consumable.transaksi');
    Route::get('/laporan/consumable/export/pdf', [ReportConsumableController::class, 'exportPdf'])->name('laporan.consumable.export.pdf');
    Route::get('/laporan/consumable/export/excel', [ReportConsumableController::class, 'exportExcel'])->name('laporan.consumable.export.excel');
    Route::get('/report-consumable/export', [ReportConsumableController::class, 'export'])->name('report.export');

    // ==================== CATEGORIES ====================
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [InvCategoryController::class, 'index'])->name('index');
        Route::get('/create', [InvCategoryController::class, 'create'])->name('create');
        Route::post('/', [InvCategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [InvCategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [InvCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [InvCategoryController::class, 'destroy'])->name('destroy');
    });
    
    Route::get('/api/proxy/employee-list', [ConsumableTransactionController::class, 'proxyEmployeeList']);
    Route::get('/api/proxy/client-list', [ConsumableTransactionController::class, 'proxyClientList']);
    Route::get('/api/proxy/client-projects', [ConsumableTransactionController::class, 'proxyClientProjects']);

    Route::get('/api/employees', [ToolTransactionController::class, 'getEmployeesApi']);

    Route::get('/api/clients', [ToolTransactionController::class, 'getClientsApi']);

    Route::get('/api/projects', [ToolTransactionController::class, 'getProjectsApi']);
});
