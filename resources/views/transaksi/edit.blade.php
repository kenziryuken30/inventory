@extends('layouts.app')
@section('content')

    <div class="max-w-7xl mx-auto" x-data="{ openModal:false }">

        {{-- TITLE --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-[#268397]">Edit Consumable</h2>
                <p class="text-gray-500 text-sm">Pilih Consumable yang akan di Edit</p>
            </div>

            <a href="{{ route('transaksi.index') }}"
                class="bg-gray-200 hover:bg-gray-300 text-sm px-4 py-2 rounded-lg shadow">
                ← Kembali
            </a>
        </div>


        <form action="{{ route('transaksi.update', $transaction->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- CARD --}}
            <div class="bg-white rounded-2xl shadow-xl p-6">

                {{-- HEADER FORM --}}
                <div class="space-y-5 mb-6">

                    <div class="grid md:grid-cols-3 gap-6 items-end">

                        <div class="md:col-span-2">
                            <label class="text-sm text-gray-600">Karyawan</label>
                            <input type="text" name="borrower_name" value="{{ $transaction->borrower_name }}"
                                class="w-full mt-1 px-4 py-2 rounded-lg border shadow-sm">
                        </div>

                        <div>
                            <label class="text-sm text-gray-600">Tanggal</label>
                            <input type="date" name="date" value="{{ $transaction->date->format('Y-m-d') }}"
                                class="w-full mt-1 px-4 py-2 rounded-lg border shadow-sm">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6">

                        <div>
                            <label class="text-sm text-gray-600">Nama Client</label>
                            <input type="text" name="client" value="{{ $transaction->client }}"
                                placeholder="Masukan nama klien" class="w-full mt-1 px-4 py-2 rounded-lg border shadow-sm">
                        </div>

                        <div>
                            <label class="text-sm text-gray-600">Proyek</label>
                            <input type="text" name="project" value="{{ $transaction->project }}"
                                placeholder="Masukan Keterangan" class="w-full mt-1 px-4 py-2 rounded-lg border shadow-sm">
                        </div>

                        <div>
                            <label class="text-sm text-gray-600">Keperluan</label>
                            <input type="text" name="purpose" value="{{ $transaction->purpose }}"
                                placeholder="Masukan Keperluan" class="w-full mt-1 px-4 py-2 rounded-lg border shadow-sm">
                        </div>

                    </div>

                </div>


                {{-- LIST HEADER --}}
                <div class="flex justify-between items-center mb-3">
                    <h4 class="font-semibold text-gray-700">
                        Daftar Consumable yang akan di Edit
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
                            <tr class="text-white text-sm
                                bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)]">

                                <th class="py-3 w-12"></th>
                                <th>Foto</th>
                                <th>Nama Consumable</th>
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($transaction->items as $i => $item)
                                <tr data-id="{{ $item->consumable_id }}" class="border-b hover:bg-gray-50 align-middle">

                                    <td class="py-3 text-center no font-medium">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="py-2 text-center">
                                        <img src="{{ asset('storage/' . $item->consumable->image) }}"
                                            class="w-14 h-14 object-cover rounded-xl border shadow-sm mx-auto">
                                    </td>

                                    <td class="py-2">
                                        <span class="font-medium block">
                                            {{ $item->consumable->name }}
                                        </span>
                                    </td>

                                    <td class="py-2 text-center">
                                        <input type="number" value="{{ $item->qty }}" min="1" onchange="updateQty(this)"
                                            class="w-20 h-9 text-center border rounded-lg qty-input-main">
                                    </td>

                                    <td class="py-2 text-center">

                                        <button type="button" onclick="removeRow(this)"
                                            class="bg-red-100 text-red-600 px-3 py-1 rounded-lg text-sm hover:bg-red-200">
                                            Hapus
                                        </button>

                                        <input type="hidden" name="items[{{ $i }}][consumable_id]"
                                            value="{{ $item->consumable_id }}">

                                        <input type="hidden" name="items[{{ $i }}][qty]" value="{{ $item->qty }}"
                                            class="hidden-qty">
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


                {{-- BUTTON --}}
                <div class="flex justify-end mt-6">
                    <button
                        class="bg-[#268397] hover:bg-[#1d6d7c]
                                                                                    text-white px-6 py-2 rounded-lg shadow">
                        Save
                    </button>
                </div>

            </div>
        </form>


        {{-- ================= MODAL ================= --}}
        <div x-show="openModal" x-transition x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">

            <div @click.away="openModal = false" class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl p-6">

                {{-- HEADER --}}
                <div class="flex justify-between items-center mb-5">
                    <h3 class="font-semibold text-gray-700 text-lg">
                        Consumable Tersedia
                    </h3>

                    <button type="button" @click="openModal=false" class="text-gray-400 hover:text-gray-600 text-xl">
                        ✕
                    </button>
                </div>

                {{-- SEARCH --}}
                <input type="text" id="searchConsumable" placeholder="Cari Nama Consumable"
                    class="w-full px-4 py-2 rounded-xl border shadow-sm focus:ring-2 focus:ring-[#4CCAE6]">

                {{-- TABLE --}}
                <div class="rounded-xl border overflow-hidden">
                    <table class="w-full text-sm" id="popupTable">
                        <thead>
                            <tr class="text-white bg-gradient-to-r from-[#268397] to-[#4CCAE6]">
                                <th class="py-3"></th>
                                <th>Foto</th>
                                <th>Nama Consumable</th>
                                <th>Stok </th>
                                <th>Jumlah</th>
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

                {{-- FOOTER --}}
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="openModal=false"
                        class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 shadow">
                        Batal
                    </button>

                    <button type="button" id="btnAddConsumable" @click="openModal=false"
                        class="px-4 py-2 rounded-lg bg-[#268397] text-white shadow hover:bg-[#1e6c7a]">
                        + Tambahkan
                    </button>
                </div>

            </div>
        </div>

        <script>

            let index = {{ $transaction->items->count() }};

            /* ================= UPDATE QTY ================= */
            function updateQty(input) {
                const row = input.closest('tr');
                const qty = parseInt(input.value);

                if (qty <= 0 || isNaN(qty)) {
                    alert("Qty tidak valid");
                    input.value = 1;
                    return;
                }

                row.querySelector('.hidden-qty').value = qty;
            }

            /* ================= REMOVE ================= */
            function removeRow(btn) {

                const row = btn.closest('tr');
                const id = row.dataset.id;

                row.remove();

                const popupCheckbox = document.querySelector(
                    `.pick-consumable[data-id="${id}"]`
                );

                if (popupCheckbox) {
                    popupCheckbox.checked = false;
                }

                refreshNo();
            }

            function refreshNo() {
                document.querySelectorAll('#tableConsumables tbody tr')
                    .forEach((row, i) => row.children[0].innerText = i + 1);
            }

            document.addEventListener('DOMContentLoaded', function () {
                
                /* ================= SEARCH ================= */
                document.getElementById("searchConsumable")
                    .addEventListener("keyup", function () {

                        let keyword = this.value.toLowerCase();
                        let rows = document.querySelectorAll("#popupTable tbody tr");

                        rows.forEach(row => {

                            let name = row.children[2].innerText.toLowerCase();

                            if (name.includes(keyword)) {
                                row.style.display = "";
                            } else {
                                row.style.display = "none";
                            }

                        });

                    });

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

@endsection