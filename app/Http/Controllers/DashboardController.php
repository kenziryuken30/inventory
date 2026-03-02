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

        // 🔥 PEMINJAMAN TERBARU (maks 4, terbaru di atas)
        $peminjamanTerbaru = ToolTransaction::with(['items.toolkit'])
            ->orderByDesc('date') // lebih jelas dari latest
            ->limit(4)
            ->get();

        // 🔥 CONSUMABLE TERBARU (maks 4)
        $consumableTerbaru = InvConsumableTransactionItem::with('consumable')
            ->orderByDesc('created_at')
            ->limit(4)
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