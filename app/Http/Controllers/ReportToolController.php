<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ToolTransaction;
use App\Models\ToolTransactionItem;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportToolController extends Controller
{

public function index(Request $request)
{
    $type = $request->get('type', 'peminjaman');

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

        $data = $query->latest()->paginate(10)->withQueryString();

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

        $data = $query->latest()->paginate(10);
    }

    return view('laporan.tools.transaksi', compact('data','type'));
}


public function exportPDF(Request $request)
{
    $type = $request->type ?? 'peminjaman';

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

    $pdf = Pdf::loadView('laporan.tools.export_pdf', compact('data','type'))
        ->setPaper('A4','landscape');

    return $pdf->download('laporan_tools_'.$type.'.pdf');
}

public function exportExcel(Request $request)
{
    $type = $request->type ?? 'peminjaman';

    $filename = "laporan_tools_" . $type . ".csv";

    $headers = [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
    ];

    $callback = function() use ($request,$type) {

        $file = fopen('php://output', 'w');

        // Judul laporan
        fputcsv($file, ['LAPORAN TRANSAKSI TOOLS'], ';');
        fputcsv($file, ['Tanggal Cetak : '.date('d M Y')], ';');
        fputcsv($file, [], ';');

        if($type == 'peminjaman'){

            fputcsv($file, [
                'No',
                'Kode Transaksi',
                'Tanggal Pinjam',
                'Karyawan',
                'Nama Alat',
                'No Seri',
                'Client',
                'Project'
            ], ';');

            $query = ToolTransaction::with(['items.toolkit','items.serial']);

            if ($request->filled('start_date')) {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('date', '<=', $request->end_date);
            }

            $data = $query->latest()->get();

            $no = 1;

            foreach($data as $row){

                $alat = [];
                $seri = [];

                foreach($row->items as $item){
                    $alat[] = $item->toolkit->toolkit_name ?? '-';
                    $seri[] = $item->serial->serial_number ?? '-';
                }

                fputcsv($file, [
                    $no++,
                    $row->transaction_code,
                    "'".\Carbon\Carbon::parse($row->date)->format('d M Y')."'",
                    $row->borrower_name,
                    implode(' | ', $alat),
                    implode(' | ', $seri),
                    $row->client_name ?? '-',
                    $row->project ?? '-'
                ], ';');
            }

        } else {

            fputcsv($file, [
                'No',
                'Kode Transaksi',
                'Tanggal Kembali',
                'Karyawan',
                'Nama Alat',
                'No Seri',
                'Kondisi',
                'Keterangan'
            ], ';');

            $query = ToolTransactionItem::with([
                'transaction','toolkit','serial'
            ])->whereNotNull('return_date');

            if ($request->filled('start_date')) {
                $query->whereDate('return_date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('return_date', '<=', $request->end_date);
            }

            $data = $query->latest()->get();

            $no = 1;

            foreach($data as $row){

                fputcsv($file, [
                    $no++,
                    $row->transaction->transaction_code,
                    "'".\Carbon\Carbon::parse($row->return_date)->format('d M Y')."'",
                    $row->transaction->borrower_name,
                    $row->toolkit->toolkit_name ?? '-',
                    $row->serial->serial_number ?? '-',
                    $row->return_condition ?? '-',
                    $row->return_note ?? '-'
                ], ';');
            }
        }

        fclose($file);
    };

    return response()->stream($callback,200,$headers);
}
}