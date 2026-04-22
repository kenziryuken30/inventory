<?php

namespace App\Http\Controllers;

use App\Models\InvToolkit;
use App\Models\InvSerialNumber;
use App\Models\ToolTransactionItem;
use App\Models\InvConsumable;
use App\Models\ToolTransaction;
use App\Models\InvConsumableTransactionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $consumableTerbaru = InvConsumableTransactionItem::with(['consumable', 'transaction'])
            ->whereHas('transaction', function ($q) {
                $q->where('is_confirm', true);
            })
            ->whereNotNull('consumable_id')
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        $activities = DB::table('activity_logs')
            ->join('users', 'activity_logs.user_id', '=', 'users.id')
            ->select('activity_logs.*', 'users.name')
            ->orderBy('activity_logs.created_at', 'desc')
            ->limit(5)
            ->get();


        return view('dashboard.index', compact(
            'totalBarang',
            'alatTersedia',
            'alatDipinjam',
            'consumableMenipis',
            'lowStock',
            'peminjamanTerbaru',
            'consumableTerbaru',
            'activities'
        ));
    }
}
