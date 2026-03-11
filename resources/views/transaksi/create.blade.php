@extends('layouts.app')

@section('content')

<div class="w-full min-h-screen flex flex-col" x-data="{ openModal:false }">
    
    {{-- HEADER PAGE --}}
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="text-3xl font-bold text-[#1CA7B6] tracking-tight">Permintaan Consumable</h2>
            <p class="text-sm text-gray-500 mt-1">Proses permintaan barang dan kelola daftar</p>
        </div>
        <a href="{{ route('transaksi.index') }}"
           class="bg-[#E5E7EB] hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center shadow-sm">
            <span class="mr-1">←</span> Kembali
        </a>
    </div>

    <form id="formTransaksi" action="{{ route('transaksi.store') }}" method="POST">
        @csrf

        {{-- MAIN CARD --}}
        <div class="bg-[#F9FAFB] rounded-3xl shadow-xl p-8 border border-gray-100 space-y-8">
            
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-6">Proses Permintaan Consumable</h3>
                
                <div class="space-y-6">
                    {{-- ROW 1 --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Karyawan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="borrower_name" placeholder="Masukkan nama karyawan" required
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none">
                        </div>
                        
                        <div><!-- Kolom 3 Kosong --></div>
                    </div>

                    {{-- ROW 2 --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Client</label>
                            <input type="text" name="client" placeholder="Masukkan nama client"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Proyek</label>
                            <input type="text" name="project" placeholder="Masukkan nama proyek"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keperluan</label>
                            <input type="text" name="purpose" placeholder="Masukkan keperluan"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- SECTION DAFTAR BARANG --}}
            <div class="mt-10">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Daftar Consumable</h3>
                    <button type="button" @click.stop="openModal = true"
                            class="text-white px-5 py-2 rounded-lg text-xs font-bold shadow-md hover:opacity-90 transition-all"
                            style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                        + Pilih Consumable
                    </button>
                </div>

                {{-- Container Tabel (Tanpa Border Kotak, hanya shadow) --}}
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    <table class="w-full text-sm" id="tableConsumables">
                        <thead>
                            <tr class="text-white text-xs uppercase tracking-wider"
                                style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                                {{-- Lebar kolom diatur di sini (w-12, w-16, dll) agar sejajar dan rapat --}}
                                <th class="py-3 px-4 font-semibold text-center w-12">NO</th>
                                <th class="py-3 px-4 font-semibold text-center w-16">FOTO</th>
                                <th class="py-3 px-4 font-semibold text-left">NAMA CONSUMABLE</th>
                                <th class="py-3 px-4 font-semibold text-center w-24">JUMLAH</th>
                                <th class="py-3 px-4 font-semibold text-center w-20">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            <tr id="emptyRow">
                                <td colspan="5" class="py-10 text-center text-gray-400 italic text-sm">
                                    Belum ada consumable yang dipilih
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- SAVE BUTTON --}}
            <div class="pt-8 border-t border-gray-200 flex justify-end">
                <button type="button" id="btnSave"
                        class="text-white px-10 py-2.5 rounded-xl font-bold shadow-md hover:opacity-90 transition-all duration-200 tracking-wide"
                        style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                    Save Transaksi
                </button>
            </div>
        </div>
    </form>


    {{-- ================= MODAL ================= --}}
    <div x-show="openModal" x-transition.opacity x-cloak
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">

        <div @click.away="openModal = false"
            class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl relative max-h-[90vh] overflow-hidden flex flex-col">

            {{-- MODAL HEADER --}}
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center text-white"
                 style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                <h3 class="text-lg font-bold">Consumable Tersedia</h3>
                <button type="button" @click="openModal=false"
                    class="text-white/80 hover:text-white text-2xl transition">✕</button>
            </div>

            <div class="p-6 flex-1 overflow-auto">
                {{-- SEARCH INPUT --}}
                <div class="mb-5">
                    <div class="relative">
                         <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" id="searchConsumable" placeholder="Cari Nama Consumable"
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3 shadow-inner focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none transition text-sm">
                    </div>
                </div>

                {{-- TABLE MODAL --}}
                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                    <table id="popupTable" class="w-full text-sm">
                        <thead class="sticky top-0 bg-gray-50">
                            <tr class="text-gray-600 border-b border-gray-200">
                                <th class="py-3 px-4 text-center w-10">
                                    <input type="checkbox" id="selectAllCons" class="w-4 h-4 accent-[#1CA7B6] rounded border-gray-300">
                                </th>
                                <th class="py-3 px-4 text-left font-semibold">Nama Consumable</th>
                                <th class="py-3 px-4 text-center font-semibold">Stock</th>
                                <th class="py-3 px-4 text-center w-24 font-semibold">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($consumables as $c)
                            <tr class="border-b hover:bg-teal-50/50 transition cursor-pointer cons-row" data-name="{{ strtolower($c->name) }}">
                                <td class="text-center py-3 px-4">
                                    <input type="checkbox" class="pick-consumable w-4 h-4 accent-[#1CA7B6] rounded border-gray-300"
                                        data-id="{{ $c->id }}"
                                        data-name="{{ $c->name }}"
                                        data-stock="{{ $c->stock }}">
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                         <img src="{{ asset('storage/' . $c->image) }}"
                                            class="w-10 h-10 object-cover rounded-lg border shadow-sm">
                                         <span class="font-medium text-gray-800">{{ $c->name }}</span>
                                    </div>
                                </td>
                                <td class="text-center py-3 px-4">
                                    <span class="font-semibold {{ $c->stock <= $c->minimum_stock ? 'text-red-500' : 'text-blue-600' }}">
                                        {{ $c->stock }}
                                    </span>
                                    @if($c->stock <= $c->minimum_stock)
                                        <div class="text-xs text-red-400">Min: {{ $c->minimum_stock }}</div>
                                    @endif
                                </td>
                                <td class="text-center py-3 px-4">
                                    <input type="number" min="1" max="{{ $c->stock }}" value="1"
                                        class="w-16 h-8 border border-gray-300 rounded-lg text-center qty-input shadow-sm focus:ring-1 focus:ring-[#1CA7B6] focus:outline-none">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MODAL FOOTER --}}
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50">
                <button type="button" @click="openModal=false"
                    class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-100 transition font-medium text-sm shadow-sm">
                    Batal
                </button>

                <button type="button" id="btnAddConsumable"
                    @click="openModal = false"
                    class="px-6 py-2.5 text-white rounded-xl hover:opacity-90 transition font-medium text-sm shadow-md flex items-center gap-2"
                    style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambahkan
                </button>
            </div>
        </div>
    </div>

