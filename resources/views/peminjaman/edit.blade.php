@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Edit Peminjaman Tools</h4>

    {{-- FLASH MESSAGE --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- ================= FORM UPDATE TRANSAKSI ================= --}}
    <form action="{{ route('peminjaman.update', $transaction->id) }}" method="POST">
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

        <button type="submit" class="btn btn-primary">
            Simpan Perubahan
        </button>

        <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">
            Kembali
        </a>
    </form>

    <hr>

    {{-- ================= DAFTAR ITEM ================= --}}
    <div class="d-flex justify-content-between mb-2">
        <strong>Daftar Alat yang Dipinjam</strong>
        <button type="button"
                class="btn btn-outline-primary btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalTools">
            + Pilih Tools
        </button>
    </div>

    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Image</th>
                <th>Nama Tools</th>
                <th>No Seri</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaction->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if ($item->toolkit->image)
                            <img src="{{ asset('storage/'.$item->toolkit->image) }}"
                                 width="60"
                                 class="rounded border">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->toolkit->toolkit_name }}</td>
                    <td>{{ $item->serial->serial_number }}</td>
                    <td>
                        <form action="{{ route('peminjaman.item.destroy', $item->id) }}"
                              method="POST"
                              onsubmit="return confirm('Hapus item ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        Belum ada barang yang ditambahkan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ================= MODAL TAMBAH ITEM ================= --}}
<div class="modal fade" id="modalTools" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <form action="{{ route('peminjaman.item.add', $transaction->id) }}"
                  method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Pilih Tools Tersedia</h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <table class="table table-bordered text-center align-middle">
                        <thead>
                            <tr>
                                <th>Pilih</th>
                                <th>Image</th>
                                <th>Nama Tools</th>
                                <th>No Seri</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($serials as $serial)
                                <tr>
                                    <td>
                                        <input type="checkbox"
                                               name="serial_ids[]"
                                               value="{{ $serial->id }}">
                                    </td>
                                    <td>
                                        @if ($serial->toolkit->image)
                                            <img src="{{ asset('storage/'.$serial->toolkit->image) }}"
                                                class="preview-image"
                                                style="width: 60px;height:60px;object-fit:contain;cursor:pointer;">
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $serial->toolkit->toolkit_name }}</td>
                                    <td>{{ $serial->serial_number }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted">
                                        Tidak ada tools tersedia
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        Tambahkan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<div id="imagePreviewModal"
     class="fixed inset-0 bg-black/70 hidden z-50 flex items-center justify-center">

    <div class="relative">
        <button id="closePreview"
                class="absolute -top-8 right-0 text-white text-2xl">
            ✕
        </button>

        <img id="previewImage"
             src=""
             class="max-h-[90vh] max-w-[90vw] rounded shadow-lg">
    </div>

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modalPreview = document.getElementById('imagePreviewModal');
    const previewImage = document.getElementById('previewImage');
    const closeBtn = document.getElementById('closePreview');

    // Buka preview
    document.querySelectorAll('.preview-image').forEach(function (img) {
        img.addEventListener('click', function () {
            previewImage.src = this.src;
            modalPreview.classList.remove('hidden');
        });
    });

    // Tutup preview
    closeBtn.addEventListener('click', closeModal);

    modalPreview.addEventListener('click', function (e) {
        if (e.target === modalPreview) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === "Escape") {
            closeModal();
        }
    });

    function closeModal() {
        modalPreview.classList.add('hidden');
        previewImage.src = "";
    }
});
</script>
@endsection
