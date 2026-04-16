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

    private function getData($request, $type, $isExport = false)
    {
        if ($type === 'pengembalian') {

            $query = InvConsumableTransactionItem::with([
                'transaction',
                'consumable'
            ])
                ->whereNotNull('qty_return')
                ->where('qty_return', '>', 0);

            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            if ($request->filled('search')) {
                $query->whereHas('transaction', function ($q) use ($request) {
                    $q->where('borrower_name', 'like', '%' . $request->search . '%');
                });
            }
        } else {

            $query = InvConsumableTransaction::with([
                'items.consumable'
            ])->where('is_confirm', 1);

            if ($request->filled('start_date')) {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('date', '<=', $request->end_date);
            }

            if ($request->filled('search')) {
                $query->where('borrower_name', 'like', '%' . $request->search . '%');
            }
        }

        if ($type === 'pengembalian') {
            $query->orderBy('created_at', 'desc') 
                ->orderBy('id', 'desc');
        } else {
            $query->orderBy('date', 'desc')
                ->orderBy('id', 'desc');
        }

        return $isExport
            ? $query->get()
            : $query->paginate(10)->withQueryString();
    }


    public function transaksi(Request $request)
    {
        $type = $request->get('type', 'pengeluaran');

        [$start, $end] = [$request->start_date, $request->end_date];

        if ($start && $end && $end < $start) {
            $data = collect(); 
            $totalItems = 0;
            $totalTransaksi = 0;

            return view('laporan.consumable.transaksi', compact(
                'data',
                'type',
                'totalItems',
                'totalTransaksi'
            ));
        }

        // BARU QUERY JALAN
        $allData = $this->getData($request, $type, true);
        $data = $this->getData($request, $type);

        if ($type == 'pengeluaran') {
            $totalItems = $allData->flatMap->items->sum('qty');
        } else {
            $totalItems = $allData->sum('qty_return');
        }

        $totalTransaksi = $allData->count();

        return view('laporan.consumable.transaksi', compact(
            'data',
            'type',
            'totalItems',
            'totalTransaksi'
        ));
    }


    public function exportPdf(Request $request)
    {
        $type = $request->type ?? 'pengeluaran';

        $data = $this->getData($request, $type, true);

        $period = [
            'start' => $request->start_date,
            'end'   => $request->end_date,
        ];

        $pdf = Pdf::loadView('laporan.consumable.export', [
            'data'   => $data,
            'type'   => $type,
            'period' => $period,
        ])->setPaper('A4', 'landscape');

        return $pdf->download('laporan_consumable_' . $type . '.pdf');
    }


    public function exportExcel(Request $request)
    {
        $type = $request->type ?? 'pengeluaran';

        $data = $this->getData($request, $type, true);

        return Excel::download(
            new ReportConsumableExport($data, $type, $request->start_date, $request->end_date),
            'laporan_consumable_' . $type . '.xlsx'
        );
    }
}
