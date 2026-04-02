@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen flex flex-col">

    {{-- ================= TITLE ================= --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-[#1CA7B6] tracking-tight">Edit Peminjaman Tools</h2>
            <p class="text-sm text-gray-500 mt-1">Edit Proses Peminjaman dan Daftar Tools yang dipinjam</p>
        </div>

        <a href="{{ route('peminjaman.index') }}"
            class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition">
            ← Kembali
        </a>
    </div>

    {{-- ================= NOTIF TOAST ================= --}}
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

        {{-- ================= FORM UPDATE ================= --}}
        <form id="updateForm"
            action="{{ route('peminjaman.update', $transaction->id) }}"
            method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Kolom 1: Nama Peminjam --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Peminjam</label>
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

                {{-- Kolom 3: Spacer --}}
                <div class="hidden md:block">
                    <label class="block text-sm font-bold text-gray-700 mb-2">&nbsp;</label>
                    <div class="w-full px-4 py-2.5">&nbsp;</div>
                </div>

                {{-- Kolom 1: Nama Client --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Client</label>
                    <input type="text"
                        name="client_name"
                        value="{{ $transaction->client_name }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                </div>

                {{-- Kolom 2: Proyek --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Proyek</label>
                    <input type="text"
                        name="project"
                        value="{{ $transaction->project }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                </div>

                {{-- Kolom 3: Keperluan --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Keperluan</label>
                    <input type="text"
                        name="purpose"
                        value="{{ $transaction->purpose }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                </div>

            </div>
        </form>
        {{-- END FORM UPDATE --}}


        {{-- ================= DAFTAR TOOLS ================= --}}
        <div class="space-y-4">

            <div class="flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-lg">Daftar Alat yang Di Edit</h3>

                <button type="button"
                    id="openToolsBtn"
                    class="text-white px-5 py-2 rounded-xl font-bold shadow-md hover:opacity-90 transition-all duration-200 text-sm tracking-wide"
                    style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                    + Pilih Tools
                </button>
            </div>

            <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                <table class="w-full text-sm" id="tableTools">
                    <thead>
                        <tr class="text-white text-xs uppercase tracking-wider"
                            style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                            <th class="py-3 px-4 font-semibold text-center w-10">NO</th>
                            <th class="py-3 px-4 font-semibold text-center w-20">Image</th>
                            <th class="py-3 px-4 font-semibold text-center">Nama Tools</th>
                            <th class="py-3 px-4 font-semibold text-center">No Seri</th>
                            <th class="py-3 px-4 font-semibold text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($transaction->items as $item)
                        <tr data-id="{{ $item->id }}" data-name="{{ $item->toolkit->toolkit_name ?? '-' }}" class="hover:bg-gray-50 transition">
                            <td class="text-center py-4 px-4 text-gray-600 no font-medium">
                                {{ $loop->iteration }}
                            </td>

                            <td class="text-center py-2 px-4">
                                @if($item->toolkit && $item->toolkit->image)
                                <img src="{{ asset('storage/'.$item->toolkit->image) }}"
                                    class="w-12 h-12 object-contain mx-auto rounded-md shadow-sm border">
                                @else
                                <div class="w-12 h-12 bg-gray-200 rounded-md mx-auto flex items-center justify-center text-gray-400 text-xs">
                                    No Img
                                </div>
                                @endif
                            </td>

                            <td class="text-center py-4 px-4 font-medium text-gray-800">
                                {{ $item->toolkit->toolkit_name ?? '-' }}
                            </td>

                            <td class="text-center py-4 px-4 text-gray-600 font-mono">
                                {{ $item->serial->serial_number ?? '-' }}
                            </td>

                            <td class="text-center py-4 px-4">
                                {{-- Form tersembunyi untuk DELETE --}}
                                <form action="{{ route('peminjaman.item.destroy', $item->id) }}"
                                    method="POST"
                                    id="deleteForm_{{ $item->id }}"
                                    class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>

                                <button type="button"
                                    onclick="openDeleteModal({{ $item->id }})"
                                    class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg font-semibold text-xs transition shadow-sm">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-400 py-10 bg-white">
                                Belum ada tools yang dipinjam.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>


        {{-- ================= SAVE BUTTON ================= --}}
        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button type="submit"
                form="updateForm"
                class="text-white px-8 py-3 rounded-xl font-bold shadow-md hover:opacity-90 transition-all duration-200 tracking-wide"
                style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                Save Transaksi
            </button>
        </div>

    </div>


    {{-- ================= MODAL TOOLS ================= --}}
    <div id="toolsModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">

        <div class="bg-white w-11/12 max-w-3xl rounded-2xl shadow-2xl relative overflow-hidden flex flex-col">

            <div class="px-6 py-4 flex justify-between items-center text-white"
                style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                <div>
                    <h3 class="text-lg font-bold">Pilih Tools Tersedia</h3>
                </div>
                <button type="button"
                    id="closeToolsBtn"
                    class="text-2xl text-white/80 hover:text-white transition">
                    ✕
                </button>
            </div>

            <div class="p-6 overflow-auto flex-1 bg-gray-50">
                <div class="mb-6">
                    <input type="text"
                        id="searchToolsModal"
                        placeholder="Cari nama tools atau no seri..."
                        class="w-full bg-white border-0 rounded-xl px-4 py-3 shadow-inner focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none text-sm">
                </div>

                <form action="{{ route('peminjaman.item.add', $transaction->id) }}"
                    method="POST">
                    @csrf

                    <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm max-h-[350px] overflow-y-auto">

                        <table class="w-full text-sm">

                            <thead class="sticky top-0 text-white text-xs uppercase tracking-wider"
                                style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                                <tr>
                                    <th class="py-3 px-4 w-10"></th>
                                    <th class="py-3 px-4 text-left font-semibold">Nama Tools</th>
                                    <th class="py-3 px-4 text-center font-semibold">No Seri</th>
                                    <th class="py-3 px-4 text-center font-semibold">Image</th>
                                </tr>
                            </thead>

                            <tbody id="toolsTableBody" class="bg-white divide-y divide-gray-100">

                                @forelse ($serials as $serial)
                                <tr class="hover:bg-gray-50 transition cursor-pointer">

                                    <td class="py-3 px-4 text-center">
                                        <input type="checkbox"
                                            name="serial_ids[]"
                                            value="{{ $serial->id }}"
                                            class="w-5 h-5 rounded border-gray-300 text-[#1CA7B6] focus:ring-[#1CA7B6]">
                                    </td>

                                    <td class="py-3 px-4 font-medium text-gray-800">
                                        {{ $serial->toolkit->toolkit_name }}
                                    </td>

                                    <td class="py-3 px-4 text-center text-gray-600 font-mono text-xs">
                                        {{ $serial->serial_number }}
                                    </td>

                                    <td class="py-3 px-4 text-center">
                                        <img src="{{ $serial->toolkit->image
                                            ? asset('storage/'.$serial->toolkit->image)
                                            : asset('images/no-image.png') }}"
                                            class="w-10 h-10 object-contain mx-auto rounded shadow-sm border">
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4"
                                        class="text-center text-gray-400 py-10">
                                        Tidak ada tools tersedia
                                    </td>
                                </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    <div class="flex justify-end gap-3 pt-6 mt-4 bg-transparent">
                        <button type="button"
                            id="btnCancelToolsModal"
                            class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                            Batal
                        </button>

                        <button type="submit"
                            class="text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:opacity-90 transition"
                            style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                            + Tambahkan
                        </button>

                    </div>

                </form>

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


    {{-- ================= STYLE NOTIF ================= --}}
    <style>
        #notifWrap {
            animation: notifSlideIn 0.4s ease-out;
        }
        @keyframes notifSlideIn {
            from { opacity: 0; transform: translateX(-40px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        #notifWrap.hiding {
            animation: notifSlideOut 0.35s ease-in forwards;
        }
        @keyframes notifSlideOut {
            from { opacity: 1; transform: translateX(0); }
            to   { opacity: 0; transform: translateX(60px); }
        }
        #notifBar {
            transition: width 3.5s linear;
        }
    </style>

    {{-- ================= SCRIPT ================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('toolsModal');
            const openBtn = document.getElementById('openToolsBtn');
            const closeBtns = document.querySelectorAll('#closeToolsBtn');
            const cancelToolsBtn = document.getElementById('btnCancelToolsModal');
            const searchInput = document.getElementById('searchToolsModal');

            // ================= NOTIF SYSTEM =================
            const notifWrap = document.getElementById('notifWrap');
            const notifBox = document.getElementById('notifBox');
            const notifIcon = document.getElementById('notifIcon');
            const notifText = document.getElementById('notifText');
            const notifBar = document.getElementById('notifBar');
            const notifClose = document.getElementById('notifClose');
            let notifTimer = null;

            window.showNotif = function(message, type) {
                if (notifTimer) clearTimeout(notifTimer);
                notifWrap.classList.remove('hidden', 'hiding');

                if (type === 'success') {
                    notifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-emerald-50 border-emerald-200 text-emerald-800';
                    notifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-emerald-100';
                    notifIcon.innerHTML = '<svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>';
                    notifBar.style.background = '#34d399';
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

            @if(session('success'))
                window.showNotif('{{ session("success") }}', 'success');
            @endif
            @if(session('error'))
                window.showNotif('{{ session("error") }}', 'error');
            @endif


            // ================= MODAL HAPUS ITEM =================
            const deleteItemModal = document.getElementById('deleteItemModal');
            const deleteItemNameModal = document.getElementById('deleteItemNameModal');
            let pendingDeleteId = null;

            window.openDeleteModal = function(itemId) {
                const row = document.querySelector(`#tableTools tr[data-id="${itemId}"]`);
                if (!row) return;

                pendingDeleteId = itemId;
                deleteItemNameModal.textContent = row.dataset.name;

                deleteItemModal.classList.remove('hidden');
                deleteItemModal.classList.add('flex');
            };

            function closeDeleteItemModal() {
                deleteItemModal.classList.add('hidden');
                deleteItemModal.classList.remove('flex');
                pendingDeleteId = null;
            }

            document.getElementById('cancelDeleteItem').addEventListener('click', closeDeleteItemModal);

            deleteItemModal.addEventListener('click', function(e) {
                if (e.target === deleteItemModal) closeDeleteItemModal();
            });

            document.getElementById('confirmDeleteItem').addEventListener('click', function() {
                if (pendingDeleteId) {
                    const form = document.getElementById('deleteForm_' + pendingDeleteId);
                    if (form) {
                        form.submit();
                    }
                }
                closeDeleteItemModal();
            });


            // ================= MODAL TOOLS =================
            if (openBtn && modal) {
                openBtn.addEventListener('click', function() {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });
            }

            function closeToolsModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');

                if (searchInput) {
                    searchInput.value = '';
                    document.querySelectorAll('#toolsTableBody tr').forEach(row => {
                        row.style.display = '';
                    });
                }
            }

            closeBtns.forEach(btn => {
                btn.addEventListener('click', closeToolsModal);
            });

            if (cancelToolsBtn) {
                cancelToolsBtn.addEventListener('click', closeToolsModal);
            }

            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeToolsModal();
            });

            // ================= LIVE SEARCH =================
            if (searchInput) {
                searchInput.addEventListener('keyup', function() {
                    let keyword = this.value.toLowerCase();
                    document.querySelectorAll('#toolsTableBody tr').forEach(function(row) {
                        let text = row.innerText.toLowerCase();
                        row.style.display = text.includes(keyword) ? '' : 'none';
                    });
                });
            }

            // ================= ESC KEY =================
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeToolsModal();
                    closeDeleteItemModal();
                }
            });

        });
    </script>
@endsection