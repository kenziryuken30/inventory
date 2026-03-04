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
            padding: 3px;
            text-align: center;
        }

        th {
            background: #f2f2f2;
        }
    </style>
</head>

<body>

    <h2>LAPORAN PENGELUARAN CONSUMABLE ({{ strtoupper($type) }})</h2>

    <table>
        <thead>
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
        </thead>
        <tbody>

            @php $no = 1; @endphp

            @if($type == 'pengeluaran')

                @foreach($data as $transaction)
                    @foreach($transaction->items as $item)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $transaction->transaction_code ?? '-' }}</td>
                            <td>{{ $transaction->date ?? '-' }}</td>
                            <td>{{ $transaction->borrower_name ?? '-' }}</td>
                            <td>{{ $item->consumable->name ?? '-' }}</td>
                            <td>{{ $item->qty ?? 0 }}</td>
                            <td>{{ $transaction->client ?? '-' }}</td>
                            <td>{{ $transaction->project ?? '-' }}</td>
                            <td>{{ $transaction->purpose ?? '-' }}</td>
                        </tr>
                    @endforeach
                @endforeach

            @else

                @foreach($data as $item)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ $item->transaction->transaction_code ?? '-' }}</td>
                        <td>{{ $item->return_date ?? '-' }}</td>
                        <td>{{ $item->transaction->borrower_name ?? '-' }}</td>
                        <td>{{ $item->consumable->name ?? '-' }}</td>
                        <td>{{ $item->qty_return ?? 0 }}</td>
                        <td>{{ $item->transaction->client ?? '-' }}</td>
                        <td>{{ $item->transaction->project ?? '-' }}</td>
                        <td>{{ $item->transaction->purpose ?? '-' }}</td>
                    </tr>
                @endforeach

            @endif

        </tbody>
    </table>

</body>

</html>