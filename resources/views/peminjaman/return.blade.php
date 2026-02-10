@extends('layouts.app')

@section('content')
<div class="container">

    <h4 class="mb-3">Proses Pengembalian Tools</h4>

    {{-- Flash message --}}
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body">

            <p>
                <strong>Nama Peminjam:</strong>
                {{ $transaction->borrower_name }}
            </p>

            <p>
                <strong>Tanggal Peminjaman:</strong>
                {{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}
            </p>

            <hr>

            <h6>Daftar Tools yang Dikembalikan</h6>

            <table class="table table-bordered mt-2">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Tools</th>
                        <th>No Seri</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaction->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->toolkit->toolkit_name }}</td>
                            <td>{{ $item->serial->serial_number }}</td>
                            <td>
                                <span class="badge bg-warning">
                                    Dipinjam
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <form action="{{ route('peminjaman.return.process', $transaction->id) }}"
                  method="POST"
                  onsubmit="return confirm('Yakin ingin memproses pengembalian?')">
                @csrf

                <div class="mt-3">
                    <a href="{{ route('peminjaman.index') }}"
                       class="btn btn-secondary">
                        Batal
                    </a>

                    <button type="submit"
                            class="btn btn-success">
                        Proses Pengembalian
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
