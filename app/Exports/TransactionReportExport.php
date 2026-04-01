<?php

namespace App\Exports;

use App\Models\ToolTransaction;
use App\Models\ToolTransactionItem;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;

class TransactionReportExport implements FromArray
{
    protected $type, $start, $end;

    public function __construct($type, $start, $end)
    {
        $this->type = $type;
        $this->start = $start;
        $this->end = $end;
    }

    public function array(): array
    {
        $rows = [];

        // Judul
        $rows[] = ['LAPORAN TRANSAKSI TOOLS'];
        $rows[] = ['Tanggal Cetak : ' . date('d M Y')];

        if ($this->start || $this->end) {
            $rows[] = [
                'Periode : ' .
                ($this->start ? Carbon::parse($this->start)->format('d M Y') : '-') .
                ' s/d ' .
                ($this->end ? Carbon::parse($this->end)->format('d M Y') : '-')
            ];
        } else {
            $rows[] = ['Periode : Semua Data'];
        }

        $rows[] = []; // spasi

        // ================= PEMINJAMAN =================
        if ($this->type == 'peminjaman') {

            $rows[] = [
                'No',
                'Kode Transaksi',
                'Tanggal',
                'Karyawan',
                'Nama Alat',
                'No Seri',
                'Client',
                'Project'
            ];

            $query = ToolTransaction::with(['items.toolkit', 'items.serial']);

            if ($this->start) $query->whereDate('date', '>=', $this->start);
            if ($this->end)   $query->whereDate('date', '<=', $this->end);

            $data = $query->latest()->get();

            $no = 1;

            foreach ($data as $row) {

                $alat = [];
                $seri = [];

                foreach ($row->items as $item) {
                    $alat[] = $item->toolkit->toolkit_name ?? '-';
                    $seri[] = $item->serial->serial_number ?? '-';
                }

                $rows[] = [
                    $no++,
                    $row->transaction_code,
                    Carbon::parse($row->date)->format('d M Y'),
                    $row->borrower_name,
                    implode(' | ', $alat),
                    implode(' | ', $seri),
                    $row->client_name ?? '-',
                    $row->project ?? '-'
                ];
            }

        } else {

            // ================= PENGEMBALIAN =================
            $rows[] = [
                'No',
                'Kode Transaksi',
                'Tanggal',
                'Karyawan',
                'Nama Alat',
                'No Seri',
                'Kondisi',
                'Keterangan'
            ];

            $query = ToolTransactionItem::with([
                'transaction',
                'toolkit',
                'serial'
            ])->whereNotNull('return_date');

            if ($this->start) $query->whereDate('return_date', '>=', $this->start);
            if ($this->end)   $query->whereDate('return_date', '<=', $this->end);

            $data = $query->latest()->get();

            $no = 1;

            foreach ($data as $row) {

                $rows[] = [
                    $no++,
                    $row->transaction->transaction_code,
                    Carbon::parse($row->return_date)->format('d M Y'),
                    $row->transaction->borrower_name,
                    $row->toolkit->toolkit_name ?? '-',
                    $row->serial->serial_number ?? '-',
                    $row->return_condition ?? '-',
                    $row->return_note ?? '-'
                ];
            }
        }

        return $rows;
    }
}