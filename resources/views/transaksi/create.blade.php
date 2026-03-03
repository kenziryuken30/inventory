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


        <form action="{{ route('transaksi.store') }}" method="POST">
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
                            <input type="text" name="client_name" placeholder="Masukkan nama client"
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
                    <table class="w-full" id="tableConsumables">
                        <thead>
                            <tr class="text-white text-sm
                                            bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)]">
                                <th class="py-3">NO</th>
                                <th>Nama Consumable</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>


                {{-- BUTTON --}}
                <div class="flex justify-end mt-6">
                    <button class="bg-gray-300 hover:bg-gray-400
                    text-sm px-5 py-2 rounded-md shadow">
                        Save
                    </button>
                </div>

            </div>
        </form>


        {{-- ================= MODAL ================= --}}
        <div x-show="openModal" x-transition x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">

            <div @click.away="openModal = false" class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl p-6">

                {{-- HEADER --}}
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-700 text-lg">
                        Consumable Tersedia
                    </h3>

                    <button type="button" @click="openModal=false" class="text-gray-400 hover:text-gray-600 text-xl">
                        ✕
                    </button>
                </div>

                {{-- SEARCH --}}
                <div class="mb-5">
                    <div class="bg-gradient-to-r from-[#268397] to-[#4CCAE6]
                                p-3 rounded-xl shadow">
                        <input type="text" placeholder="Cari Nama Consumable" class="w-full px-4 py-2 rounded-lg border
                                   focus:outline-none text-sm">
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="rounded-xl border overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-white text-sm
                                bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)]">
                                <th class="py-3 w-12"></th>
                                <th>Foto</th>
                                <th>Nama Consumable</th>
                                <th class="text-center">Jumlah</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($consumables as $c)
                                <tr class="border-b hover:bg-gray-50">

                                    {{-- RADIO --}}
                                    <td class="text-center">
                                        <input type="radio" class="pick-consumable" data-id="{{ $c->id }}"
                                            data-name="{{ $c->name }}" data-stock="{{ $c->stock }}">
                                    </td>

                                    {{-- FOTO --}}
                                    <td class="py-2">
                                        <img src="{{ asset('storage/' . $c->image) }}"
                                            class="w-12 h-12 object-cover rounded-lg border shadow-sm">
                                    </td>

                                    {{-- NAMA --}}
                                    <td>{{ $c->name }}</td>

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

                    <button type="button" id="btnAddConsumable" @click="openModal = false" class="px-4 py-2 rounded-lg bg-[#268397]
               text-white shadow hover:bg-[#1e6c7a] text-sm">
                        + Tambahkan
                    </button>
                </div>

            </div>
        </div>


        <script>
            document.addEventListener('DOMContentLoaded', function () {

                let index = 0;

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

                window.removeRow = function (btn) {
                    btn.closest('tr').remove();
                    refreshNo();
                };

                function refreshNo() {
                    document.querySelectorAll('#tableConsumables tbody tr')
                        .forEach((row, i) => row.children[0].innerText = i + 1);
                }

                document.getElementById('btnAddConsumable')
                    .addEventListener('click', function () {

                        document.querySelectorAll('.pick-consumable')
                            .forEach(check => {

                                if (!check.checked) return;

                                const row = check.closest('tr');
                                const id = check.dataset.id;
                                const name = check.dataset.name;
                                const stock = parseInt(check.dataset.stock);
                                const qty = parseInt(row.querySelector('.qty-input').value);

                                const exist = document.querySelector(
                                    `#tableConsumables tr[data-id="${id}"]`
                                );

                                if (qty > stock) {
                                    alert(`Stock ${name} hanya ${stock}`);
                                    return;
                                }

                                if (qty <= 0) {
                                    alert("Qty tidak valid");
                                    return;
                                }

                                if (exist) {
                                    exist.querySelector('input[type="number"]').value = qty;
                                    exist.querySelector('.hidden-qty').value = qty;
                                } else {

                                    const html = `
                                                                    <tr data-id="${id}" class="border-b hover:bg-gray-50">
                                                                        <td class="text-center py-3"></td>
                                                                        <td>${name}</td>
                                                                        <td class="text-center">
                                                                            <input type="number"
                                                                                value="${qty}"
                                                                                min="1"
                                                                                onchange="updateQty(this)"
                                                                                class="w-20 text-center border rounded-lg">
                                                                        </td>
                                                                        <td class="text-center">
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
                            });

                        refreshNo();
                    });

            });
        </script>

@endsection