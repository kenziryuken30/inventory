@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h4 class="mb-3">Peminjaman Tools</h4>

    <form action="{{ route('peminjaman.store') }}" method="POST">
        @csrf

        {{-- HEADER FORM --}}
        <div class="card mb-3">
            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Peminjam</label>
                        <input type="text"
                               name="borrower_name"
                               class="form-control"
                               placeholder="Masukkan nama peminjam"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tanggal</label>
                        <input type="date"
                               name="date"
                               class="form-control"
                               value="{{ date('Y-m-d') }}"
                               required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Nama Client</label>
                        <input type="text"
                               name="client_name"
                               class="form-control"
                               placeholder="Masukkan nama client">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Proyek</label>
                        <input type="text"
                               name="project"
                               class="form-control"
                               placeholder="Masukkan keterangan">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Keperluan</label>
                        <input type="text"
                               name="purpose"
                               class="form-control"
                               placeholder="Masukkan keperluan">
                    </div>
                </div>

            </div>
        </div>

        {{-- DAFTAR ALAT --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Daftar Alat yang Dipinjam</strong>

                <button type="button"
                        class="btn btn-outline-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#modalTools">
                    + Pilih Tools
                </button>
            </div>

            <div class="card-body">
                <table class="table table-bordered" id="tableSelectedTools">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th width="100">Image</th>
                            <th>Nama Tool</th>
                            <th>No Seri</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="text-end mt-3">
            <button class="btn btn-primary">Save</button>
        </div>

    </form>
</div>

{{-- MODAL PILIH TOOLS --}}
<div class="modal fade" id="modalTools" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Pilih Tools Tersedia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Nama Tools</th>
                            <th>No Seri</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($serials as $serial)
                        <tr>
                            <td>
                                <input type="radio"
                                       name="pick_tool"
                                       class="tool-radio"
                                       data-id="{{ $serial->id }}"
                                       data-name="{{ $serial->toolkit->toolkit_name }}"
                                       data-serial="{{ $serial->serial_number }}">
                            </td>
                            <td>{{ $serial->toolkit->toolkit_name }}</td>
                            <td>{{ $serial->serial_number }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button"
                        class="btn btn-primary"
                        id="btnAddTool">
                    + Tambahkan
                </button>
            </div>

        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
let counter = 1;

document.getElementById('btnAddTool').addEventListener('click', function () {
    const selected = document.querySelector('.tool-radio:checked');
    if (!selected) return;

    const id = selected.dataset.id;
    const name = selected.dataset.name;
    const serial = selected.dataset.serial;

    if (document.getElementById('row-' + id)) return;

    const row = `
        <tr id="row-${id}">
            <td>${counter++}</td>
            <td></td>
            <td>${name}</td>
            <td>${serial}</td>
            <td>
                <button type="button"
                        class="btn btn-sm btn-outline-danger"
                        onclick="document.getElementById('row-${id}').remove()">
                    Hapus
                </button>
                <input type="hidden" name="serial_ids[]" value="${id}">
            </td>
        </tr>
    `;

    document.querySelector('#tableSelectedTools tbody')
        .insertAdjacentHTML('beforeend', row);

    bootstrap.Modal.getInstance(
        document.getElementById('modalTools')
    ).hide();
});
</script>
@endsection
