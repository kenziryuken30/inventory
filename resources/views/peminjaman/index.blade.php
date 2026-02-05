@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>Peminjaman Tools</h4>
        <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
            + Pinjam Tools
        </a>
    </div>

    {{-- Flash message --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Tgl Peminjaman</th>
                <th>Nama Peminjam</th>
                <th>Nama Tools</th>
                <th>No Seri</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @php $no = 1; @endphp

            @forelse ($transactions as $transaction)
                @foreach ($transaction->items as $item)
                    <tr>
                        <td>{{ $no++ }}</td>

                        <td>
                            {{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}
                        </td>

                        <td>
                            {{ $transaction->borrower_name }}
                        </td>

                        <td>
                            {{ $item->toolkit->toolkit_name }}
                        </td>

                        <td>
                            {{ $item->serial->serial_number }}
                        </td>

                        <td>
                        @if (! $transaction->is_confirm)

                            {{-- EDIT --}}
                            <a href="{{ route('peminjaman.edit', $transaction->id) }}"
                            class="btn btn-sm btn-info">
                                Edit
                            </a>

                            {{-- HAPUS --}}
                            <form action="{{ route('peminjaman.destroy', $transaction->id) }}"
                                method="POST"
                                style="display:inline"
                                onsubmit="return confirm('Yakin hapus transaksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    Hapus
                                </button>
                            </form>

                            {{-- CONFIRM --}}
                            <form action="{{ route('peminjaman.confirm', $transaction->id) }}"
                                method="POST"
                                style="display:inline">
                                @csrf
                                <button class="btn btn-sm btn-success">
                                    Confirm
                                </button>
                            </form>

                        @else

                            {{-- PENGEMBALIAN --}}
                            @if ($item->status === 'DIPINJAM')
                                <form action="{{ route('peminjaman.return', $transaction->id) }}"
                                    method="POST"
                                    style="display:inline">
                                    @csrf
                                    <button class="btn btn-sm btn-warning">
                                        Pengembalian
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-success">
                                    confirmed
                                </span>
                            @endif

                        @endif
                    </td>

                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="6" class="text-center">
                        Tidak ada data peminjaman
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
