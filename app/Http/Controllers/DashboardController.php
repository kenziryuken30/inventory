<?php

namespace App\Http\Controllers;

use App\Models\InvToolkit;
use App\Models\InvSerialNumber;
use App\Models\InvTransactionItem;
use App\Models\InvConsumable;
use App\Models\InvTransaction;
use App\Models\InvConsumableTransactionItem;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang = InvToolkit::count();
        $alatTersedia = InvSerialNumber::where('status', 'tersedia')->count();
        $alatDipinjam = InvTransactionItem::where('status', 'Dipinjam')->count();

        $consumableMenipis = InvConsumable::whereColumn('stock', '<=', 'minimum_stock')->count();
        $lowStock = InvConsumable::whereColumn('stock', '<', 'minimum_stock')->get();
        $peminjamanTerbaru = InvTransaction::with(['items.toolkit'])
            ->latest('date')
            ->take(5)
            ->get();

        $consumableTerbaru = InvConsumableTransactionItem::with('consumable')
        ->orderBy('id', 'desc')
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