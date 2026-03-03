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
                            <input type="text" name="client_name" value="{{ $transaction->client_name }}"
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

                        <tbody>

                            @foreach($transaction->items as $i => $item)
                                <tr data-id="{{ $item->consumable_id }}" class="border-b hover:bg-gray-50">

                                    <td class="text-center py-3">{{ $loop->iteration }}</td>

                                    <td>{{ $item->consumable->name }}</td>

                                    <td class="text-center">
                                        <input type="number" min="1" value="{{ $item->qty }}" onchange="updateQty(this)"
                                            class="w-20 text-center border rounded-lg">
                                    </td>

                                    <td class="text-center">

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
                    <button class="bg-[#268397] hover:bg-[#1d6d7c]
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
                <div class="mb-5">
                    <input type="text" placeholder="Cari Nama Consumable"
                        class="w-full px-4 py-2 rounded-xl border shadow-sm focus:ring-2 focus:ring-[#4CCAE6]">
                </div>

                {{-- TABLE --}}
                <div class="rounded-xl border overflow-hidden">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-white bg-gradient-to-r from-[#268397] to-[#4CCAE6]">
                                <th class="py-3"></th>
                                <th>Nama Consumable</th>
                                <th>Jumlah</th>
                                <th>Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($consumables as $c)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="text-center">
                                        <input type="radio" name="pick" class="pick-consumable" data-id="{{ $c->id }}"
                                            data-name="{{ $c->name }}" data-stock="{{ $c->stock }}">
                                    </td>

                                    <td>{{ $c->name }}</td>

                                    <td class="text-center">
                                        <input type="number" min="1" max="{{ $c->stock }}" value="1"
                                            class="w-16 border rounded text-center qty-input">
                                    </td>

                                    <td class="text-center">
                                        <img src="{{ asset('storage/' . $c->photo) }}" class="w-10 h-10 object-cover rounded">
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
            document.addEventListener('DOMContentLoaded', function () {

                let index = {{ $transaction->items->count() }};

                /* ================= UPDATE QTY ================= */
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

                /* ================= REMOVE ================= */
                window.removeRow = function (btn) {
                    btn.closest('tr').remove();
                    refreshNo();
                };

                function refreshNo() {
                    document.querySelectorAll('#tableConsumables tbody tr')
                        .forEach((row, i) => row.children[0].innerText = i + 1);
                }

                /* ================= ADD CONSUMABLE ================= */
                document.getElementById('btnAddConsumable')
                    .addEventListener('click', function () {

                        document.querySelectorAll('.pick-consumable')
                            .forEach(check => {

                                if (!check.checked) return;

                                const row = check.closest('tr');

                                const id = check.dataset.id;
                                const name = check.dataset.name;
                                const unit = check.dataset.unit;
                                const stock = parseInt(check.dataset.stock);
                                const qty = parseInt(row.querySelector('.qty-input').value);

                                const exist = document.querySelector(
                                    `#tableConsumables tr[data-id="${id}"]`
                                );

                                if (qty > stock) {
                                    alert(`Stock ${name} hanya ${stock} ${unit}`);
                                    return;
                                }

                                if (qty <= 0) {
                                    alert("Qty tidak valid");
                                    return;
                                }

                                /* UPDATE */
                                if (exist) {
                                    exist.querySelector('input[type="number"]').value = qty;
                                    exist.querySelector('.hidden-qty').value = qty;
                                }
                                /* INSERT */
                                else {

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