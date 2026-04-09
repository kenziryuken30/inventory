<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #f2f2f2;
        }

        .text-left {
            text-align: left;
        }
    </style>

</head>

<body>

    <h2>
        @if($type == 'pengeluaran')
            Laporan Pengeluaran Consumable
        @else
            Laporan Pengembalian Consumable
        @endif
    </h2>

    <div style="width:100%; margin-bottom:10px; font-size:12px;">

        <span style="float:left;">
            <strong>Periode :</strong>

            @if($period['start'] || $period['end'])
                {{ $period['start'] ? \Carbon\Carbon::parse($period['start'])->format('d M Y') : '-' }}
                s/d
                {{ $period['end'] ? \Carbon\Carbon::parse($period['end'])->format('d M Y') : '-' }}
            @else
                Semua Data
            @endif
        </span>

        <span style="float:right;">
            <strong>Tanggal Cetak :</strong>
            {{ \Carbon\Carbon::now()->format('d M Y') }}
        </span>

        <div style="clear:both;"></div>

    </div>

    <table>

        <thead>

            @if($type == 'pengeluaran')

                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Tanggal</th>
                    <th>Karyawan</th>
                    <th>Consumable</th>
                    <th>Jumlah</th>
                    <th>Client</th>
                    <th>Project</th>
                    <th>Keperluan</th>
                </tr>

            @else

                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Tanggal</th>
                    <th>Karyawan</th>
                    <th>Consumable</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                </tr>

            @endif

        </thead>

        <tbody>

            @php $no = 1; @endphp

            @if($type == 'pengeluaran')

                @foreach($data as $transaction)
                    @foreach($transaction->items as $item)

                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $transaction->transaction_code }}</td>
                            <td>{{ $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('d M Y') : '-' }}</td>
                            <td class="text-left">{{ $transaction->borrower_name }}</td>
                            <td class="text-left">{{ $item->consumable->name ?? '-' }}</td>
                            <td>{{ $item->qty }}</td>
                            <td class="text-left">{{ $transaction->client ?? '-' }}</td>
                            <td class="text-left">{{ $transaction->project ?? '-' }}</td>
                            <td class="text-left">{{ $transaction->purpose ?? '-' }}</td>
                        </tr>

                    @endforeach
                @endforeach

            @else

                @foreach($data as $item)

                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $item->transaction->transaction_code }}</td>
                        <td>{{ $item->transaction->return_date ? \Carbon\Carbon::parse($item->transaction->return_date)->format('d M Y') : '-' }}</td>
                        <td class="text-left">{{ $item->transaction->borrower_name }}</td>
                        <td class="text-left">{{ $item->consumable->name ?? '-' }}</td>
                        <td>{{ $item->qty_return }}</td>
                        <td class="text-left">{{ $item->note ?? '-' }}</td>
                    </tr>

                @endforeach

            @endif

        </tbody>

    </table>

</body>

</html>