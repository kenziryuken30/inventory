@extends('layouts.app')

@section('content')
<div class="container">

<h4>Edit Transaksi Consumable</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('transaksi.update', $transaction->id) }}" method="POST">
@csrf
@method('PUT')

<div class="row mb-3">
    <div class="col-md-6">
        <label>Nama Peminjam</label>
        <input type="text" name="borrower_name"
               value="{{ $transaction->borrower_name }}"
               class="form-control" required>
    </div>

    <div class="col-md-6">
        <label>Tanggal</label>
        <input type="date" name="date"
               value="{{ $transaction->date->format('Y-m-d') }}"
               class="form-control" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label>Client</label>
        <input type="text" name="client"
               value="{{ $transaction->client }}"
               class="form-control">
    </div>
    <div class="col-md-4">
        <label>Project</label>
        <input type="text" name="project"
               value="{{ $transaction->project }}"
               class="form-control">
    </div>
    <div class="col-md-4">
        <label>Keperluan</label>
        <input type="text" name="purpose"
               value="{{ $transaction->purpose }}"
               class="form-control">
    </div>
</div>

<button class="btn btn-primary">Simpan</button>
<a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Kembali</a>
</form>

<hr>

<h5>Daftar Consumable</h5>

<table class="table table-bordered">
<thead>
<tr>
    <th>No</th>
    <th>Nama</th>
    <th>Qty</th>
    <th>Unit</th>
    <th>Aksi</th>
</tr>
</thead>
<tbody>
@foreach($transaction->items as $item)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $item->consumable->name }}</td>

    <td>
        <form action="{{ route('transaksi.item.update', $item->id) }}"
              method="POST" class="d-flex gap-2">
            @csrf
            @method('PUT')
            <input type="number" name="qty"
                   value="{{ $item->qty }}"
                   min="1"
                   class="form-control form-control-sm"
                   style="width:90px">
            <button class="btn btn-success btn-sm">Update</button>
        </form>
    </td>

    <td>{{ $item->consumable->unit }}</td>

    <td>
        <form action="{{ route('transaksi.item.destroy', $item->id) }}"
              method="POST"
              onsubmit="return confirm('Hapus item?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm">Hapus</button>
        </form>
    </td>
</tr>
@endforeach
</tbody>
</table>

</div>
@endsection
