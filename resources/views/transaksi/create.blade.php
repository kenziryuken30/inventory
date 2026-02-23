@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h4 class="mb-3">Permintaan Consumable</h4>

    <form action="{{ route('transaksi.store') }}" method="POST">
        @csrf

        {{-- HEADER FORM --}}
        <div class="card mb-3">
            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Karyawan</label>
                        <input type="text" name="borrower_name" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="date" class="form-control"
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Client</label>
                        <input type="text" name="client" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Proyek</label>
                        <input type="text" name="project" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Keperluan</label>
                        <input type="text" name="purpose" class="form-control">
                    </div>
                </div>

            </div>
        </div>

        {{-- DAFTAR CONSUMABLE --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Daftar Consumable</strong>
                <button type="button" class="btn btn-outline-primary btn-sm"
                        data-bs-toggle="modal" data-bs-target="#modalConsumable">
                    + Pilih Consumable
                </button>
            </div>

            <div class="card-body">
                <table class="table table-bordered" id="tableConsumables">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Nama</th>
                            <th width="120">Qty</th>
                            <th width="100">Unit</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="text-end mt-3">
            <button type="submit" class="btn btn-primary">
                Save Transaksi
            </button>
        </div>

    </form>
</div>

{{-- MODAL PILIH CONSUMABLE --}}
<div class="modal fade" id="modalConsumable" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Pilih Consumable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Nama</th>
                            <th>Stok</th>
                            <th width="120">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($consumables as $c)
                        <tr>
                            <td>
                                <input type="checkbox"
                                       class="pick-consumable"
                                       data-id="{{ $c->id }}"
                                       data-name="{{ $c->name }}"
                                       data-unit="{{ $c->unit }}"
                                       data-stock="{{ $c->stock }}">
                            </td>
                            <td>{{ $c->name }}</td>
                            <td>{{ $c->stock }} {{ $c->unit }}</td>
                            <td>
                                <input type="number"
                                       class="form-control qty-input"
                                       min="1"
                                       max="{{ $c->stock }}"
                                       value="1">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary"
                        id="btnAddConsumable">+ Tambahkan</button>
            </div>

        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script>
let index = 0;
document.getElementById('btnAddConsumable').addEventListener('click', function () {

    document.querySelectorAll('.pick-consumable:checked').forEach((checkbox) => {

        const row   = checkbox.closest('tr');
        const id    = checkbox.dataset.id;
        const name  = checkbox.dataset.name;
        const unit  = checkbox.dataset.unit;
        const stock = parseInt(checkbox.dataset.stock);
        const qty   = parseInt(row.querySelector('.qty-input').value);

        // 🔥 VALIDASI STOCK
        if (qty > stock) {
            alert(`Stock ${name} hanya tersedia ${stock} ${unit}`);
            return;
        }

        if (qty <= 0) {
            alert("Qty tidak valid");
            return;
        }

        // cegah double
        if (document.querySelector(`tr[data-id="${id}"]`)) return;

        const html = `
            <tr data-id="${id}">
                <td>${index + 1}</td>
                <td>${name}</td>
                <td>${qty}</td>
                <td>${unit}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-danger"
                            onclick="this.closest('tr').remove()">
                        Hapus
                    </button>

                    <input type="hidden" name="items[${index}][consumable_id]" value="${id}">
                    <input type="hidden" name="items[${index}][qty]" value="${qty}">
                </td>
            </tr>
        `;

        document.querySelector('#tableConsumables tbody')
            .insertAdjacentHTML('beforeend', html);

        index++;
    });

    bootstrap.Modal.getInstance(
        document.getElementById('modalConsumable')
    ).hide();
});

</script>
@endsection
