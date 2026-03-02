<?php

namespace App\Http\Controllers;

use App\Models\InvToolkit;
use App\Models\InvSerialNumber;
use App\Models\ToolTransactionItem;
use App\Models\InvConsumable;
use App\Models\ToolTransaction;
use App\Models\InvConsumableTransactionItem;

class DashboardController extends Controller
{
    public function index()
{
    // TOTAL ALAT (jumlah serial)
    $totalBarang = InvSerialNumber::count();

    // ALAT TERSEDIA
    $alatTersedia = InvSerialNumber::where('status', 'TERSEDIA')->count();

    // ALAT DIPINJAM
    $alatDipinjam = InvSerialNumber::where('status', 'DIPINJAM')->count();

    // Consumable
    $consumableMenipis = InvConsumable::whereColumn('stock', '<=', 'minimum_stock')->count();

    $lowStock = InvConsumable::whereColumn('stock', '<', 'minimum_stock')->get();

    // Peminjaman terbaru
    $peminjamanTerbaru = ToolTransaction::with(['items.toolkit'])
        ->latest('date')
        ->take(5)
        ->get();

    $consumableTerbaru = InvConsumableTransactionItem::with('consumable')
        ->latest()
        ->take(5)
        ->get();

    return view('dashboard.index', compact(
        'totalBarang',
        'alatTersedia',
        'alatDipinjam',
        'consumableMenipis',
        'lowStock',
        'peminjamanTerbaru',
        'consumableTerbaru'
    ));
}
}