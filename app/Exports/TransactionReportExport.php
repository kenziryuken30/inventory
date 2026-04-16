<?php

namespace App\Exports;

use App\Models\ToolTransaction;
use App\Models\ToolTransactionItem;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\FromCollection;


class TransactionReportExport implements
    FromCollection,
    WithHeadings,
    WithEvents,
    WithCustomStartCell
{
    protected $type, $start, $end;

    public function __construct($type, $start, $end)
    {
        $this->type = $type;
        $this->start = $start;
        $this->end = $end;
    }

    public function collection()
    {
        $rows = [];

        if ($this->type == 'peminjaman') {

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
                    \Carbon\Carbon::parse($row->date)->format('d M Y'),
                    $row->borrower_name,
                    implode(' | ', $alat),
                    implode(' | ', $seri),
                    $row->client_name ?? '-',
                    $row->project ?? '-'
                ];
            }
        } else { 

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
                    $row->transaction->transaction_code ?? '-',
                    \Carbon\Carbon::parse($row->return_date)->format('d M Y'),
                    $row->transaction->borrower_name ?? '-',
                    $row->toolkit->toolkit_name ?? '-',
                    $row->serial->serial_number ?? '-',
                    $row->return_condition ?? '-',
                    $row->return_note ?? '-',
                ];
            }
        }

        return collect($rows);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            5 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();
                $lastCol = $this->type == 'peminjaman' ? 'H' : 'H';

                // Judul
                $sheet->mergeCells('A1:' . $lastCol . '1');
                $sheet->setCellValue('A1', 'LAPORAN TRANSAKSI TOOLS');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                // Periode
                $sheet->mergeCells('A2:' . $lastCol . '2');

                $start = $this->start
                    ? \Carbon\Carbon::parse($this->start)->format('d F Y')
                    : 'Awal';

                $end = $this->end
                    ? \Carbon\Carbon::parse($this->end)->format('d F Y')
                    : 'Sekarang';

                $sheet->setCellValue('A2', 'Periode: ' . $start . ' — ' . $end . ' | Pengambilan: ' . now()->format('d F Y'));

                // Auto width
                foreach (range('A', $lastCol) as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Header style
                $sheet->getStyle("A4:{$lastCol}4")->getFont()->setBold(true);
                $sheet->getStyle("A4:{$lastCol}4")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('E8F4F8');

                // Border
                $lastRow = 4 + count($this->collection());
                $sheet->getStyle("A4:{$lastCol}{$lastRow}")
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
    public function startCell(): string
    {
        return 'A4';
    }

    public function headings(): array
    {
        if ($this->type == 'peminjaman') {
            return [
                'No',
                'Kode',
                'Tanggal',
                'Peminjam',
                'Alat',
                'No Seri',
                'Client',
                'Project'
            ];
        }

        return [
            'No',
            'Kode',
            'Tanggal',
            'Peminjam',
            'Alat',
            'No Seri',
            'Kondisi',
            'Keterangan'
        ];
    }
}
