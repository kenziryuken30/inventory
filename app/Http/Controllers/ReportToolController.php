<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\ToolTransaction;
use App\Models\ToolTransactionItem;
use App\Models\InvSerialNumber;
use Illuminate\Support\Facades\DB;

class ReportToolController extends Controller
{
public function index(Request $request)
{
    $type = $request->get('type', 'peminjaman');

    if ($type === 'pengembalian') {

        $query = ToolTransactionItem::with([
            'transaction',
            'toolkit',
            'serial.conditionLogs'
        ])->whereNotNull('return_date');

        if ($request->filled('start_date')) {
            $query->whereDate('return_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('return_date', '<=', $request->end_date);
        }

        $data = $query->latest()->get();

    } else {

        $query = ToolTransaction::with([
            'items.toolkit',
            'items.serial'
        ]);

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $data = $query->latest()->get();
    }

    return view('laporan.tools.transaksi', compact('data', 'type'));
}

}
