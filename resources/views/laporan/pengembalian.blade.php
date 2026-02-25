@extends('layouts.app')

@section('content')

<div class="container">

    <h3 class="mb-4">Laporan Pengembalian Tools</h3>

    {{-- 🔹 Filter Tanggal --}}
    <form method="GET" action="{{ route('laporan.pengembalian') }}" class="row mb-4">

        <div class="col-md-3">
            <label>Dari Tanggal</label>
            <input type="date" name="start_date" class="form-control"
                   value="{{ request('start_date') }}">
        </div>

        <div class="col-md-3">
            <label>Sampai Tanggal</label>
            <input type="date" name="end_date" class="form-control"
                   value="{{ request('end_date') }}">
        </div>

        <div class="col-md-3 align-self-end">
            <button class="btn btn-primary">Filter</button>
            <a href="{{ route('laporan.pengembalian') }}" class="btn btn-secondary">
                Reset
            </a>
        </div>
    </form>

    {{-- 🔹 Total --}}
    <div class="mb-3">
        <strong>Total Pengembalian: {{ $returns->count() }}</strong>
    </div>

    {{-- 🔹 Tabel --}}
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-success">
                <tr>
                    <th>Kode Transaksi</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Peminjam</th>
                    <th>Nama Alat</th>
                    <th>No Seri</th>
                    <th>Kondisi</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>

                @forelse($returns as $item)
                    <tr>
                        <td>{{ $item->transaction->transaction_code ?? '-' }}</td>
                        <td>{{ $item->transaction->date ?? '-' }}</td>
                        <td>{{ $item->return_date }}</td>
                        <td>{{ $item->transaction->borrower_name ?? '-' }}</td>
                        <td>{{ $item->toolkit->name ?? '-' }}</td>
                        <td>{{ $item->serial->serial_number ?? '-' }}</td>
                        <td>{{ $item->condition ?? '-' }}</td>
                        <td>{{ $item->note ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            Tidak ada data pengembalian pada periode ini
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

</div>

@endsection