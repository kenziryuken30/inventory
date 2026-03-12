@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen flex flex-col">

    {{-- ================= TITLE ================= --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-[#1CA7B6] tracking-tight">Edit Consumable</h2>
            <p class="text-sm text-gray-500 mt-1">Edit Proses Transaksi dan Daftar Consumable</p>
        </div>

        <a href="{{ route('transaksi.index') }}"
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition">
            ← Kembali
        </a>
    </div>

    {{-- ================= ALERT ================= --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- ================= PANEL BESAR ================= --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 space-y-8">

        {{-- PERBAIKAN: Form dibuka di awal agar membungkus SEMUA input termasuk tabel --}}
        <form id="updateForm"
              action="{{ route('transaksi.update', $transaction->id) }}"
              method="POST">
            @csrf
            @method('PUT')

            {{-- Grid Utama: 3 Kolom --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- Kolom 1: Karyawan --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Karyawan</label>
                    <input type="text"
                           name="borrower_name"
                           value="{{ $transaction->borrower_name }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                </div>

                {{-- Kolom 2: Tanggal --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal</label>
                    <input type="date"
                           name="date"
                           value="{{ $transaction->date->format('Y-m-d') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                </div>

                {{-- Kolom 3: Kosong (Spacer) --}}
                <div class="hidden md:block">
                    <label class="block text-sm font-bold text-gray-700 mb-2">&nbsp;</label>
                    <div class="w-full px-4 py-2.5">&nbsp;</div>
                </div>

                {{-- Kolom 1 (Baris 2): Nama Client --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Client</label>
                    <input type="text"
                           name="client"
                           value="{{ $transaction->client }}"
                           placeholder="Masukan nama klien"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                </div>

                {{-- Kolom 2 (Baris 2): Proyek --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Proyek</label>
                    <input type="text"
                           name="project"
                           value="{{ $transaction->project }}"
                           placeholder="Masukan Keterangan"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                </div>

                {{-- Kolom 3 (Baris 2): Keperluan --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Keperluan</label>
                    <input type="text"
                           name="purpose"
                           value="{{ $transaction->purpose }}"
                           placeholder="Masukan Keperluan"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                </div>

            </div>
            {{-- END Grid Header --}}


            {{-- ================= DAFTAR CONSUMABLE (SEKARANG DI DALAM FORM) ================= --}}
            <div class="space-y-4 mt-8">
                
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-gray-800 text-lg">Daftar Consumable yang Di Edit</h3>

                    <button type="button"
                            id="openConsumableBtn"
                            class="text-white px-5 py-2 rounded-xl font-bold shadow-md hover:opacity-90 transition-all duration-200 text-sm tracking-wide"
                            style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                        + Pilih Consumable
                    </button>
                </div>

                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                    <table class="w-full text-sm" id="tableConsumables">
                        <thead>
                            <tr class="text-white text-xs uppercase tracking-wider"
                                style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                                <th class="py-3 px-4 font-semibold text-center w-10">NO</th>
                                <th class="py-3 px-4 font-semibold text-center w-20">Foto</th>
                                <th class="py-3 px-4 font-semibold text-center">Nama Consumable</th>
                                <th class="py-3 px-4 font-semibold text-center w-32">Jumlah</th>
                                <th class="py-3 px-4 font-semibold text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($transaction->items as $i => $item)
                                <tr data-id="{{ $item->consumable_id }}" class="hover:bg-gray-50 transition align-middle">
                                    <td class="text-center py-4 px-4 text-gray-600 no font-medium">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="text-center py-2 px-4">
                                        @if($item->consumable && $item->consumable->image)
                                            <img src="{{ asset('storage/' . $item->consumable->image) }}"
                                                 class="w-12 h-12 object-cover rounded-lg mx-auto shadow-sm border">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 rounded-lg mx-auto flex items-center justify-center text-gray-400 text-xs">
                                                No Img
                                            </div>
                                        @endif
                                    </td>

                                    <td class="text-center py-4 px-4 font-medium text-gray-800">
                                        {{ $item->consumable->name ?? '-' }}
                                    </td>

                                    <td class="text-center py-4 px-4">
                                        {{-- Input Qty utama (visible) --}}
                                        <input type="number" value="{{ $item->qty }}" min="1" onchange="updateQty(this)"
                                               class="w-20 h-9 text-center border rounded-lg qty-input-main shadow-sm focus:ring-2 focus:ring-[#1CA7B6]">
                                        
                                        {{-- PERBAIKAN: Hidden input diletakkan di dalam <td> yang sama --}}
                                        <input type="hidden" name="items[{{ $i }}][consumable_id]" value="{{ $item->consumable_id }}">
                                        <input type="hidden" name="items[{{ $i }}][qty]" value="{{ $item->qty }}" class="hidden-qty">
                                    </td>

                                    <td class="text-center py-4 px-4">
                                        <button type="button" onclick="removeRow(this)"
                                            class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg font-semibold text-xs transition shadow-sm">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>


            {{-- ================= SAVE BUTTON (SEKARANG DI DALAM FORM) ================= --}}
            <div class="flex justify-end pt-4 mt-6 border-t border-gray-100">
                <button type="submit"
                        class="text-white px-8 py-3 rounded-xl font-bold shadow-md hover:opacity-90 transition-all duration-200 tracking-wide"
                        style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                    Save Transaksi
                </button>
            </div>

        </form>
        {{-- FORM DITUTUP DI SINI --}}

    </div>


    {{-- ================= MODAL CONSUMABLE ================= --}}
    <div id="consumableModal"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">

        <div class="bg-white w-11/12 max-w-3xl rounded-2xl shadow-2xl relative overflow-hidden flex flex-col">

            <div class="px-6 py-4 flex justify-between items-center text-white"
                 style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                <div>
                    <h3 class="text-lg font-bold">Pilih Consumable Tersedia</h3>
                </div>
                <button type="button"
                        id="closeConsumableBtn"
                        class="text-2xl text-white/80 hover:text-white transition">
                    ✕
                </button>
            </div>

            <div class="p-6 overflow-auto flex-1 bg-gray-50">
                <div class="mb-6">
                    <input type="text"
                           id="searchConsumableModal"
                           placeholder="Cari nama consumable..."
                           class="w-full bg-white border-0 rounded-xl px-4 py-3 shadow-inner focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none text-sm">
                </div>

                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm max-h-[350px] overflow-y-auto">

                    <table class="w-full text-sm" id="popupTable">

                        <thead class="sticky top-0 text-white text-xs uppercase tracking-wider"
                               style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                            <tr>
                                <th class="py-3 px-4 w-10"></th>
                                <th class="py-3 px-4 text-left font-semibold">Nama Consumable</th>
                                <th class="py-3 px-4 text-center font-semibold">Stok</th>
                                <th class="py-3 px-4 text-center font-semibold">Jumlah</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($consumables as $c)
                                <tr class="hover:bg-gray-50 transition cursor-pointer">

                                    <td class="py-3 px-4 text-center">
                                        <input type="checkbox" class="pick-consumable w-5 h-5 rounded border-gray-300 text-[#1CA7B6] focus:ring-[#1CA7B6]" 
                                               data-id="{{ $c->id }}"
                                               data-name="{{ $c->name }}"
                                               data-stock="{{ $c->stock }}">
                                    </td>

                                    <td class="py-3 px-4 font-medium text-gray-800">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ asset('storage/' . $c->image) }}"
                                                 class="w-10 h-10 object-cover rounded shadow-sm border">
                                            <span>{{ $c->name }}</span>
                                        </div>
                                    </td>

                                    <td class="py-3 px-4 text-center font-semibold {{ $c->stock <= $c->minimum_stock ? 'text-red-500' : 'text-blue-600' }}">
                                        {{ $c->stock }}
                                        @if($c->stock <= $c->minimum_stock)
                                            <div class="text-xs text-red-400 normal-case font-normal">
                                                Min: {{ $c->minimum_stock }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="py-3 px-4 text-center">
                                        <input type="number" min="1" max="{{ $c->stock }}" value="1"
                                               class="w-16 border rounded-lg text-center qty-input shadow-sm focus:ring-2 focus:ring-[#1CA7B6]">
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </div>

                <div class="flex justify-end gap-3 pt-6 mt-4 bg-transparent">
                    <button type="button"
                            id="btnCancelModal"
                            class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                        Batal
                    </button>

                    <button type="button"
                            id="btnAddConsumable"
                            class="text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:opacity-90 transition"
                            style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                        + Tambahkan
                    </button>
                </div>

            </div>

        </div>
    </div>


    {{-- ================= SCRIPT ================= --}}
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
            
            // Update hidden input for form submission
            // Pastikan mencari class hidden-qty di dalam row yang sama
            const hiddenQty = row.querySelector('.hidden-qty');
            if(hiddenQty) {
                hiddenQty.value = qty;
            }
        }

        /* ================= REMOVE ROW ================= */
        function removeRow(btn) {
            const row = btn.closest('tr');
            const id = row.dataset.id;

            row.remove();

            // Uncheck di modal jika ada
            const popupCheckbox = document.querySelector(`.pick-consumable[data-id="${id}"]`);
            if (popupCheckbox) {
                popupCheckbox.checked = false;
            }

            refreshNo();
        }

        function refreshNo() {
            document.querySelectorAll('#tableConsumables tbody tr')
                .forEach((row, i) => {
                    // Cari elemen .no di dalam row
                    const noTd = row.querySelector('.no');
                    if(noTd) noTd.innerText = i + 1;
                });
        }

        document.addEventListener('DOMContentLoaded', function () {

            const modal = document.getElementById('consumableModal');
            const openBtn = document.getElementById('openConsumableBtn');
            const closeBtn = document.getElementById('closeConsumableBtn');
            const cancelBtn = document.getElementById('btnCancelModal');
            const addBtn = document.getElementById('btnAddConsumable');
            const searchInput = document.getElementById('searchConsumableModal');

            // ================= OPEN MODAL =================
            if (openBtn && modal) {
                openBtn.addEventListener('click', function () {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });
            }

            // ================= CLOSE MODAL =================
            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                if (searchInput) searchInput.value = '';
                document.querySelectorAll('#popupTable tbody tr').forEach(row => row.style.display = '');
            }

            closeBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);
            
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeModal();
            });

            // ================= SEARCH =================
            if (searchInput) {
                searchInput.addEventListener('keyup', function () {
                    let keyword = this.value.toLowerCase();
                    document.querySelectorAll('#popupTable tbody tr').forEach(row => {
                        let name = row.children[1].innerText.toLowerCase();
                        row.style.display = name.includes(keyword) ? '' : 'none';
                    });
                });
            }

            // ================= ADD CONSUMABLE =================
            addBtn.addEventListener('click', function () {
                const selectedItems = document.querySelectorAll('.pick-consumable:checked');

                if (selectedItems.length === 0) {
                    alert("Pilih minimal 1 consumable");
                    return;
                }

                selectedItems.forEach(selected => {
                    const rowPopup = selected.closest('tr');
                    const id = selected.dataset.id;
                    const name = selected.dataset.name;
                    const stock = parseInt(selected.dataset.stock);
                    const image = rowPopup.querySelector('img').src;
                    const qty = parseInt(rowPopup.querySelector('.qty-input').value);

                    if (qty > stock) {
                        alert(`Stock ${name} hanya ${stock}`);
                        return;
                    }

                    const exist = document.querySelector(`#tableConsumables tr[data-id="${id}"]`);

                    if (exist) {
                        // Update qty jika sudah ada
                        exist.querySelector('.qty-input-main').value = qty;
                        const hiddenQtyExist = exist.querySelector('.hidden-qty');
                        if(hiddenQtyExist) hiddenQtyExist.value = qty;
                    } else {
                        // Tambah baris baru
                        // PERBAIKAN: Pastikan hidden inputs punya name yang benar
                        const html = `
                        <tr data-id="${id}" class="hover:bg-gray-50 transition align-middle border-b border-gray-100">
                            <td class="text-center py-4 px-4 text-gray-600 no font-medium"></td>
                            <td class="text-center py-2 px-4">
                                <img src="${image}" class="w-12 h-12 object-cover rounded-lg mx-auto shadow-sm border">
                            </td>
                            <td class="text-center py-4 px-4 font-medium text-gray-800">${name}</td>
                            <td class="text-center py-4 px-4">
                                <input type="number" value="${qty}" min="1" onchange="updateQty(this)"
                                       class="w-20 h-9 text-center border rounded-lg qty-input-main shadow-sm focus:ring-2 focus:ring-[#1CA7B6]">
                               
                                <input type="hidden" name="items[${index}][consumable_id]" value="${id}">
                                <input type="hidden" name="items[${index}][qty]" value="${qty}" class="hidden-qty">
                            </td>
                            <td class="text-center py-4 px-4">
                                <button type="button" onclick="removeRow(this)"
                                    class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg font-semibold text-xs transition shadow-sm">
                                    Hapus
                                </button>
                            </td>
                        </tr>`;

                        document.querySelector('#tableConsumables tbody').insertAdjacentHTML('beforeend', html);
                        index++;
                    }

                    selected.checked = false; 
                    rowPopup.querySelector('.qty-input').value = 1; 
                });

                refreshNo();
                closeModal();
            });

        });
    </script>
@endsection