@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Daftar Peminjaman</h4>

    {{-- 🔥 TOMBOL PINJAM TOOLS --}}
    <a href="{{ route('peminjaman.create') }}"
       class="btn btn-primary">
        + Pinjam Tools
    </a>

    {{-- -Flash Massage --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <table class="table table-bordered table-stripped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peminjam</th>
                <th>Tanggal Peminjaman</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($transactions as $transaction)
                @foreach ($transaction->items as $item)
                    <tr>
                        {{-- tanggal pinjam --}}
                        <td>
                            {{ $transaction->date->format('d-m-Y') }}
                        </td>

                        {{-- karyawan --}}
                        <td>
                            {{ $transaction->employee->name }}
                        </td>

                        {{-- nama tools --}}
                        <td>
                            {{ $item->toolkit->name }}
                        </td>

                        {{-- nomor seri --}}
                        <td>
                            {{ $item->serial->serial_number }}
                        </td>

                        {{-- aksi --}}
                        <td>
                            @if (! $transaction->is_confirm)
                                {{-- tombol confirm --}}
                                <form action="{{ route('peminjaman.confirm', $transaction->id) }}"
                                      method="POST"
                                      style="display:inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success">
                                        Confirm
                                    </button>
                                </form>
                            @else
                                {{-- tombol pengembalian --}}
                                <form action="{{ route('peminjaman.return', $transaction->id) }}"
                                      method="POST"
                                      style="display:inline">
                                    @csrf
                                    <button class="btn btn-sm btn-warning">
                                        Pengembalian
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="5" class="text-center">
                        Tidak ada data peminjaman
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection