<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class ReportConsumableExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $type;

    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function collection()
    {
        $rows = [];
        $no = 1;

        // ================= PENGELUARAN =================
        if ($this->type == 'pengeluaran') {

            foreach ($this->data as $trx) {
                foreach ($trx->items as $item) {

                    $rows[] = [
                        $no++,
                        $trx->transaction_code,
                        $trx->date,
                        $trx->borrower_name,
                        $item->consumable->name ?? '-',
                        $item->qty,
                        $trx->client ?? '-',
                        $trx->project ?? '-',
                        $trx->purpose ?? '-',
                    ];
                }
            }
        }
        // ================= PENGEMBALIAN =================
        else {

            foreach ($this->data as $item) {

                $rows[] = [
                    $no++,
                    $item->transaction->transaction_code,
                    $item->transaction->return_date ?? '-',
                    $item->transaction->borrower_name,
                    $item->consumable->name ?? '-',
                    $item->qty_return,
                    $item->note ?? '-',
                ];
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        if ($this->type == 'pengembalian') {
            return [
                'No',
                'Kode',
                'Tanggal',
                'Peminjam',
                'Consumable',
                'Jumlah',
                'Keterangan',
            ];
        }

        return [
            'No',
            'Kode',
            'Tanggal',
            'Peminjam',
            'Consumable',
            'Jumlah',
            'Client',
            'Project',
            'Keperluan',
        ];
    }
}
