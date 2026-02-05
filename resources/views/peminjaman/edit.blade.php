@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Edit Peminjaman Tools</h4>

    {{-- ERROR --}}
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- FORM UPDATE --}}
    <form action="{{ route('peminjaman.update', $transaction->id) }}"
          method="POST">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Nama Peminjam</label>
                <input type="text"
                       name="borrower_name"
                       class="form-control"
                       value="{{ $transaction->borrower_name }}"
                       required>
            </div>

            <div class="col-md-6">
                <label>Tanggal</label>
                <input type="date"
                       name="date"
                       class="form-control"
                       value="{{ $transaction->date->format('Y-m-d') }}"
                       required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Nama Client</label>
                <input type="text"
                       name="client_name"
                       class="form-control"
                       value="{{ $transaction->client_name }}">
            </div>

            <div class="col-md-4">
                <label>Proyek</label>
                <input type="text"
                       name="project"
                       class="form-control"
                       value="{{ $transaction->project }}">
            </div>

            <div class="col-md-4">
                <label>Keperluan</label>
                <input type="text"
                       name="purpose"
                       class="form-control"
                       value="{{ $transaction->purpose }}">
            </div>
        </div>

        <hr>

        {{-- DAFTAR TOOLS --}}
        <h5>Tools yang Dipinjam</h5>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Tools</th>
                    <th>No Seri</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->toolkit->toolkit_name }}</td>
                    <td>{{ $item->serial->serial_number }}</td>
                    <td>
                        {{-- HAPUS ITEM --}}
                        <form action="{{ route('peminjaman.item.destroy', $item->id) }}"
                              method="POST"
                              onsubmit="return confirm('Hapus item ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- BUTTON --}}
        <div class="mt-3">
            <button class="btn btn-primary">
                Simpan Perubahan
            </button>

            <a href="{{ route('peminjaman.index') }}"
               class="btn btn-secondary">
                Kembali
            </a>
        </div>
    </form>
</div>
@endsection
