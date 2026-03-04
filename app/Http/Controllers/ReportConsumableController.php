<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvConsumableTransaction;
use App\Models\InvConsumableTransactionItem;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportConsumableExport;


class ReportConsumableController extends Controller
{
    public function transaksi(Request $request)
{
    $type = $request->type ?? 'pengeluaran';
    $search = $request->search;
    $start = $request->start_date;
    $end   = $request->end_date;

    if ($type == 'pengeluaran') {

        $query = InvConsumableTransaction::with('items.consumable')
            ->latest();

        if ($search) {
            $query->where('borrower_name', 'like', '%' . $search . '%');
        }

        if ($start && $end) {
            $query->whereBetween('date', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        }

        $data = $query->get();
    }

    else { // ================= PENGEMBALIAN =================

        $query = InvConsumableTransaction::with('items.consumable')
            ->whereHas('items', function ($q) {
                $q->whereNull('qty_return');
            })
            ->latest();

        if ($search) {
            $query->where('borrower_name', 'like', '%' . $search . '%');
        }

        if ($start && $end) {
            $query->whereBetween('date', [
                Carbon::parse($start)->startOfDay(),
                Carbon::parse($end)->endOfDay()
            ]);
        }

        $data = $query->get();
    }

    return view('laporan.consumable.transaksi', compact('data', 'type'));
}

    public function exportPdf(Request $request)
    {
        $type = $request->type ?? 'pengeluaran';

        $data = $this->getFilteredData($request, $type)->get();

        $pdf = Pdf::loadView('laporan.consumable.export', compact('data', 'type'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan-Consumable.export.pdf');
    }

    public function exportExcel(Request $request)
    {
        $type = $request->type ?? 'pengeluaran';

        $data = $this->getFilteredData($request, $type)->get();

        return Excel::download(
            new ReportConsumableExport($data, $type),
            'Laporan-Consumable.xlsx'
        );
    }
    public function getFilteredData($request, $type)
    {
        // ================= PENGELUARAN =================
        if ($type == 'pengeluaran') {

            $query = InvConsumableTransaction::with('items.consumable');

            if ($request->search) {
                $query->where('borrower_name', 'like', '%' . $request->search . '%');
            }

            if ($request->start_date) {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->whereDate('date', '<=', $request->end_date);
            }

            return $query;
        }

        // ================= PENGEMBALIAN =================
        else {

            $query = InvConsumableTransactionItem::with([
                'transaction',
                'consumable'
            ])->whereNotNull('qty_return');

            if ($request->search) {
                $query->whereHas('transaction', function ($q) use ($request) {
                    $q->where('borrower_name', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->start_date) {
                $query->whereHas('transaction', function ($q) use ($request) {
                    $q->whereDate('date', '>=', $request->start_date);
                });
            }

            if ($request->end_date) {
                $query->whereHas('transaction', function ($q) use ($request) {
                    $q->whereDate('date', '<=', $request->end_date);
                });
            }

            return $query;
        }
    }
}