</div>

<style>
    /* Hilangkan garis border pada tabel utama */
    #tableConsumables th, #tableConsumables td {
        border: none !important;
    }
    /* Styling shadow dalam input search */
    .shadow-inner {
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05);
    }
    input::placeholder {
        color: #9CA3AF;
        font-weight: 400;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const btnSave = document.getElementById('btnSave');
    const form = document.getElementById('formTransaksi');
    const btnAddConsumable = document.getElementById('btnAddConsumable');
    const searchInput = document.getElementById('searchConsumable');
    const selectAllCheckbox = document.getElementById('selectAllCons');

    // ===== VALIDATION SAVE =====
    btnSave.addEventListener('click', function () {
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

        const items = document.querySelectorAll('#tableConsumables tbody tr:not(#emptyRow)');

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

    // ===== SEARCH MODAL =====
    searchInput?.addEventListener('keyup', function () {
        const keyword = this.value.toLowerCase();
        const rows = document.querySelectorAll('#popupTable tbody tr.cons-row');

        rows.forEach(row => {
            const nameText = row.dataset.name;
            row.style.display = nameText.includes(keyword) ? '' : 'none';
        });
    });

    // ===== SELECT ALL MODAL =====
    selectAllCheckbox?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.pick-consumable');
        checkboxes.forEach(cb => {
            if (cb.closest('tr').style.display !== 'none') {
                cb.checked = this.checked;
            }
        });
    });

    // ===== LOGIC TAMBAH ITEM =====
    let index = 0;

    function refreshNo() {
        const rows = document.querySelectorAll('#tableConsumables tbody tr:not(#emptyRow)');
        rows.forEach((row, i) => {
            row.querySelector('.no-col').innerText = i + 1;
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
        
        const tbody = document.querySelector('#tableConsumables tbody');
        if (tbody.querySelectorAll('tr:not(#emptyRow)').length === 0) {
            tbody.innerHTML = `
                <tr id="emptyRow">
                    <td colspan="5" class="py-10 text-center text-gray-400 italic text-sm">
                        Belum ada consumable yang dipilih
                    </td>
                </tr>`;
        } else {
            refreshNo();
        }
    };

    btnAddConsumable.addEventListener('click', function () {

        const selectedItems = document.querySelectorAll('.pick-consumable:checked');
        if (selectedItems.length === 0) {
            alert("Pilih minimal 1 consumable");
            return;
        }

        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) emptyRow.remove();

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
                // PERBAIKAN: Padding dikurangi jadi px-4, w-12, w-16 dll sesuai header
                const html = `
                <tr data-id="${id}" class="hover:bg-gray-50 transition">
                    <td class="text-center py-3 px-4 text-gray-600 no-col font-medium w-12">1</td>
                    <td class="text-center py-3 px-4 w-16">
                        <img src="${image}"
                            class="w-10 h-10 object-cover rounded-lg shadow-sm mx-auto">
                    </td>
                    <td class="py-3 px-4 font-semibold text-gray-800">${name}</td>
                    <td class="text-center py-3 px-4 w-24">
                        <input type="number"
                            value="${qty}"
                            min="1"
                            onchange="updateQty(this)"
                            class="w-16 h-8 text-center border border-gray-300 rounded-lg qty-input-main shadow-sm focus:ring-1 focus:ring-[#1CA7B6] focus:outline-none">
                    </td>
                    <td class="text-center py-3 px-4 w-20">
                        <button type="button"
                            onclick="removeRow(this)"
                            class="bg-red-50 text-red-500 px-2 py-1 rounded-lg text-xs font-bold hover:bg-red-100 transition">
                            Hapus
                        </button>

                        <input type="hidden" name="items[${index}][consumable_id]" value="${id}">
                        <input type="hidden" name="items[${index}][qty]" value="${qty}" class="hidden-qty">
                    </td>
                </tr>`;

                document.querySelector('#tableConsumables tbody').insertAdjacentHTML('beforeend', html);
                index++;
            }

            selected.checked = false;
            row.querySelector('.qty-input').value = 1;
        });
        
        if(selectAllCheckbox) selectAllCheckbox.checked = false;

        refreshNo();
    });
});
</script>
@endsection