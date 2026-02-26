@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6"
     x-data="{ openConsumable: false }"
     @close-modal.window="openConsumable = false">

    <h2 class="text-xl font-semibold mb-4">Permintaan Consumable</h2>

    <form action="{{ route('transaksi.store') }}" method="POST">
        @csrf

        {{-- ================= HEADER ================= --}}
        <div class="bg-white shadow rounded-xl p-6 mb-6">

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Karyawan</label>
                    <input type="text" name="borrower_name" required
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <input type="text" name="client" class="border rounded-lg px-3 py-2" placeholder="Client">
                <input type="text" name="project" class="border rounded-lg px-3 py-2" placeholder="Proyek">
                <input type="text" name="purpose" class="border rounded-lg px-3 py-2" placeholder="Keperluan">
            </div>

        </div>


        {{-- ================= DAFTAR ================= --}}
        <div class="bg-white shadow rounded-xl p-6">

            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold">Daftar Consumable</h3>

                <button type="button"
                        @click="openConsumable = true"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    + Pilih Consumable
                </button>
            </div>

            <table class="w-full text-sm border rounded-lg overflow-hidden" id="tableConsumables">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2">No</th>
                        <th class="p-2">Nama</th>
                        <th class="p-2">Qty</th>
                        <th class="p-2">Unit</th>
                        <th class="p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

        </div>

        <div class="text-right mt-6">
            <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Save Transaksi
            </button>
        </div>

    </form>


    {{-- ================= MODAL ================= --}}
    <div x-show="openConsumable"
         x-transition
         @click.self="openConsumable = false"
         @keydown.escape.window="openConsumable = false"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

        <div class="bg-white w-3/4 rounded-xl shadow-xl p-6 max-h-[80vh] overflow-y-auto">

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Pilih Consumable</h3>
                <button @click="openConsumable = false"
                        class="text-2xl text-gray-500">&times;</button>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th></th>
                        <th>Nama</th>
                        <th>Stok</th>
                        <th>Qty</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($consumables as $c)
                    <tr class="border-b">
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
                                   class="border rounded px-2 py-1 w-20 qty-input"
                                   min="1"
                                   max="{{ $c->stock }}"
                                   value="1">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-end gap-3 mt-4">
                <button @click="openConsumable = false"
                        class="px-4 py-2 border rounded-lg">
                    Batal
                </button>

                <button type="button"
                        id="btnAddConsumable"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                    + Tambahkan
                </button>
            </div>

        </div>
    </div>

</div>


{{-- ================= SCRIPT ================= --}}
<script>
let index = 0;

document.addEventListener('DOMContentLoaded', function(){

document.getElementById('btnAddConsumable').addEventListener('click', function () {

    let added = false;

    document.querySelectorAll('.pick-consumable:checked').forEach((checkbox) => {

        const row = checkbox.closest('tr');
        const id = checkbox.dataset.id;
        const name = checkbox.dataset.name;
        const unit = checkbox.dataset.unit;
        const stock = parseInt(checkbox.dataset.stock);
        const qty = parseInt(row.querySelector('.qty-input').value);

        if (qty > stock) {
            alert(`Stock ${name} hanya tersedia ${stock} ${unit}`);
            return;
        }

        if (qty <= 0) {
            alert("Qty tidak valid");
            return;
        }

        if (document.querySelector(`tr[data-id="${id}"]`)) return;

        const html = `
        <tr data-id="${id}" class="border-b">
            <td class="p-2">${index + 1}</td>
            <td class="p-2">${name}</td>
            <td class="p-2">${qty}</td>
            <td class="p-2">${unit}</td>
            <td class="p-2">
                <button type="button"
                        class="text-red-600"
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
        added = true;
    });

    document.querySelectorAll('.pick-consumable').forEach(c => c.checked = false);

    if (added) {
        window.dispatchEvent(new Event('close-modal'));
    }
});
});
</script>

@endsection