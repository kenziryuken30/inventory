<?php

namespace App\Http\Controllers;

use App\Models\InvToolkit;
use App\Models\InvSerialNumber;
use App\Models\InvTransactionItem;
use App\Models\InvConsumables;
use App\Models\InvTransaction;
use App\Models\InvConsumableTransactionItems;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang = InvToolkit::count();

        $alatTersedia = InvSerialNumber::where('is_available', 'Tersedia')->count();

        $alatDipinjam = InvTransactionItem::where('status', 'Dipinjam')->count();

        $consumableMenipis = InvConsumables::whereColumn('stock', '<=', 'minimum_stock')->count();

        $peminjamanTerbaru = InvTransaction::with(['employee', 'items.toolkit'])
            ->latest('date')
            ->take(5)
            ->get();

        $consumableTerbaru = InvConsumableTransactionItems::with('consumable')
        ->orderBy('id', 'desc')
        ->take(5)
        ->get();


        return view('dashboard.index', compact(
            'totalBarang',
            'alatTersedia',
            'alatDipinjam',
            'consumableMenipis',
            'peminjamanTerbaru',
            'consumableTerbaru'
        ));
    }
}

