<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Consumable</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 4px 6px;
        }

        th {
            background: #e8f4f8;
            text-align: center;
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .header-box {
            border: 1px solid #333;
            padding: 10px 16px;
            margin-bottom: 12px;
        }

        .header-box h2 {
            margin: 0 0 2px 0;
            font-size: 14px;
        }

        .header-box .subtitle {
            margin: 0;
            font-size: 9px;
            color: #555;
        }

        .period {
            margin-bottom: 10px;
            font-size: 9px;
        }
    </style>
</head>

<body>

    <div class="header-box">
        <h2>LAPORAN {{ strtoupper($type) }} CONSUMABLE</h2>
        <p class="subtitle">Rekap Data {{ ucfirst($type) }} Barang Consumable</p>
    </div>

    <div class="period">
        @php
            $startLabel = $period['start'] ? \Carbon\Carbon::parse($period['start'])->format('d F Y') : 'Awal';
            $endLabel = $period['end'] ? \Carbon\Carbon::parse($period['end'])->format('d F Y') : 'Sekarang';
        @endphp
        <strong>Periode:</strong> {{ $startLabel }} — {{ $endLabel }}
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <strong>Pengambilan:</strong> {{ now()->format('d F Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30px;">No</th>
                <th style="width:120px;">Kode</th>
                <th style="width:80px;">Tanggal</th>
                <th style="width:120px;">Karyawan</th>
                <th>Consumable</th>
                <th style="width:50px;">Jumlah</th>
                @if($type == 'pengembalian')
                    <th>Keterangan</th>
                @else
                    <th style="width:100px;">Client</th>
                    <th style="width:120px;">Project</th>
                    <th>Keperluan</th>
                @endif
            </tr>
        </thead>
        <tbody>

            @php $no = 1; @endphp

            @if($type == 'pengeluaran')

                @foreach($data as $transaction)
                    @foreach($transaction->items as $item)
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td class="text-left">{{ $transaction->transaction_code }}</td>
                            <td class="text-center">
                                {{ $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') : '-' }}
                            </td>
                            <td class="text-left">{{ $transaction->borrower_name }}</td>
                            <td class="text-left">{{ $item->consumable->name ?? '-' }}</td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td class="text-left">{{ $transaction->client ?? '-' }}</td>
                            <td class="text-left">{{ $transaction->project ?? '-' }}</td>
                            <td class="text-left">{{ $transaction->purpose ?? '-' }}</td>
                        </tr>
                    @endforeach
                @endforeach

            @else

                @foreach($data as $item)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-left">{{ $item->transaction->transaction_code }}</td>
                        <td class="text-center">
                            {{ $item->transaction->return_date ? \Carbon\Carbon::parse($item->transaction->return_date)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="text-left">{{ $item->transaction->borrower_name }}</td>
                        <td class="text-left">{{ $item->consumable->name ?? '-' }}</td>
                        <td class="text-center">{{ $item->qty_return }}</td>
                        <td class="text-left">{{ $item->note ?? '-' }}</td>
                    </tr>
                @endforeach

            @endif

        </tbody>
    </table>

</body>

</html>