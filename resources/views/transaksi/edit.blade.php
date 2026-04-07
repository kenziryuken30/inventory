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

        {{-- ================= NOTIF TOAST HALAMAN UTAMA ================= --}}
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

        {{-- ================= PANEL BESAR ================= --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 space-y-8">

            <form id="updateForm" action="{{ route('transaksi.update', $transaction->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Karyawan</label>
                        <input type="text" name="borrower_name" value="{{ $transaction->borrower_name }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal</label>
                        <input type="date" name="date" value="{{ $transaction->date->format('Y-m-d') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                    </div>

                    <div class="hidden md:block">
                        <label class="block text-sm font-bold text-gray-700 mb-2">&nbsp;</label>
                        <div class="w-full px-4 py-2.5">&nbsp;</div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Client</label>
                        <input type="text" name="client" value="{{ $transaction->client }}" placeholder="Masukan nama klien"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Proyek</label>
                        <input type="text" name="project" value="{{ $transaction->project }}"
                            placeholder="Masukan Keterangan"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Keperluan</label>
                        <input type="text" name="purpose" value="{{ $transaction->purpose }}"
                            placeholder="Masukan Keperluan"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                    </div>
                </div>


                {{-- ================= DAFTAR CONSUMABLE ================= --}}
                <div class="space-y-4 mt-8">

                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 text-lg">Daftar Consumable yang Di Edit</h3>

                        <button type="button" id="openConsumableBtn"
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
                                    <th class="py-3 px-4 font-semibold text-center w-24">Stock</th>
                                    <th class="py-3 px-4 font-semibold text-center w-32">Jumlah</th>
                                    <th class="py-3 px-4 font-semibold text-center w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($transaction->items as $i => $item)
                                    <tr data-id="{{ $item->consumable_id }}" data-stock="{{ $item->consumable->stock ?? 0 }}" class="hover:bg-gray-50 transition align-middle">
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

                                        <td class="text-center py-4 px-4 font-medium text-gray-800 item-name">
                                            {{ $item->consumable->name ?? '-' }}
                                        </td>

                                        <td class="text-center py-4 px-4 font-semibold text-blue-600 stock-display">
                                            {{ $item->consumable->stock ?? 0 }}
                                        </td>

                                        <td class="text-center py-4 px-4">
                                            <input type="number" value="{{ $item->qty }}" min="1" max="{{ $item->consumable->stock ?? 0 }}" onchange="updateQty(this)"
                                                class="w-20 h-9 text-center border rounded-lg qty-input-main shadow-sm focus:ring-2 focus:ring-[#1CA7B6]">

                                            <input type="hidden" name="items[{{ $i }}][consumable_id]"
                                                value="{{ $item->consumable_id }}">
                                            <input type="hidden" name="items[{{ $i }}][qty]" value="{{ $item->qty }}"
                                                class="hidden-qty">
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


                {{-- ================= SAVE BUTTON ================= --}}
                <div class="flex justify-end pt-4 mt-6 border-t border-gray-100">
                    <button type="button" id="btnSave"
                        class="text-white px-8 py-3 rounded-xl font-bold shadow-md hover:opacity-90 transition-all duration-200 tracking-wide"
                        style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                        Save Transaksi
                    </button>
                </div>

            </form>

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
                    <button type="button" id="closeConsumableBtn"
                        class="text-2xl text-white/80 hover:text-white transition">
                        ✕
                    </button>
                </div>

                <div class="p-6 overflow-auto flex-1 bg-gray-50">

                    {{-- ========== NOTIF ERROR DI DALAM MODAL ========== --}}
                    <div id="modalNotifWrap" class="hidden mb-4">
                        <div id="modalNotifBox"
                            class="relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border">
                            <div id="modalNotifIcon" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"></div>
                            <p id="modalNotifText" class="text-sm font-medium"></p>
                            <button id="modalNotifClose" class="ml-auto flex-shrink-0 opacity-50 hover:opacity-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div id="modalNotifBar" class="absolute bottom-0 left-0 h-1 rounded-b-2xl" style="width:0%"></div>
                        </div>
                    </div>
                    {{-- ========== END NOTIF MODAL ========== --}}

                    <div class="mb-6">
                        <input type="text" id="searchConsumableModal" placeholder="Cari nama consumable..."
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
                                    <tr class="hover:bg-gray-50 transition cursor-pointer cons-row" data-name="{{ strtolower($c->name) }}">

                                        <td class="py-3 px-4 text-center">
                                            <input type="checkbox"
                                                class="pick-consumable w-5 h-5 rounded border-gray-300 text-[#1CA7B6] focus:ring-[#1CA7B6]"
                                                data-id="{{ $c->id }}" data-name="{{ $c->name }}" data-stock="{{ $c->stock }}">
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
                        <button type="button" id="btnCancelModal"
                            class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                            Batal
                        </button>

                        <button type="button" id="btnAddConsumable"
                            class="text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:opacity-90 transition"
                            style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                            + Tambahkan
                        </button>
                    </div>

                </div>

            </div>
        </div>

        {{-- ================= MODAL HAPUS ITEM ================= --}}
        <div id="deleteItemModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[10002] p-3 sm:p-4">
            <div class="w-[calc(100%-1.5rem)] sm:w-11/12 max-w-sm bg-white rounded-2xl shadow-2xl p-5 sm:p-6 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </div>
                </div>

                <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-2">Hapus Item?</h3>
                <p class="text-xs sm:text-sm text-gray-500 mb-1">Anda yakin ingin menghapus</p>
                <p id="deleteItemNameModal" class="text-xs sm:text-sm font-semibold text-[#1CA7B6] mb-5"></p>

                <div class="flex gap-3">
                    <button id="cancelDeleteItem" class="flex-1 px-5 py-2.5 bg-[#dcdcdc] text-gray-700 rounded-xl text-xs sm:text-sm font-semibold hover:bg-[#c5c5c5] transition">
                        Batal
                    </button>
                    <button id="confirmDeleteItem" class="flex-1 px-5 py-2.5 bg-red-500 text-white rounded-xl text-xs sm:text-sm font-semibold hover:bg-red-600 transition">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>

        <style>
            #notifWrap,
            #modalNotifWrap {
                animation: notifSlideIn 0.4s ease-out;
            }
            @keyframes notifSlideIn {
                from { opacity: 0; transform: translateX(-40px); }
                to   { opacity: 1; transform: translateX(0); }
            }
            #notifWrap.hiding,
            #modalNotifWrap.hiding {
                animation: notifSlideOut 0.35s ease-in forwards;
            }
            @keyframes notifSlideOut {
                from { opacity: 1; transform: translateX(0); }
                to   { opacity: 0; transform: translateX(60px); }
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
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }
        </style>

        {{-- ================= SCRIPT ================= --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                let index = {{ $transaction->items->count() }};
                const form = document.getElementById('updateForm');

                // ================= ELEMEN NOTIF HALAMAN UTAMA =================
                const notifWrap = document.getElementById('notifWrap');
                const notifBox = document.getElementById('notifBox');
                const notifIcon = document.getElementById('notifIcon');
                const notifText = document.getElementById('notifText');
                const notifBar = document.getElementById('notifBar');
                const notifClose = document.getElementById('notifClose');
                let notifTimer = null;

                // ================= ELEMEN NOTIF MODAL =================
                const modalNotifWrap = document.getElementById('modalNotifWrap');
                const modalNotifBox = document.getElementById('modalNotifBox');
                const modalNotifIcon = document.getElementById('modalNotifIcon');
                const modalNotifText = document.getElementById('modalNotifText');
                const modalNotifBar = document.getElementById('modalNotifBar');
                const modalNotifClose = document.getElementById('modalNotifClose');
                let modalNotifTimer = null;

                // ================= FUNGSI NOTIF HALAMAN UTAMA =================
                window.showNotif = function (message, type) {
                    if (notifTimer) clearTimeout(notifTimer);
                    notifWrap.classList.remove('hidden', 'hiding');

                    if (type === 'success') {
                        notifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-emerald-50 border-emerald-200 text-emerald-800';
                        notifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-emerald-100';
                        notifIcon.innerHTML = '<svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>';
                        notifBar.style.background = '#34d399';
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
                };

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

                // ================= FUNGSI NOTIF MODAL =================
                function showModalNotif(message, type) {
                    if (modalNotifTimer) clearTimeout(modalNotifTimer);
                    modalNotifWrap.classList.remove('hidden', 'hiding');

                    if (type === 'success') {
                        modalNotifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-emerald-50 border-emerald-200 text-emerald-800';
                        modalNotifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-emerald-100';
                        modalNotifIcon.innerHTML = '<svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>';
                        modalNotifBar.style.background = '#34d399';
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

                // ================= SESSION NOTIF =================
                @if(session('success'))
                    window.showNotif('{{ session("success") }}', 'success');
                @endif
                @if(session('error'))
                    window.showNotif('{{ session("error") }}', 'error');
                @endif


                // ================= REFRESH NO =================
                function refreshNo() {
                    document.querySelectorAll('#tableConsumables tbody tr')
                        .forEach((row, i) => {
                            const noTd = row.querySelector('.no');
                            if (noTd) noTd.innerText = i + 1;
                        });
                }

                // ================= AMBIL ID YANG MASIH ADA DI TABEL UTAMA =================
                function getExistingIds() {
                    const ids = new Set();
                    document.querySelectorAll('#tableConsumables tbody tr').forEach(row => {
                        if (row.dataset.id) ids.add(row.dataset.id);
                    });
                    return ids;
                }

                // ================= UPDATE QTY =================
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
                        window.showNotif("Stock " + itemName + " hanya tersedia " + stock, "warning");
                        row.classList.add('row-error');
                        input.value = stock;
                        const hiddenQty = row.querySelector('.hidden-qty');
                        if (hiddenQty) hiddenQty.value = stock;
                        return;
                    }

                    const hiddenQty = row.querySelector('.hidden-qty');
                    if (hiddenQty) {
                        hiddenQty.value = qty;
                    }
                };

                // ================= REMOVE ROW — GAK USAH SENTUH POPUP =================
                let rowToDelete = null;
                const deleteItemModal = document.getElementById('deleteItemModal');
                const deleteItemNameModal = document.getElementById('deleteItemNameModal');

                window.removeRow = function (btn) {
                    const row = btn.closest('tr');
                    const itemName = row.querySelector('.item-name').textContent.trim();

                    rowToDelete = row;
                    deleteItemNameModal.textContent = itemName;

                    deleteItemModal.classList.remove('hidden');
                    deleteItemModal.classList.add('flex');
                };

                function closeDeleteItemModal() {
                    deleteItemModal.classList.add('hidden');
                    deleteItemModal.classList.remove('flex');
                    rowToDelete = null;
                }

                document.getElementById('cancelDeleteItem').addEventListener('click', closeDeleteItemModal);

                deleteItemModal.addEventListener('click', function(e) {
                    if (e.target === deleteItemModal) closeDeleteItemModal();
                });

                document.getElementById('confirmDeleteItem').addEventListener('click', function() {
                    if (rowToDelete) {
                        const itemName = rowToDelete.querySelector('.item-name').textContent.trim();
                        rowToDelete.remove();
                        refreshNo();
                        window.showNotif(itemName + " berhasil dihapus", "success");
                    }
                    closeDeleteItemModal();
                });


                // ================= MODAL LOGIC — BACA LANGSUNG DARI TABEL, GAK PAKE FLAG =================
                const modal = document.getElementById('consumableModal');
                const openBtn = document.getElementById('openConsumableBtn');
                const closeBtn = document.getElementById('closeConsumableBtn');
                const cancelBtn = document.getElementById('btnCancelModal');
                const addBtn = document.getElementById('btnAddConsumable');
                const searchInput = document.getElementById('searchConsumableModal');

                // —— BUKA MODAL: baca ID dari tabel utama, hide yang match ——
                if (openBtn && modal) {
                    openBtn.addEventListener('click', function () {
                        const existingIds = getExistingIds();

                        document.querySelectorAll('#popupTable tbody tr.cons-row').forEach(row => {
                            const checkbox = row.querySelector('.pick-consumable');
                            if (!checkbox) return;

                            const id = checkbox.dataset.id;
                            checkbox.checked = false;

                            const qtyInput = row.querySelector('.qty-input');
                            if (qtyInput) qtyInput.value = 1;

                            if (existingIds.has(id)) {
                                row.style.display = 'none';
                            } else {
                                row.style.display = '';
                            }
                        });

                        if (searchInput) searchInput.value = '';
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    });
                }

                function closeModal() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                closeBtn.addEventListener('click', closeModal);
                cancelBtn.addEventListener('click', closeModal);

                modal.addEventListener('click', function (e) {
                    if (e.target === modal) closeModal();
                });

                // —— SEARCH: baca ID dari tabel utama, gabungkan dengan filter keyword ——
                if (searchInput) {
                    searchInput.addEventListener('keyup', function () {
                        const keyword = this.value.toLowerCase();
                        const existingIds = getExistingIds();

                        document.querySelectorAll('#popupTable tbody tr.cons-row').forEach(row => {
                            const checkbox = row.querySelector('.pick-consumable');
                            if (!checkbox) return;

                            const id = checkbox.dataset.id;
                            const name = row.dataset.name;

                            if (existingIds.has(id)) {
                                row.style.display = 'none';
                            } else {
                                row.style.display = name.includes(keyword) ? '' : 'none';
                            }
                        });
                    });
                }

                // ================= ADD CONSUMABLE =================
                addBtn.addEventListener('click', function () {
                    const selectedItems = document.querySelectorAll('.pick-consumable:checked');

                    if (selectedItems.length === 0) {
                        showModalNotif("Pilih minimal 1 consumable", "error");
                        return;
                    }

                    let hasError = false;

                    selectedItems.forEach(selected => {
                        const rowPopup = selected.closest('tr');
                        const name = selected.dataset.name;
                        const stock = parseInt(selected.dataset.stock);
                        const qty = parseInt(rowPopup.querySelector('.qty-input').value);

                        if (qty > stock) {
                            showModalNotif("Stock " + name + " hanya tersedia " + stock, "error");
                            hasError = true;
                        }
                    });

                    if (hasError) return;

                    let addedCount = 0;
                    let updatedCount = 0;

                    selectedItems.forEach(selected => {
                        const rowPopup = selected.closest('tr');
                        const id = selected.dataset.id;
                        const name = selected.dataset.name;
                        const stock = parseInt(selected.dataset.stock);
                        const image = rowPopup.querySelector('img').src;
                        const qty = parseInt(rowPopup.querySelector('.qty-input').value);

                        const exist = document.querySelector(`#tableConsumables tr[data-id="${id}"]`);

                        if (exist) {
                            exist.querySelector('.qty-input-main').value = qty;
                            exist.querySelector('.qty-input-main').max = stock;
                            const hiddenQtyExist = exist.querySelector('.hidden-qty');
                            if (hiddenQtyExist) hiddenQtyExist.value = qty;
                            const stockDisplay = exist.querySelector('.stock-display');
                            if (stockDisplay) stockDisplay.textContent = stock;
                            exist.dataset.stock = stock;
                            updatedCount++;
                        } else {
                            const html = `
                                <tr data-id="${id}" data-stock="${stock}" class="hover:bg-gray-50 transition align-middle border-b border-gray-100">
                                    <td class="text-center py-4 px-4 text-gray-600 no font-medium"></td>
                                    <td class="text-center py-2 px-4">
                                        <img src="${image}" class="w-12 h-12 object-cover rounded-lg mx-auto shadow-sm border">
                                    </td>
                                    <td class="text-center py-4 px-4 font-medium text-gray-800 item-name">${name}</td>
                                    <td class="text-center py-4 px-4 font-semibold text-blue-600 stock-display">${stock}</td>
                                    <td class="text-center py-4 px-4">
                                        <input type="number" value="${qty}" min="1" max="${stock}" onchange="updateQty(this)"
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
                            addedCount++;
                        }

                        selected.checked = false;
                        rowPopup.querySelector('.qty-input').value = 1;
                        rowPopup.style.display = 'none';
                    });

                    refreshNo();
                    closeModal();

                    if (addedCount > 0 && updatedCount === 0) {
                        window.showNotif(addedCount + " consumable berhasil ditambahkan", "success");
                    } else if (addedCount > 0 && updatedCount > 0) {
                        window.showNotif(addedCount + " consumable ditambahkan, " + updatedCount + " diperbarui", "success");
                    } else if (updatedCount > 0 && addedCount === 0) {
                        window.showNotif(updatedCount + " consumable berhasil diperbarui", "success");
                    }
                });


                // ================= VALIDASI SAVE =================
                document.getElementById('btnSave').addEventListener('click', function () {
                    const items = document.querySelectorAll('#tableConsumables tbody tr');

                    document.querySelectorAll('.row-error').forEach(row => {
                        row.classList.remove('row-error');
                    });

                    if (items.length === 0) {
                        window.showNotif("Tambahkan minimal 1 consumable", "error");
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
                        window.showNotif(stockErrors[0], "error");
                        const firstErrorRow = document.querySelector('.row-error');
                        if (firstErrorRow) {
                            firstErrorRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        return;
                    }

                    form.submit();
                });


                // ================= ESC KEY =================
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        closeModal();
                        closeDeleteItemModal();
                    }
                });

            });
        </script>
@endsection