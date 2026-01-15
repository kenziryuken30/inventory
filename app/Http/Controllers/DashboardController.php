<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'totalBarang' => Item::count(),
            'alatTersedia' => Item::where('tipe','alat')->where('status','tersedia')->count(),
            'alatDipinjam' => Item::where('tipe','alat')->where('status','dipinjam')->count(),
            'alatRusak' => Item::where('status','rusak')->count(),
            'stokSparepart' => Item::where('tipe','sparepart')->sum('stok'),
            'sparepartMenipis' => Item::where('tipe','sparepart')
                ->whereColumn('stok','<=','stok_minimum')->count(),
        ]);
    }
}
