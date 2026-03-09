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
        } else { // ================= PENGEMBALIAN =================

            $query = InvConsumableTransactionItem::with([
                'transaction',
                'consumable'
            ])
                ->whereNotNull('qty_return')
                ->where('qty_return', '>', 0)
                ->latest();

            if ($search) {
                $query->whereHas('transaction', function ($q) use ($search) {
                    $q->where('borrower_name', 'like', '%' . $search . '%');
                });
            }

            if ($start && $end) {
                $query->whereBetween('created_at', [
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
        $type = $request->type;

        $query = InvConsumableTransactionItem::with(['transaction', 'consumable']);

        if ($request->start_date && $request->end_date) {
            $query->whereHas('transaction', function ($q) use ($request) {
                $q->whereBetween('date', [$request->start_date, $request->end_date]);
            });
        }

        $data = $query->get();

        $pdf = Pdf::loadView('laporan.consumable.export', [
            'data' => $data,
            'type' => $type
        ]);

        return $pdf->download('laporan_consumable.pdf');
    }

    public function exportExcel(Request $request)
    {
        $type = $request->type;

        if ($type == 'pengeluaran') {

            $query = InvConsumableTransaction::with('items.consumable');

            if ($request->start_date) {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->whereDate('date', '<=', $request->end_date);
            }

            if ($request->search) {
                $query->where('borrower_name', 'like', '%' . $request->search . '%');
            }

            $data = $query->get();
        } else {

            $query = InvConsumableTransactionItem::with('transaction', 'consumable');

            if ($request->start_date) {
                $query->whereDate('return_date', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $query->whereDate('return_date', '<=', $request->end_date);
            }

            if ($request->search) {
                $query->whereHas('transaction', function ($q) use ($request) {
                    $q->where('borrower_name', 'like', '%' . $request->search . '%');
                });
            }

            $data = $query->get();
        }

        return Excel::download(
            new ReportConsumableExport($data, $type),
            'laporan_consumable.xlsx'
        );
    }
    public function getFilteredData($request, $type)
    {
        // ================= PENGELUARAN =================
        if ($type == 'pengeluaran') {

            $query = InvConsumableTransaction::with('items.consumable')
                ->latest();

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
            ])
                ->whereNotNull('qty_return')
                ->where('qty_return', '>', 0)
                ->latest();

            if ($request->search) {
                $query->whereHas('transaction', function ($q) use ($request) {
                    $q->where('borrower_name', 'like', '%' . $request->search . '%');
                });
            }

            if ($request->start_date && $request->end_date) {
                $query->whereHas('transaction', function ($q) use ($request) {
                    $q->whereBetween('return_date', [
                        Carbon::parse($request->start_date)->startOfDay(),
                        Carbon::parse($request->end_date)->endOfDay()
                    ]);
                });
            }

            return $query;
        }
    }
}
