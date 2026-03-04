<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>

body{
    font-family: DejaVu Sans;
    font-size:12px;
}

h2{
    text-align:center;
    margin-bottom:20px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    border:1px solid black;
    padding:6px;
    text-align:center;
}

th{
    background:#f2f2f2;
}

</style>

</head>

<body>

<h2>
@if($type=='peminjaman')
Laporan Peminjaman Tools
@else
Laporan Pengembalian Tools
@endif
</h2>

<table>

<thead>

@if($type=='peminjaman')

<tr>
<th>No</th>
<th>Kode Transaksi</th>
<th>Tgl Pinjam</th>
<th>Karyawan</th>
<th>Alat Pinjam</th>
<th>No Seri</th>
<th>Client</th>
<th>Project</th>
</tr>

@else

<tr>
<th>No</th>
<th>Kode Transaksi</th>
<th>Tgl Kembali</th>
<th>Karyawan</th>
<th>Alat Dipinjam</th>
<th>No Seri</th>
<th>Kondisi</th>
<th>Keterangan</th>
</tr>

@endif

</thead>

<tbody>

@if($type=='peminjaman')

@foreach($data as $row)

<tr>

<td>{{ $loop->iteration }}</td>

<td>{{ $row->transaction_code }}</td>

<td>{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</td>

<td>{{ $row->borrower_name }}</td>

<td>
@foreach($row->items as $item)
{{ $item->toolkit->toolkit_name ?? '-' }}<br>
@endforeach
</td>

<td>
@foreach($row->items as $item)
{{ $item->serial->serial_number ?? '-' }}<br>
@endforeach
</td>

<td>{{ $row->client_name ?? '-' }}</td>

<td>{{ $row->project ?? '-' }}</td>

</tr>

@endforeach


@else

@php
$group = $data->groupBy('transaction_id');
@endphp

@foreach($group as $items)

<tr>

<td>{{ $loop->iteration }}</td>

<td>{{ $items->first()->transaction->transaction_code }}</td>

<td>{{ \Carbon\Carbon::parse($items->first()->return_date)->format('d M Y') }}</td>

<td>{{ $items->first()->transaction->borrower_name }}</td>

<td>
@foreach($items as $item)
{{ $item->toolkit->toolkit_name ?? '-' }}<br>
@endforeach
</td>

<td>
@foreach($items as $item)
{{ $item->serial->serial_number ?? '-' }}<br>
@endforeach
</td>

<td>
@foreach($items as $item)
{{ $item->return_condition ?? '-' }}<br>
@endforeach
</td>

<td>
@foreach($items as $item)
{{ $item->return_note ?? '-' }}<br>
@endforeach
</td>

</tr>

@endforeach

@endif

</tbody>

</table>

</body>
</html>