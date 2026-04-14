@extends('layouts.app')

@section('content')

    <div class="w-full min-h-screen flex flex-col" x-data="{ openModal:false }" @close-modal.window="openModal = false">

        {{-- HEADER PAGE --}}
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-3xl font-bold text-[#5EA6FF] tracking-tight">Permintaan Consumable</h2>
                <p class="text-sm text-gray-500 mt-1">Proses permintaan barang dan kelola daftar</p>
            </div>
            <a href="{{ route('transaksi.index') }}"
                class="bg-[#E5E7EB] hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center shadow-sm">
                <span class="mr-1">←</span> Kembali
            </a>
        </div>

        {{-- NOTIF TOAST HALAMAN UTAMA --}}
        <div id="notifWrap" class="hidden mb-5">
            <div id="notifBox"
                class="relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border">
                <div id="notifIcon" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"></div>
                <p id="notifText" class="text-sm font-medium"></p>
                <button id="notifClose" class="ml-auto flex-shrink-0 opacity-50 hover:opacity-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div id="notifBar" class="absolute bottom-0 left-0 h-1 rounded-b-2xl" style="width:0%"></div>
            </div>
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

                            <script>window.empData = @json($employees);</script>

                            {{-- Search Employee --}}
                            <div x-data="empSearch()" @click.away="show = false" class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Karyawan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" x-model="search" @focus="if(!selected) show = true"
                                        @input="if(!selected) show = true" placeholder="Ketik nama karyawan..."
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none pr-9">
                                    <button type="button" x-show="selected" x-cloak
                                        @click="search = ''; selected = ''; selectedId = ''"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <input type="hidden" name="employee_id" :value="selectedId">
                                <input type="hidden" name="borrower_name" :value="selected">
                                <div x-show="show && !selected && search.length > 0" x-transition x-cloak
                                    class="absolute z-50 mt-1 w-full bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                                    <div class="max-h-56 overflow-y-auto">
                                        <template x-for="[id, name] in filtered" :key="id">
                                            <button type="button"
                                                @click="selected = name; selectedId = id; search = name; show = false"
                                                class="w-full px-4 py-2.5 text-left text-sm hover:bg-blue-50 transition border-b border-gray-50 last:border-0">
                                                <span x-text="name"></span>
                                            </button>
                                        </template>
                                        <template x-if="filtered.length === 0">
                                            <div class="px-4 py-6 text-center text-gray-400 text-sm">Tidak ditemukan</div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- Tanggal --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none">
                            </div>
                            <div></div>
                        </div>

                        {{-- ROW 2 --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Client</label>
                                <input type="text" name="client" placeholder="Masukkan nama client"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Proyek</label>
                                <input type="text" name="project" placeholder="Masukkan nama proyek"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keperluan</label>
                                <input type="text" name="purpose" placeholder="Masukkan keperluan"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION DAFTAR BARANG --}}
                <div class="mt-10">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Daftar Consumable</h3>
                        {{-- TOMBOL PILIH CONSUMABLE (STYLE PUTIH) --}}
                        <button type="button" @click.stop="openModal = true"
                            class="group inline-flex items-center px-4 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all duration-200 tracking-wide border-2 border-[#5EA6FF] bg-white text-sm text-[#5EA6FF] hover:bg-[#5EA6FF] hover:text-white hover:shadow-blue-500/40 hover:-translate-y-0.5">
                            <svg class="w-4 h-4 mr-2 transition-transform duration-300 group-hover:rotate-90" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                            Pilih Consumable
                        </button>
                    </div>
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                        <table class="w-full text-sm" id="tableConsumables">
                            <thead>
                                <tr class="text-white text-xs uppercase tracking-wider"
                                    style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">
                                    <th class="py-3 px-4 font-semibold text-center w-12">No</th>
                                    <th class="py-3 px-4 font-semibold text-center w-20">Foto</th>
                                    <th class="py-3 px-4 font-semibold text-left">Nama Consumable</th>
                                    <th class="py-3 px-4 font-semibold text-center w-24">Stock</th>
                                    <th class="py-3 px-4 font-semibold text-center w-32">Jumlah</th>
                                    <th class="py-3 px-4 font-semibold text-center w-16">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-50">
                                <tr id="emptyRow">
                                    <td colspan="6" class="py-10 text-center text-gray-400 italic text-sm">
                                        Belum ada consumable yang dipilih
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- SAVE BUTTON --}}
                <div class="pt-8 border-t border-gray-200 flex justify-end">
                    {{-- TOMBOL SAVE (STYLE PUTIH) --}}
                    <button type="button" id="btnSave"
                        class="px-10 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all duration-200 tracking-wide border-2 border-[#5EA6FF] bg-white text-sm text-[#5EA6FF] hover:bg-[#5EA6FF] hover:text-white hover:shadow-blue-500/40 hover:-translate-y-0.5">
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

                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center text-white"
                    style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">
                    <h3 class="text-lg font-bold">Consumable Tersedia</h3>
                    <button type="button" @click="openModal=false"
                        class="text-white/80 hover:text-white text-2xl transition">✕</button>
                </div>

                <div class="p-6 flex-1 overflow-auto">

                    {{-- NOTIF ERROR DI DALAM MODAL --}}
                    <div id="modalNotifWrap" class="hidden mb-4">
                        <div id="modalNotifBox"
                            class="relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border">
                            <div id="modalNotifIcon"
                                class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"></div>
                            <p id="modalNotifText" class="text-sm font-medium"></p>
                            <button id="modalNotifClose"
                                class="ml-auto flex-shrink-0 opacity-50 hover:opacity-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div id="modalNotifBar" class="absolute bottom-0 left-0 h-1 rounded-b-2xl" style="width:0%">
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </span>
                            <input type="text" id="searchConsumable" placeholder="Cari Nama Consumable"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3 shadow-inner focus:ring-2 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none transition text-sm">
                        </div>
                    </div>

                    <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                        <table id="popupTable" class="w-full text-sm">
                            <thead class="sticky top-0 bg-gray-50">
                                <tr class="text-gray-600 border-b border-gray-200">
                                    <th class="py-3 px-4 text-center w-10">
                                        <input type="checkbox" id="selectAllCons"
                                            class="w-4 h-4 accent-[#5EA6FF] rounded border-gray-300">
                                    </th>
                                    <th class="py-3 px-4 text-left font-semibold">Nama Consumable</th>
                                    <th class="py-3 px-4 text-center font-semibold">Stock</th>
                                    <th class="py-3 px-4 text-center w-24 font-semibold">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($consumables as $c)
                                    <tr class="border-b hover:bg-blue-50/30 transition cursor-pointer cons-row"
                                        data-name="{{ strtolower($c->name) }}">
                                        <td class="text-center py-3 px-4">
                                            <input type="checkbox"
                                                class="pick-consumable w-4 h-4 accent-[#5EA6FF] rounded border-gray-300"
                                                data-id="{{ $c->id }}" data-name="{{ $c->name }}" data-stock="{{ $c->stock }}"
                                                data-unit="{{ $c->unit }}">
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ $c->image ? asset('storage/' . $c->image) : asset('images/no-image.png') }}"
                                                    class="preview-click w-10 h-10 object-cover rounded-lg cursor-pointer"
                                                    onerror="this.src='{{ asset('images/no-image.png') }}'"
                                                    class="w-10 h-10 object-cover rounded-lg border shadow-sm">
                                                <span class="font-medium text-gray-800">{{ $c->name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center py-3 px-4">
                                            <span
                                                class="font-semibold {{ $c->stock <= $c->minimum_stock ? 'text-red-500' : 'text-blue-600' }}">
                                                {{ $c->stock }}
                                            </span>
                                            <div class="text-xs text-gray-400">{{ $c->unit }}</div>
                                            @if($c->stock <= $c->minimum_stock)
                                                <div class="text-xs text-red-400">Min: {{ $c->minimum_stock }}</div>
                                            @endif
                                        </td>
                                        <td class="text-center py-3 px-4">
                                            <input type="number" min="1" max="{{ $c->stock }}" value="1"
                                                class="w-16 h-8 border border-gray-300 rounded-lg text-center qty-input shadow-sm focus:ring-1 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none">
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
                    {{-- TOMBOL TAMBAHKAN (STYLE PUTIH) --}}
                    <button type="button" id="btnAddConsumable"
                        class="group px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all duration-200 tracking-wide border-2 border-[#5EA6FF] bg-white text-sm text-[#5EA6FF] hover:bg-[#5EA6FF] hover:text-white hover:shadow-blue-500/40 hover:-translate-y-0.5 flex items-center gap-2">
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-90" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        Tambahkan
                    </button>
                </div>
            </div>
        </div>

        {{-- ================= MODAL HAPUS ITEM ================= --}}
        <div id="deleteItemModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[10002] p-3 sm:p-4">
            <div class="w-[calc(100%-1.5rem)] sm:w-11/12 max-w-sm bg-white rounded-2xl shadow-2xl p-5 sm:p-6 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8 text-red-500" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-2">Hapus Item?</h3>
                <p class="text-xs sm:text-sm text-gray-500 mb-1">Anda yakin ingin menghapus</p>
                <p id="deleteItemNameModal" class="text-xs sm:text-sm font-semibold text-[#5EA6FF] mb-5"></p>
                <div class="flex gap-3">
                    <button id="cancelDeleteItem"
                        class="flex-1 px-5 py-2.5 bg-[#dcdcdc] text-gray-700 rounded-xl text-xs sm:text-sm font-semibold hover:bg-[#c5c5c5] transition">
                        Batal
                    </button>
                    <button id="confirmDeleteItem"
                        class="flex-1 px-5 py-2.5 bg-red-500 text-white rounded-xl text-xs sm:text-sm font-semibold hover:bg-red-600 transition">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL PREVIEW IMAGE -->
        <div id="imagePreviewModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-[9999]">

            <div class="relative">
                <img id="previewImg" class="max-w-[90vw] max-h-[90vh] rounded-xl">

                <button id="closePreviewBtn"
                    class="absolute -top-3 -right-3 bg-white rounded-full px-3 py-1 shadow z-[10000]">
                    ✕
                </button>
            </div>
        </div>

    </div>

    <style>
        #tableConsumables th,
        #tableConsumables td {
            border: none !important;
        }

        .shadow-inner {
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05);
        }

        input::placeholder {
            color: #9CA3AF;
            font-weight: 400;
        }

        #notifWrap,
        #modalNotifWrap {
            animation: notifSlideIn 0.4s ease-out;
        }

        @keyframes notifSlideIn {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        #notifWrap.hiding,
        #modalNotifWrap.hiding {
            animation: notifSlideOut 0.35s ease-in forwards;
        }

        @keyframes notifSlideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(60px);
            }
        }

        #notifBar,
        #modalNotifBar {
            transition: width 3.5s linear;
        }

        .row-error {
            background-color: #fef2f2 !important;
            animation: shakeRow 0.4s ease-in-out;
        }

        @keyframes shakeRow {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .btn-delete-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #fef2f2;
            color: #ef4444;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-delete-icon:hover {
            background: #fee2e2;
            transform: scale(1.1);
        }

        .btn-delete-icon:active {
            transform: scale(0.95);
        }
    </style>

    <script>
        function empSearch() {
            return {
                search: '',
                selected: '',
                selectedId: '',
                show: false,
                list: window.empData || {},
                get filtered() {
                    if (!this.search) return [];
                    const q = this.search.toLowerCase();
                    return Object.entries(this.list).filter(([id, name]) => name.toLowerCase().includes(q));
                }
            };
        }
        document.addEventListener('DOMContentLoaded', function () {

            const btnSave = document.getElementById('btnSave');
            const form = document.getElementById('formTransaksi');
            const btnAddConsumable = document.getElementById('btnAddConsumable');
            const searchInput = document.getElementById('searchConsumable');
            const selectAllCheckbox = document.getElementById('selectAllCons');

            // ===== ELEMEN NOTIF HALAMAN UTAMA =====
            const notifWrap = document.getElementById('notifWrap');
            const notifBox = document.getElementById('notifBox');
            const notifIcon = document.getElementById('notifIcon');
            const notifText = document.getElementById('notifText');
            const notifBar = document.getElementById('notifBar');
            const notifClose = document.getElementById('notifClose');
            let notifTimer = null;

            // ===== ELEMEN NOTIF MODAL =====
            const modalNotifWrap = document.getElementById('modalNotifWrap');
            const modalNotifBox = document.getElementById('modalNotifBox');
            const modalNotifIcon = document.getElementById('modalNotifIcon');
            const modalNotifText = document.getElementById('modalNotifText');
            const modalNotifBar = document.getElementById('modalNotifBar');
            const modalNotifClose = document.getElementById('modalNotifClose');
            let modalNotifTimer = null;

            // ===== FUNGSI NOTIF HALAMAN UTAMA =====
            function showNotif(message, type) {
                if (notifTimer) clearTimeout(notifTimer);
                notifWrap.classList.remove('hidden', 'hiding');

                if (type === 'success') {
                    notifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-green-50 border-green-200 text-green-800';
                    notifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-green-100';
                    notifIcon.innerHTML = '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>';
                    notifBar.style.background = '#22c55e';
                } else if (type === 'warning') {
                    notifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-amber-50 border-amber-200 text-amber-800';
                    notifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-amber-100';
                    notifIcon.innerHTML = '<svg class="w-4.5 h-4.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>';
                    notifBar.style.background = '#fbbf24';
                } else {
                    notifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-red-50 border-red-200 text-red-800';
                    notifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-red-100';
                    notifIcon.innerHTML = '<svg class="w-4.5 h-4.5 text-red-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
                    notifBar.style.background = '#f87171';
                }

                notifText.textContent = message;
                notifBar.style.transition = 'none';
                notifBar.style.width = '0%';

                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        notifBar.style.transition = 'width 3.5s linear';
                        notifBar.style.width = '100%';
                    });
                });

                notifTimer = setTimeout(() => hideNotif(), 3500);
            }

            function hideNotif() {
                notifWrap.classList.add('hiding');
                setTimeout(() => {
                    notifWrap.classList.add('hidden');
                    notifWrap.classList.remove('hiding');
                }, 250);
            }

            notifClose.addEventListener('click', () => {
                if (notifTimer) clearTimeout(notifTimer);
                hideNotif();
            });

            // ===== FUNGSI NOTIF MODAL =====
            function showModalNotif(message, type) {
                if (modalNotifTimer) clearTimeout(modalNotifTimer);
                modalNotifWrap.classList.remove('hidden', 'hiding');

                if (type === 'success') {
                    modalNotifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-blue-50 border-blue-200 text-blue-800';
                    modalNotifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-blue-100';
                    modalNotifIcon.innerHTML = '<svg class="w-4.5 h-4.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>';
                    modalNotifBar.style.background = '#5EA6FF';
                } else if (type === 'warning') {
                    modalNotifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-amber-50 border-amber-200 text-amber-800';
                    modalNotifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-amber-100';
                    modalNotifIcon.innerHTML = '<svg class="w-4.5 h-4.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>';
                    modalNotifBar.style.background = '#fbbf24';
                } else {
                    modalNotifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-red-50 border-red-200 text-red-800';
                    modalNotifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-red-100';
                    modalNotifIcon.innerHTML = '<svg class="w-4.5 h-4.5 text-red-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
                    modalNotifBar.style.background = '#f87171';
                }

                modalNotifText.textContent = message;
                modalNotifBar.style.transition = 'none';
                modalNotifBar.style.width = '0%';

                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        modalNotifBar.style.transition = 'width 3.5s linear';
                        modalNotifBar.style.width = '100%';
                    });
                });

                modalNotifTimer = setTimeout(() => hideModalNotif(), 3500);
            }

            function hideModalNotif() {
                modalNotifWrap.classList.add('hiding');
                setTimeout(() => {
                    modalNotifWrap.classList.add('hidden');
                    modalNotifWrap.classList.remove('hiding');
                }, 250);
            }

            modalNotifClose.addEventListener('click', () => {
                if (modalNotifTimer) clearTimeout(modalNotifTimer);
                hideModalNotif();
            });

            // ===== SESSION NOTIF =====
            @if(session('success'))
                showNotif('{{ session("success") }}', 'success');
            @endif
            @if(session('error'))
                showNotif('{{ session("error") }}', 'error');
            @endif

            // ===== VALIDASI SAVE =====
            btnSave.addEventListener('click', function () {

                // 1. Cegah double click langsung di sini
                if (btnSave.disabled) return;

                // Ubah state tombol jadi loading/disable
                btnSave.disabled = true;
                const originalText = btnSave.innerText;
                btnSave.innerText = "Proses...";
                btnSave.classList.add('opacity-75', 'cursor-not-allowed');

                // Fungsi buat balikin tombol kalau validasi gagal
                const resetButton = () => {
                    btnSave.disabled = false;
                    btnSave.innerText = originalText;
                    btnSave.classList.remove('opacity-75', 'cursor-not-allowed');
                };

                const employeeId = form.querySelector('input[name="employee_id"]');
                const items = document.querySelectorAll('#tableConsumables tbody tr:not(#emptyRow)');

                document.querySelectorAll('.row-error').forEach(row => {
                    row.classList.remove('row-error');
                });

                if (!employeeId.value) {
                    showNotif("Pilih karyawan terlebih dahulu", "error");
                    resetButton(); // Balikin tombol karena error
                    return;
                }

                if (items.length === 0) {
                    showNotif("Pilih minimal 1 consumable terlebih dahulu", "error");
                    resetButton(); // Balikin tombol karena error
                    return;
                }

                let stockErrors = [];
                items.forEach(row => {
                    const qtyInput = row.querySelector('.qty-input-main');
                    const hiddenQty = row.querySelector('.hidden-qty');
                    const stock = parseInt(row.dataset.stock);
                    let qty = parseInt(qtyInput.value);
                    const itemName = row.querySelector('.item-name').textContent.trim();

                    if (isNaN(qty) || qty <= 0) {
                        qty = 1;
                        qtyInput.value = 1;
                    }

                    if (hiddenQty) hiddenQty.value = qty;

                    if (qty > stock) {
                        stockErrors.push(itemName + " - stock hanya " + stock + ", Anda meminta " + qty);
                        row.classList.add('row-error');
                    }
                });

                if (stockErrors.length > 0) {
                    showNotif(stockErrors[0], "error");
                    const firstErrorRow = document.querySelector('.row-error');
                    if (firstErrorRow) {
                        firstErrorRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    resetButton(); 
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
                    if (row.dataset.added === 'true') {
                        row.style.display = 'none';
                        return;
                    }
                    row.style.display = row.dataset.name.includes(keyword) ? '' : 'none';
                });
            });

            // ===== SELECT ALL MODAL =====
            selectAllCheckbox?.addEventListener('change', function () {
                const checkboxes = document.querySelectorAll('.pick-consumable');
                checkboxes.forEach(cb => {
                    if (cb.closest('tr').style.display !== 'none') {
                        cb.checked = this.checked;
                    }
                });
            });

            // ===== LOGIC QTY =====
            window.updateQty = function (input) {
                const row = input.closest('tr');
                const qty = parseInt(input.value);
                const stock = parseInt(row.dataset.stock);

                row.classList.remove('row-error');

                if (isNaN(qty) || qty <= 0) {
                    const hiddenQty = row.querySelector('.hidden-qty');
                    if (hiddenQty) hiddenQty.value = input.value;
                    return;
                }

                if (qty > stock) {
                    const itemName = row.querySelector('.item-name').textContent.trim();
                    showNotif("Stock " + itemName + " hanya tersedia " + stock, "warning");
                    row.classList.add('row-error');
                    input.value = stock;
                    const hiddenQty = row.querySelector('.hidden-qty');
                    if (hiddenQty) hiddenQty.value = stock;
                    return;
                }

                const hiddenQty = row.querySelector('.hidden-qty');
                if (hiddenQty) hiddenQty.value = qty;
            };

            // ===== LOGIC HAPUS ITEM =====
            let rowToDelete = null;
            const deleteItemModal = document.getElementById('deleteItemModal');
            const deleteItemNameModal = document.getElementById('deleteItemNameModal');

            window.removeRow = function (btn) {
                const row = btn.closest('tr');
                rowToDelete = row;
                deleteItemNameModal.textContent = row.querySelector('.item-name').textContent.trim();
                deleteItemModal.classList.remove('hidden');
                deleteItemModal.classList.add('flex');
            };

            function closeDeleteItemModal() {
                deleteItemModal.classList.add('hidden');
                deleteItemModal.classList.remove('flex');
                rowToDelete = null;
            }

            document.getElementById('cancelDeleteItem').addEventListener('click', closeDeleteItemModal);
            deleteItemModal.addEventListener('click', function (e) {
                if (e.target === deleteItemModal) closeDeleteItemModal();
            });

            document.getElementById('confirmDeleteItem').addEventListener('click', function () {
                if (rowToDelete) {
                    const itemName = rowToDelete.querySelector('.item-name').textContent.trim();
                    const rowId = rowToDelete.dataset.id;

                    rowToDelete.remove();

                    const modalCheckbox = document.querySelector(`.pick-consumable[data-id="${rowId}"]`);
                    if (modalCheckbox) {
                        const modalRow = modalCheckbox.closest('tr');
                        delete modalRow.dataset.added;
                        modalRow.style.display = '';
                    }

                    const tbody = document.querySelector('#tableConsumables tbody');
                    if (tbody.querySelectorAll('tr:not(#emptyRow)').length === 0) {
                        tbody.innerHTML = '<tr id="emptyRow"><td colspan="6" class="py-10 text-center text-gray-400 italic text-sm">Belum ada consumable yang dipilih</td></tr>';
                    } else {
                        document.querySelectorAll('#tableConsumables tbody tr:not(#emptyRow)').forEach((row, i) => {
                            row.querySelector('.no-col').innerText = i + 1;
                        });
                    }

                    showNotif(itemName + " berhasil dihapus", "success");
                }
                closeDeleteItemModal();
            });


            // ===== BUTTON TAMBAH DARI MODAL =====
            let index = 0;

            btnAddConsumable.addEventListener('click', function () {

                const selectedItems = document.querySelectorAll('.pick-consumable:checked');
                if (selectedItems.length === 0) {
                    showModalNotif("Pilih minimal 1 consumable", "error");
                    return;
                }

                let hasError = false;

                selectedItems.forEach(selected => {
                    const row = selected.closest('tr');
                    const name = selected.dataset.name;
                    const stock = parseInt(selected.dataset.stock);
                    const qty = parseInt(row.querySelector('.qty-input').value);

                    if (qty > stock) {
                        showModalNotif("Stock " + name + " hanya tersedia " + stock, "error");
                        hasError = true;
                    }
                });

                if (hasError) return;

                const emptyRow = document.getElementById('emptyRow');
                if (emptyRow) emptyRow.remove();

                let addedCount = 0;
                let updatedCount = 0;
                const existingRows = document.querySelectorAll('#tableConsumables tbody tr:not(#emptyRow)');
                let startNo = existingRows.length;

                selectedItems.forEach(selected => {
                    const row = selected.closest('tr');
                    const id = selected.dataset.id;
                    const name = selected.dataset.name;
                    const stock = parseInt(selected.dataset.stock);
                    const unit = selected.dataset.unit;
                    const image = row.querySelector('img').src;
                    const qty = parseInt(row.querySelector('.qty-input').value);

                    const exist = document.querySelector(`#tableConsumables tr[data-id="${id}"]`);
                    if (exist) {
                        exist.querySelector('.qty-input-main').value = qty;
                        exist.querySelector('.qty-input-main').max = stock;
                        exist.querySelector('.hidden-qty').value = qty;
                        exist.dataset.stock = stock;
                        const stockDisplay = exist.querySelector('.stock-display');
                        if (stockDisplay) stockDisplay.textContent = stock;
                        const unitDisplay = exist.querySelector('.unit-display');
                        if (unitDisplay) unitDisplay.textContent = unit;
                        updatedCount++;
                    } else {
                        startNo++;

                        const html = `
                                                        <tr data-id="${id}" data-stock="${stock}" class="hover:bg-gray-50 transition">
                                                            <td class="py-3 px-4 text-center font-medium text-gray-600 w-12">
                                                                <span class="no-col">${startNo}</span>
                                                            </td>
                                                            <td class="py-3 px-4 text-center w-20">
                                                                <img src="${image}" class="w-10 h-10 object-cover rounded-lg shadow-sm mx-auto">
                                                            </td>
                                                            <td class="py-3 px-4">
                                                                <span class="font-semibold text-gray-800 item-name">${name}</span>
                                                            </td>
                                                            <td class="text-center py-3 px-4 w-24">
                                                                <div class="font-medium text-blue-600 stock-display">${stock}</div>
                                                                <div class="text-xs text-gray-400 unit-display">${unit}</div>
                                                            </td>
                                                            <td class="text-center py-3 px-4 w-32">
                                                                <input type="number" value="${qty}" min="1" max="${stock}" onchange="updateQty(this)"
                                                                    class="w-20 h-8 text-center border border-gray-300 rounded-lg qty-input-main shadow-sm focus:ring-1 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none">
                                                            </td>
                                                            <td class="text-center py-3 px-4 w-16">
                                                                <button type="button" onclick="removeRow(this)" class="btn-delete-icon" title="Hapus item">
                                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                                    </svg>
                                                                </button>
                                                                <input type="hidden" name="items[${index}][consumable_id]" value="${id}">
                                                                <input type="hidden" name="items[${index}][qty]" value="${qty}" class="hidden-qty">
                                                            </td>
                                                        </tr>`;
                        document.querySelector('#tableConsumables tbody').insertAdjacentHTML('beforeend', html);
                        index++;
                        addedCount++;
                    }

                    row.dataset.added = 'true';
                    row.style.display = 'none';
                    selected.checked = false;
                    row.querySelector('.qty-input').value = 1;
                });

                if (selectAllCheckbox) selectAllCheckbox.checked = false;

                window.dispatchEvent(new CustomEvent('close-modal'));

                if (addedCount > 0 && updatedCount === 0) {
                    showNotif(addedCount + " consumable berhasil ditambahkan", "success");
                } else if (addedCount > 0 && updatedCount > 0) {
                    showNotif(addedCount + " consumable ditambahkan, " + updatedCount + " diperbarui", "success");
                } else if (updatedCount > 0 && addedCount === 0) {
                    showNotif(updatedCount + " consumable berhasil diperbarui", "success");
                }
            });

            // ===== ESC KEY =====
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeDeleteItemModal();
            });

            document.querySelectorAll('.preview-click').forEach(img => {
                img.addEventListener('click', function () {
                    const modal = document.getElementById('imagePreviewModal');
                    const preview = document.getElementById('previewImg');

                    preview.src = this.src;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });
            });

            function closeImagePreview() {
                const modal = document.getElementById('imagePreviewModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            document.getElementById('closePreviewBtn').addEventListener('click', function () {
                const modal = document.getElementById('imagePreviewModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });

        });
    </script>
@endsection