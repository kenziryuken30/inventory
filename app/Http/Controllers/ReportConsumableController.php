<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvConsumableTransaction;
use App\Models\InvConsumableTransactionItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportConsumableExport;

class ReportConsumableController extends Controller
{

    public function transaksi(Request $request)
    {
        $type = $request->get('type', 'pengeluaran');

        if ($type === 'pengembalian') {

            $query = InvConsumableTransactionItem::with([
                'transaction',
                'consumable'
            ])
                ->whereNotNull('qty_return')
                ->where('qty_return', '>', 0);

            if ($request->filled('start_date')) {
                $query->whereHas('transaction', function ($q) use ($request) {
                    $q->whereDate('return_date', '>=', $request->start_date);
                });
            }

            if ($request->filled('end_date')) {
                $query->whereHas('transaction', function ($q) use ($request) {
                    $q->whereDate('return_date', '<=', $request->end_date);
                });
            }

            if ($request->filled('search')) {
                $query->whereHas('transaction', function ($q) use ($request) {
                    $q->where('borrower_name', 'like', '%' . $request->search . '%');
                });
            }

            $data = $query->latest()->get();
        } else {

            $query = InvConsumableTransaction::with([
                'items.consumable'
            ]);

            if ($request->filled('start_date')) {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('date', '<=', $request->end_date);
            }

            if ($request->filled('search')) {
                $query->where('borrower_name', 'like', '%' . $request->search . '%');
            }

            $data = $query->latest()->get();
        }

        return view('laporan.consumable.transaksi', compact('data', 'type'));
    }



    public function exportPdf(Request $request)
    {
        $type = $request->type ?? 'pengeluaran';

        $data = $this->getData($request, $type);

        $pdf = Pdf::loadView('laporan.consumable.export', compact('data', 'type'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('laporan_consumable_' . $type . '.pdf');
    }



    public function exportExcel(Request $request)
    {
        $type = $request->type ?? 'pengeluaran';

        $data = $this->getData($request, $type);

        return Excel::download(
            new ReportConsumableExport($data, $type),
            'laporan_consumable_' . $type . '.xlsx'
        );
    }



    private function getData($request, $type)
    {
        if ($type === 'pengembalian') {

            $query = InvConsumableTransactionItem::with([
                'transaction',
                'consumable'
            ])
                ->whereNotNull('qty_return')
                ->where('qty_return', '>', 0);

            if ($request->filled('start_date')) {
                $query->whereHas('transaction', function ($q) use ($request) {
                    $q->whereDate('return_date', '>=', $request->start_date);
                });
            }

            if ($request->filled('end_date')) {
                $query->whereHas('transaction', function ($q) use ($request) {
                    $q->whereDate('return_date', '<=', $request->end_date);
                });
            }

            if ($request->filled('search')) {
                $query->whereHas('transaction', function ($q) use ($request) {
                    $q->where('borrower_name', 'like', '%' . $request->search . '%');
                });
            }

            return $query->latest()->get();
        } else {

            $query = InvConsumableTransaction::with([
                'items.consumable'
            ]);

            if ($request->filled('start_date')) {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('date', '<=', $request->end_date);
            }

            if ($request->filled('search')) {
                $query->where('borrower_name', 'like', '%' . $request->search . '%');
            }

            return $query->latest()->get();
        }
    }
}
