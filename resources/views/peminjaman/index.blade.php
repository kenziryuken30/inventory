@extends('layouts.app')

@section('content')
<div class="container">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between mb-3">
        <h4>Peminjaman Tools</h4>
        <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
            + Pinjam Tools
        </a>
    </div>

    {{-- FLASH MESSAGE --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- TABLE --}}
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Peminjam</th>
                <th>Tools</th>
                <th>No Seri</th>
                <th width="220">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp

            @forelse ($transactions as $transaction)
                @foreach ($transaction->items as $item)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}</td>
                    <td>{{ $transaction->borrower_name }}</td>
                    <td>{{ $item->toolkit->toolkit_name }}</td>
                    <td>{{ $item->serial->serial_number }}</td>

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
                                  class="d-inline"
                                  onsubmit="return confirm('Hapus transaksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    Hapus
                                </button>
                            </form>

                            {{-- CONFIRM --}}
                            <form action="{{ route('peminjaman.confirm', $transaction->id) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-success">
                                    Confirm
                                </button>
                            </form>

                        @else
                            {{-- PENGEMBALIAN (BUKA MODAL) --}}
                            <button type="button"
                                    class="btn btn-sm btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalReturn{{ $transaction->id }}">
                                Pengembalian
                            </button>

                            <span class="badge bg-success ms-1">
                                Confirmed
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Tidak ada data peminjaman
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

{{-- ================= MODAL PENGEMBALIAN ================= --}}
@foreach ($transactions as $transaction)
@if ($transaction->is_confirm)

<div class="modal fade" id="modalReturn{{ $transaction->id }}" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <form action="{{ route('peminjaman.return', $transaction->id) }}" method="POST">
        @csrf

        {{-- HEADER --}}
        <div class="modal-header">
          <h5 class="modal-title">Proses Pengembalian</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        {{-- BODY --}}
        <div class="modal-body">

          {{-- INFO --}}
          <div class="row mb-3">
            <div class="col-md-6">
              <label>Nama Karyawan</label>
              <input type="text"
                     class="form-control"
                     value="{{ $transaction->borrower_name }}"
                     readonly>
            </div>
            <div class="col-md-6">
              <label>Tanggal</label>
              <input type="date"
                     class="form-control"
                     value="{{ now()->format('Y-m-d') }}">
            </div>
          </div>

          {{-- TABLE ALAT --}}
          <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
              <tr>
                <th>Pilih</th>
                <th>Nama Alat</th>
                <th>No Seri</th>
                <th>Kondisi</th>
                <th>Keterangan</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($transaction->items as $item)
              <tr>
                <td>
                  <input type="checkbox"
                         name="items[{{ $item->id }}][id]"
                         value="{{ $item->id }}">
                </td>

                <td>{{ $item->toolkit->toolkit_name }}</td>
                <td>{{ $item->serial->serial_number }}</td>

                <td>
                  <select name="items[{{ $item->id }}][condition]"
                          class="form-select">
                    <option value="BAIK">Baik</option>
                    <option value="PERBAIKAN">Butuh Perbaikan</option>
                    <option value="RUSAK">Rusak</option>
                  </select>
                </td>

                <td>
                  <input type="text"
                         name="items[{{ $item->id }}][note]"
                         class="form-control"
                         placeholder="Keterangan">
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>

        </div>

        {{-- FOOTER --}}
        <div class="modal-footer">
          <button type="button"
                  class="btn btn-secondary"
                  data-bs-dismiss="modal">
            Batal
          </button>

          <button type="submit"
                  class="btn btn-primary"
                  onclick="return confirm('Proses pengembalian?')">
            Kembalikan Alat
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

@endif
@endforeach
@endsection
