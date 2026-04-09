<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Collection;

class ReportConsumableExport implements FromCollection, WithHeadings, WithTitle, WithEvents, WithCustomStartCell
{
    protected $data;
    protected $type;
    protected $startDate;
    protected $endDate;

    public function __construct($data, $type, $startDate = null, $endDate = null)
    {
        $this->data = $data;
        $this->type = $type;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function title(): string
    {
        return 'Laporan ' . ucfirst($this->type);
    }

    public function collection()
    {
        $rows = [];
        $no = 1;

        if ($this->type == 'pengeluaran') {

            foreach ($this->data as $trx) {
                foreach ($trx->items as $item) {
                    $rows[] = [
                        $no++,
                        $trx->transaction_code,
                        $trx->date ? \Carbon\Carbon::parse($trx->date)->format('d-m-Y') : '-',
                        $trx->borrower_name,
                        $item->consumable->name ?? '-',
                        $item->qty,
                        $trx->client ?? '-',
                        $trx->project ?? '-',
                        $trx->purpose ?? '-',
                    ];
                }
            }
        } else {

            foreach ($this->data as $item) {
                $rows[] = [
                    $no++,
                    $item->transaction->transaction_code,
                    $item->transaction->return_date
                        ? \Carbon\Carbon::parse($item->transaction->return_date)->format('d-m-Y')
                        : '-',
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = $this->getLastColumn();

                // Row 1: Judul
                $sheet->mergeCells('A1:' . $lastCol . '1');
                $sheet->setCellValue('A1', 'LAPORAN ' . strtoupper($this->type) . ' CONSUMABLE');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                // Row 2: Periode
                if ($this->startDate || $this->endDate) {
                    $startLabel = $this->startDate
                        ? \Carbon\Carbon::parse($this->startDate)->format('d M Y')
                        : '-';

                    $endLabel = $this->endDate
                        ? \Carbon\Carbon::parse($this->endDate)->format('d M Y')
                        : '-';

                    $periodeText = "Periode: $startLabel s/d $endLabel";
                } else {
                    $periodeText = "Periode: Semua Data";
                }

                $sheet->mergeCells('A2:' . $lastCol . '2');
                $sheet->setCellValue('A2', $periodeText . '  |  Tanggal Cetak: ' . now()->format('d M Y'));
                $sheet->getStyle('A2')->getFont()->setSize(9);

                // Auto-width
                foreach (range('A', $lastCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Header row bold + background
                $sheet->getStyle("A4:{$lastCol}4")
                    ->getFont()->setBold(true);
                $sheet->getStyle("A4:{$lastCol}4")
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('E8F4F8');

                // Hitung baris data terakhir
                $rowCount = count($this->collection());
                $lastRow = 4 + $rowCount;

                // Border semua data
                $sheet->getStyle("A4:{$lastCol}{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }

    private function getLastColumn(): string
    {
        return $this->type == 'pengembalian' ? 'G' : 'I';
    }
}
