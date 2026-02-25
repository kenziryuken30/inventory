@extends('layouts.app')

@section('content')

<div class="container">

    <h3 class="mb-4">Laporan Peminjaman Tools</h3>

    {{-- 🔹 Filter Tanggal --}}
    <form method="GET" action="{{ route('laporan.peminjaman') }}" class="row mb-4">

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
            <a href="{{ route('laporan.peminjaman') }}" class="btn btn-secondary">
                Reset
            </a>
        </div>
    </form>

    {{-- 🔹 Total --}}
    <div class="mb-3">
        <strong>Total Peminjaman: {{ $transactions->count() }}</strong>
    </div>

    {{-- 🔹 Tabel --}}
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-info">
                <tr>
                    <th>Kode Transaksi</th>
                    <th>Tanggal Pinjam</th>
                    <th>Peminjam</th>
                    <th>Nama Alat</th>
                    <th>No Seri</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>

                @forelse($transactions as $transaction)
                    @foreach($transaction->items as $item)
                        <tr>
                            <td>{{ $transaction->transaction_code }}</td>
                            <td>{{ $transaction->date }}</td>
                            <td>{{ $transaction->borrower_name }}</td>
                            <td>{{ $item->toolkit->name ?? '-' }}</td>
                            <td>{{ $item->serial->serial_number ?? '-' }}</td>
                            <td>
                                @if($item->return_date)
                                    <span class="badge bg-success">Kembali</span>
                                @else
                                    <span class="badge bg-warning text-dark">Dipinjam</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            Tidak ada data pada periode ini
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

</div>

@endsection