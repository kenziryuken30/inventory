@extends('layouts.app')
@section('content')

    <div class="container">

        <h4>Edit Transaksi Consumable</h4>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif


        {{-- ================= HEADER ================= --}}
        <form action="{{ route('transaksi.update', $transaction->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Nama Peminjam</label>
                    <input type="text" name="borrower_name" value="{{ $transaction->borrower_name }}" class="form-control"
                        required>
                </div>

                <div class="col-md-6">
                    <label>Tanggal</label>
                    <input type="date" name="date" value="{{ $transaction->date->format('Y-m-d') }}" class="form-control"
                        required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label>Client</label>
                    <input type="text" name="client" value="{{ $transaction->client }}" class="form-control">
                </div>

                <div class="col-md-4">
                    <label>Project</label>
                    <input type="text" name="project" value="{{ $transaction->project }}" class="form-control">
                </div>

                <div class="col-md-4">
                    <label>Keperluan</label>
                    <input type="text" name="purpose" value="{{ $transaction->purpose }}" class="form-control">
                </div>
            </div>


            {{-- ================= LIST ================= --}}
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5>Daftar Consumable</h5>

                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalConsumable">
                    + Pilih Consumable
                </button>
            </div>

            <table class="table table-bordered" id="tableConsumables">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($transaction->items as $i => $item)
                        <tr data-id="{{ $item->consumable_id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->consumable->name }}</td>
                            <td>
                                <input type="number" class="form-control" min="1" value="{{ $item->qty }}"
                                    onchange="updateQty(this)">
                            </td>
                            <td>{{ $item->consumable->unit }}</td>
                            <td>

                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">
                                    Hapus
                                </button>

                                <input type="hidden" name="items[{{ $i }}][consumable_id]" value="{{ $item->consumable_id }}">

                                <input type="hidden" name="items[{{ $i }}][qty]" value="{{ $item->qty }}" class="hidden-qty">
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

            <button class="btn btn-success">Simpan</button>
            <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Kembali</a>

        </form>

    </div>



    {{-- ================= MODAL ================= --}}
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
                                        <input type="checkbox" class="pick-consumable" data-id="{{ $c->id }}"
                                            data-name="{{ $c->name }}" data-unit="{{ $c->unit }}" data-stock="{{ $c->stock }}">
                                    </td>
                                    <td>{{ $c->name }}</td>
                                    <td>{{ $c->stock }} {{ $c->unit }}</td>
                                    <td>
                                        <input type="number" class="form-control qty-input" min="1" max="{{ $c->stock }}"
                                            value="1">
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

                    <button type="button" class="btn btn-primary" id="btnAddConsumable">
                        + Tambahkan
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            window.updateQty = function (input) {

                const row = input.closest('tr');
                const qty = parseInt(input.value);

                if (qty <= 0 || isNaN(qty)) {
                    alert("Qty tidak valid");
                    input.value = 1;
                    return;
                }

                row.querySelector('.hidden-qty').value = qty;
            };
            let index = {{ $transaction->items->count() }};

            window.removeRow = function (btn) {
                btn.closest('tr').remove();
                refreshNo();
            };

            function refreshNo() {
                document.querySelectorAll('#tableConsumables tbody tr')
                    .forEach((row, i) => row.children[0].innerText = i + 1);
            }


            /* ================= SYNC MODAL ================= */
            function syncModal() {

                document.querySelectorAll('#modalConsumable tbody tr')
                    .forEach(row => {

                        const checkbox = row.querySelector('.pick-consumable');
                        const id = checkbox.dataset.id;

                        const exist = document.querySelector(
                            `#tableConsumables tr[data-id="${id}"]`
                        );

                        if (exist) {
                            checkbox.checked = true;

                            const qty = exist.querySelector('input[name*="[qty]"]').value;
                            row.querySelector('.qty-input').value = qty;
                        } else {
                            checkbox.checked = false;
                            row.querySelector('.qty-input').value = 1;
                        }
                    });
            }


            /* ================= OPEN MODAL ================= */
            document.querySelector('[data-bs-target="#modalConsumable"]')
                .addEventListener('click', syncModal);


            /* ================= ADD / UPDATE ================= */
            document.getElementById('btnAddConsumable')
                .addEventListener('click', function () {

                    document.querySelectorAll('#modalConsumable tbody tr')
                        .forEach(row => {

                            const check = row.querySelector('.pick-consumable');

                            const id = check.dataset.id;
                            const name = check.dataset.name;
                            const unit = check.dataset.unit;
                            const stock = parseInt(check.dataset.stock);
                            const qty = parseInt(row.querySelector('.qty-input').value);

                            const exist = document.querySelector(
                                `#tableConsumables tr[data-id="${id}"]`
                            );

                            /* ================= UNCHECK = DELETE ================= */
                            if (!check.checked) {
                                if (exist) exist.remove();
                                return;
                            }

                            if (qty > stock) {
                                alert(`Stock ${name} hanya ${stock} ${unit}`);
                                return;
                            }

                            if (qty <= 0) {
                                alert("Qty tidak valid");
                                return;
                            }

                            /* ================= UPDATE ================= */
                            if (exist) {

                                exist.children[2].innerText = qty;
                                exist.querySelector('input[name*="[qty]"]').value = qty;

                            } else {

                                const html = `
                            <tr data-id="${id}">
                                <td></td>
                                <td>${name}</td>
                                <td>${qty}</td>
                                <td>${unit}</td>
                                <td>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="removeRow(this)">Hapus</button>

                                    <input type="hidden"
                                        name="items[${index}][consumable_id]"
                                        value="${id}">

                                    <input type="hidden"
                                        name="items[${index}][qty]"
                                        value="${qty}">
                                </td>
                            </tr>`;

                                document.querySelector('#tableConsumables tbody')
                                    .insertAdjacentHTML('beforeend', html);

                                index++;
                            }

                        });

                    refreshNo();

                    bootstrap.Modal.getInstance(
                        document.getElementById('modalConsumable')
                    ).hide();

                });

        });
    </script>

@endsection