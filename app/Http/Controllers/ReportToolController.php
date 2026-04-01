<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ToolTransaction;
use App\Models\ToolTransactionItem;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\TransactionReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportToolController extends Controller
{

    public function index(Request $request)
    {
        $type = $request->get('type', 'peminjaman');

        [$start, $end] = $this->fixDate($request);

        $search = $request->search;

        if ($type === 'pengembalian') {

            $query = ToolTransactionItem::with([
                'transaction',
                'toolkit',
                'serial'
            ])->whereNotNull('return_date');

            if ($start) {
                $query->whereDate('return_date', '>=', $start);
            }

            if ($end) {
                $query->whereDate('return_date', '<=', $end);
            }

            if ($search) {
                $query->whereHas('transaction', function ($q) use ($search) {
                    $q->where('borrower_name', 'like', '%' . $search . '%');
                });
            }

            $data = $query->orderBy('return_date', 'desc')->orderBy('id', 'desc')->paginate(10)->withQueryString();
        } else {

            $query = ToolTransaction::with([
                'items.toolkit',
                'items.serial'
            ]);

            if ($start) {
                $query->whereDate('date', '>=', $start);
            }

            if ($end) {
                $query->whereDate('date', '<=', $end);
            }

            if ($search) {
                $query->where('borrower_name', 'like', '%' . $search . '%');
            }

            $data = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(10);
        }

        return view('laporan.tools.transaksi', compact('data', 'type'));
    }

    private function fixDate($request)
    {
        $start = $request->start_date;
        $end   = $request->end_date;

        if ($start && $end && $start > $end) {
            $end = $start;
        }

        return [$start, $end];
    }

    public function exportPDF(Request $request)
    {
        $type = $request->type ?? 'peminjaman';

        [$start, $end] = $this->fixDate($request);

        if ($type === 'pengembalian') {

            $query = ToolTransactionItem::with([
                'transaction',
                'toolkit',
                'serial'
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

        $pdf = Pdf::loadView('laporan.tools.export_pdf', compact('data', 'type'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('laporan_tools_' . $type . '.pdf');
    }

   public function exportExcel(Request $request)
{
    $type = $request->type ?? 'peminjaman';

    [$start, $end] = $this->fixDate($request);

    return Excel::download(
        new TransactionReportExport($type, $start, $end),
        'laporan_tools_' . $type . '.xlsx'
    );
}
}
