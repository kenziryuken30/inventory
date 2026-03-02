<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvConsumableTransaction;
use App\Models\InvConsumableTransactionItem;
use Carbon\Carbon;

class ReportConsumableController extends Controller
{
    public function transaksi(Request $request)
    {
        $type = $request->type ?? 'pengeluaran';

        $start = $request->start_date;
        $end   = $request->end_date;

        if ($type == 'pengeluaran') {

            $query = InvConsumableTransaction::with('items.consumable')
                        ->latest();

            if ($start && $end) {
                $query->whereBetween('date', [
                    Carbon::parse($start)->startOfDay(),
                    Carbon::parse($end)->endOfDay()
                ]);
            }

            $data = $query->get();
        }

        else {

            $query = InvConsumableTransactionItem::with([
                        'transaction',
                        'consumable'
                    ])
                    ->whereNotNull('qty_return') 
                    ->latest();

            if ($start && $end) {
                $query->whereBetween('return_date', [
                    Carbon::parse($start)->startOfDay(),
                    Carbon::parse($end)->endOfDay()
                ]);
            }

            $data = $query->get();
        }

        return view('laporan.consumable.transaksi', compact('data', 'type'));
    }
}