@extends('layouts.app')
@section('content')

    <div class="max-w-6xl mx-auto mt-8" x-data="{ openModal:false }">

        {{-- TITLE --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-[#268397]">
                Permintaan Consumable
            </h2>

            <a href="{{ route('transaksi.index') }}"
                class="bg-gray-200 hover:bg-gray-300 text-sm px-4 py-2 rounded-md shadow-sm">
                ← Kembali
            </a>
        </div>


        <form id="formTransaksi" action="{{ route('transaksi.store') }}" method="POST">
            @csrf

            {{-- CARD --}}
            <div class="bg-gray-100 rounded-2xl shadow-xl p-6 border">

                {{-- HEADER FORM --}}
                <div class="space-y-5 mb-6">

                    <div class="grid md:grid-cols-3 gap-6 items-end">

                        <div class="md:col-span-2">
                            <label class="text-sm text-gray-600">Karyawan</label>
                            <input type="text" name="borrower_name" placeholder="Masukkan nama karyawan"
                                class="w-full mt-1 px-4 py-2 rounded-lg border bg-white shadow-sm">
                        </div>

                        <div>
                            <label class="text-sm text-gray-600">Tanggal</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}"
                                class="w-full mt-1 px-4 py-2 rounded-lg border shadow-sm">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6">

                        <div>
                            <label class="text-sm text-gray-600">Nama Client</label>
                            <input type="text" name="client" placeholder="Masukkan nama client"
                                class="w-full mt-1 px-4 py-2 rounded-lg border shadow-sm">
                        </div>

                        <div>
                            <label class="text-sm text-gray-600">Proyek</label>
                            <input type="text" name="project" placeholder="Masukkan nama proyek"
                                class="w-full mt-1 px-4 py-2 rounded-lg border shadow-sm">
                        </div>

                        <div>
                            <label class="text-sm text-gray-600">Keperluan</label>
                            <input type="text" name="purpose" placeholder="Masukkan keperluan"
                                class="w-full mt-1 px-4 py-2 rounded-lg border shadow-sm">
                        </div>

                    </div>

                </div>


                {{-- LIST HEADER --}}
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-semibold text-gray-700">
                        Daftar Consumable
                    </h4>

                    <button type="button" @click.stop="openModal = true"
                        class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg shadow text-sm">
                        + Pilih Consumable
                    </button>
                </div>


                {{-- TABLE --}}
                <div class="rounded-xl overflow-hidden border">
                    <table class="w-full table-fixed text-sm" id="tableConsumables">
                        <thead>
                            <tr class="text-white text-sm text-center
                                                                bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)]">

                                <th class="w-16 py-3">No</th>
                                <th class="w-24">Foto</th>
                                <th>Nama Consumable</th>
                                <th class="w-32">Jumlah</th>
                                <th class="w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>


                {{-- BUTTON --}}
                <div class="flex justify-end mt-6">
                    <button type="button" id="btnSave" class="bg-gray-300 hover:bg-gray-400
                                                    text-sm px-5 py-2 rounded-md shadow">
                        Save
                    </button>
                </div>

            </div>
        </form>


        {{-- ================= MODAL ================= --}}
        <div x-show="openModal" x-transition.opacity x-cloak
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40">

            <div @click.away="openModal = false" class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl p-6
                            max-h-[90vh] overflow-y-auto">

                {{-- HEADER --}}
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-700 text-lg">
                        Consumable Tersedia
                    </h3>
                    <button type="button" @click="openModal=false"
                        class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
                </div>

                {{-- SEARCH --}}
                <div class="mb-5">
                    <input type="text" id="searchConsumable" placeholder="Cari Nama Consumable"
                        class="w-full px-4 py-2 rounded-lg border focus:outline-none text-sm">
                </div>

                {{-- TABLE --}}
                <div class="rounded-xl border overflow-hidden">
                    <table id="popupTable" class="w-full text-sm">
                        <thead>
                            <tr class="text-white text-sm
                                    bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)]">
                                <th class="py-3 w-12"></th>
                                <th>Foto</th>
                                <th>Nama Consumable</th>
                                <th>Stock</th>
                                <th class="text-center">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($consumables as $c)
                                <tr class="border-b hover:bg-gray-50">

                                    {{-- RADIO --}}
                                    <td class="text-center">
                                        <input type="checkbox" class="pick-consumable" data-id="{{ $c->id }}"
                                            data-name="{{ $c->name }}" data-stock="{{ $c->stock }}">
                                    </td>

                                    {{-- FOTO --}}
                                    <td class="py-2">
                                        <img src="{{ asset('storage/' . $c->image) }}"
                                            class="w-12 h-12 object-cover rounded-lg border shadow-sm">
                                    </td>

                                    {{-- NAMA --}}
                                    <td class="py-2">
                                        <div class="flex items-center justify-start h-full px-4">
                                            <span class="font-medium">{{ $c->name }}</span>
                                        </div>
                                    </td>

                                    {{-- STOCK --}}
                                    <td
                                        class="text-center font-semibold
                                                                            {{ $c->stock <= $c->minimum_stock ? 'text-red-500' : 'text-blue-600' }}">
                                        {{ $c->stock }}
                                        @if($c->stock <= $c->minimum_stock)
                                            <div class="text-xs text-red-400">
                                                Minimum: {{ $c->minimum_stock }}
                                            </div>
                                        @endif
                                    </td>

                                    {{-- QTY --}}
                                    <td class="text-center">
                                        <input type="number" min="1" max="{{ $c->stock }}" value="1"
                                            class="w-16 border rounded text-center qty-input">
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- BUTTON --}}
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="openModal=false"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 shadow text-sm">
                        Batal
                    </button>

                    <button type="button" id="btnAddConsumable" @click="openModal = false"
                        class="px-4 py-2 rounded-lg bg-[#268397] text-white shadow hover:bg-[#1e6c7a] text-sm">
                        + Tambahkan
                    </button>
                </div>

            </div>
        </div>


        <script>
            document.getElementById('btnSave').addEventListener('click', function () {

                const form = document.getElementById('formTransaksi');

                const fields = [
                    form.querySelector('input[name="borrower_name"]'),
                    form.querySelector('input[name="client"]'),
                    form.querySelector('input[name="project"]'),
                    form.querySelector('input[name="purpose"]')
                ];

                let firstInvalid = null;

                fields.forEach(field => {

                    field.classList.remove('border-red-500');

                    if (!field.value.trim()) {
                        field.classList.add('border-red-500');
                        if (!firstInvalid) firstInvalid = field;
                    }
                });

                const items = document.querySelectorAll('#tableConsumables tbody tr');

                if (items.length === 0) {
                    alert("Pilih minimal 1 consumable dulu 🔥");
                    return;
                }

                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: "smooth", block: "center" });
                    return;
                }

                form.submit();
            });

            document.querySelectorAll('#formTransaksi input').forEach(input => {
                input.addEventListener('input', function () {
                    this.classList.remove('border-red-500');
                });
            });

            //search popup
            document.getElementById('searchConsumable')
                .addEventListener('keyup', function () {

                    const keyword = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#popupTable tbody tr');

                    rows.forEach(row => {

                        const nameCell = row.children[2]; // kolom nama
                        const nameText = nameCell.innerText.toLowerCase();

                        if (nameText.includes(keyword)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }

                    });
                });

            document.addEventListener('DOMContentLoaded', function () {

                let index = 0;

                function refreshNo() {
                    document.querySelectorAll('#tableConsumables tbody tr')
                        .forEach((row, i) => {
                            row.querySelector('.no').innerText = i + 1;
                        });
                }

                window.updateQty = function (input) {
                    const row = input.closest('tr');
                    const qty = parseInt(input.value);

                    if (qty <= 0 || isNaN(qty)) {
                        alert("Qty tidak valid");
                        input.value = 1;
                        row.querySelector('.hidden-qty').value = 1;
                        return;
                    }

                    row.querySelector('.hidden-qty').value = qty;
                };

                window.removeRow = function (btn) {
                    btn.closest('tr').remove();
                    refreshNo();
                };

                document.getElementById('btnAddConsumable')
                    .addEventListener('click', function () {

                        const selectedItems = document.querySelectorAll('.pick-consumable:checked');

                        if (selectedItems.length === 0) {
                            alert("Pilih minimal 1 consumable");
                            return;
                        }

                        selectedItems.forEach(selected => {

                            const row = selected.closest('tr');
                            const id = selected.dataset.id;
                            const name = selected.dataset.name;
                            const stock = parseInt(selected.dataset.stock);
                            const image = row.querySelector('img').src;
                            const qty = parseInt(row.querySelector('.qty-input').value);

                            if (qty > stock) {
                                alert(`Stock ${name} hanya ${stock}`);
                                return;
                            }

                            if (qty <= 0 || isNaN(qty)) {
                                alert("Qty tidak valid");
                                return;
                            }

                            const exist = document.querySelector(`#tableConsumables tr[data-id="${id}"]`);

                            if (exist) {
                                exist.querySelector('.qty-input-main').value = qty;
                                exist.querySelector('.hidden-qty').value = qty;
                            } else {

                                const html = `
                                                    <tr data-id="${id}" class="border-b hover:bg-gray-50 align-middle">

                                                        <td class="py-3 text-center no font-medium"></td>

                                                        <td class="py-2 text-center">
                                                            <img src="${image}"
                                                                class="w-14 h-14 object-cover rounded-xl border shadow-sm mx-auto">
                                                        </td>

                                                        <td class="py-2">
                                                            <span class="font-medium block">${name}</span>
                                                        </td>

                                                        <td class="py-2 text-center">
                                                            <input type="number"
                                                                value="${qty}"
                                                                min="1"
                                                                onchange="updateQty(this)"
                                                                class="w-20 h-9 text-center border rounded-lg qty-input-main">
                                                        </td>

                                                        <td class="py-2 text-center">
                                                            <button type="button"
                                                                onclick="removeRow(this)"
                                                                class="bg-red-100 text-red-600 px-3 py-1 rounded-lg text-sm hover:bg-red-200">
                                                                Hapus
                                                            </button>

                                                            <input type="hidden"
                                                                name="items[${index}][consumable_id]"
                                                                value="${id}">

                                                            <input type="hidden"
                                                                name="items[${index}][qty]"
                                                                value="${qty}"
                                                                class="hidden-qty">
                                                        </td>

                                                    </tr>`;

                                document.querySelector('#tableConsumables tbody')
                                    .insertAdjacentHTML('beforeend', html);

                                index++;
                            }

                            selected.checked = false;
                        });

                        refreshNo();
                    });
            });
        </script>

    </div>

@endsection