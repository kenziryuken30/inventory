<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\ToolTransaction;
use App\Models\ToolTransactionItem;
use App\Models\InvSerialNumber;
use Illuminate\Support\Facades\DB;

class ReportToolController extends Controller
{
    public function peminjaman(Request $request)
{
    $query = ToolTransaction::with(['items.toolkit', 'items.serial']);

    if ($request->filled('start_date')) {
        $query->whereDate('date', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('date', '<=', $request->end_date);
    }

    $transactions = $query->latest()->get();

    return view('laporan.peminjaman', compact('transactions'));
}

public function pengembalian(Request $request)
{
    $query = ToolTransactionItem::with(['transaction', 'toolkit', 'serial'])
        ->whereNotNull('return_date');

    if ($request->filled('start_date')) {
        $query->whereDate('return_date', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('return_date', '<=', $request->end_date);
    }

    $returns = $query->latest()->get();

    return view('laporan.pengembalian', compact('returns'));
}
}
