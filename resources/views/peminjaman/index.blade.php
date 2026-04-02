@extends('layouts.app')

@section('content')
    <div x-data="{openReturn: null}" class="w-full min-h-screen flex flex-col">

        {{-- ================= HEADER ================= --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-3xl font-bold text-[#1CA7B6] tracking-tight">Peminjaman Tools</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data transaksi peminjaman alat</p>
            </div>

            <a href="{{ route('peminjaman.create') }}"
                class="text-white px-6 py-2.5 rounded-xl font-bold shadow-md hover:opacity-90 transition-all duration-200 tracking-wide"
                style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                + Pinjam Tools
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

        {{-- ================= FILTER ================= --}}
        <form method="GET" action="{{ route('peminjaman.index') }}"
            class="mb-6 p-4 rounded-2xl shadow-md flex flex-wrap gap-4 items-center min-h-[80px]"
            style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">

            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                    placeholder="Masukan nama peminjam atau barang"
                    class="w-full bg-white border-0 rounded-xl px-4 py-2.5 pr-8 text-sm shadow-inner focus:ring-2 focus:ring-white focus:outline-none">

                {{-- Tombol X Langsung Reset --}}
                <button type="button"
                    onclick="document.getElementById('searchInput').value=''; this.closest('form').submit();"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition"
                    style="display:{{ request('search') ? 'block' : 'none' }}" id="clearSearchBtn">
                    X
                </button>
            </div>

            <label class="text-white font-semibold text-sm">Tanggal</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}"
                class="bg-white border-0 rounded-xl px-4 py-2.5 text-sm shadow-inner focus:outline-none">

            <span class="text-white font-bold text-sm">s/d</span>

            <input type="date" name="end_date" value="{{ request('end_date') }}"
                class="bg-white border-0 rounded-xl px-4 py-2.5 text-sm shadow-inner focus:outline-none">

            <button type="submit"
                class="bg-white text-[#1CA7B6] px-5 py-2.5 rounded-xl font-bold shadow-sm hover:bg-gray-100 transition text-sm">
                Filter
            </button>

            <a href="{{ route('peminjaman.index') }}" class="text-white underline text-sm hover:text-gray-100 transition">
                Reset
            </a>
        </form>

        {{-- ================= TABLE RIWAYAT ================= --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-white text-xs uppercase tracking-wider"
                        style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                        <th class="py-4 px-6 font-semibold text-center" style="width: 50px;">NO</th>
                        <th class="py-4 px-6 font-semibold text-left" style="width: 120px;">KODE</th>
                        <th class="py-4 px-6 font-semibold text-left" style="width: 150px;">TGL PINJAM</th>
                        <th class="py-4 px-6 font-semibold text-left" style="width: 200px;">KARYAWAN</th>
                        <th class="py-4 px-6 font-semibold text-left">NAMA TOOLS</th>
                        <th class="py-4 px-6 font-semibold text-left" style="width: 150px;">NO SERI</th>
                        <th class="py-4 px-6 font-semibold text-center" style="width: 180px;">AKSI</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($transactions as $transaction)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6 text-center font-medium text-gray-600">
                                {{ $loop->iteration }}
                            </td>
                            <td class="py-4 px-6 font-bold text-[#1CA7B6]">
                                {{ $transaction->kode_transaksi ?? $transaction->transaction_code }}
                            </td>
                            <td class="py-4 px-6 text-gray-700">
                                {{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}
                            </td>
                            <td class="py-4 px-6 text-gray-700">
                                {{ $transaction->borrower_name }}
                            </td>
                            <td class="py-4 px-6 text-gray-700">
                                @foreach ($transaction->items as $item)
                                    <div>{{ $item->toolkit->toolkit_name ?? '-' }}</div>
                                @endforeach
                            </td>
                            <td class="py-4 px-6 text-gray-500 font-mono text-xs">
                                @foreach ($transaction->items as $item)
                                    <div>{{ $item->serial->serial_number ?? '-' }}</div>
                                @endforeach
                            </td>
                            <td class="py-4 px-6 text-center">
                                <div class="flex justify-center gap-2 flex-wrap">
                                    @if (!$transaction->is_confirm)
                                        <a href="{{ route('peminjaman.edit', $transaction->id) }}"
                                            class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg font-semibold text-xs transition">
                                            Edit
                                        </a>

                                        {{-- Form Hapus Tersembunyi --}}
                                        <form action="{{ route('peminjaman.destroy', $transaction->id) }}" method="POST"
                                            id="deleteForm_{{ $transaction->id }}" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>

                                        <button type="button"
                                            onclick="openDeleteModal({{ $transaction->id }}, '{{ $transaction->kode_transaksi ?? $transaction->transaction_code }}')"
                                            class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg font-semibold text-xs transition">
                                            Hapus
                                        </button>

                                        <form action="{{ route('peminjaman.confirm', $transaction->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button class="text-white px-3 py-1.5 rounded-lg font-semibold text-xs transition"
                                                style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                                                Confirm
                                            </button>
                                        </form>
                                    @else
                                        @if ($transaction->items->whereNull('return_date')->count() > 0)
                                            <button @click="openReturn = '{{ $transaction->id }}'"
                                                class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-1.5 rounded-lg font-semibold text-xs transition shadow-sm">
                                                Pengembalian
                                            </button>
                                        @endif

                                        <span
                                            class="bg-green-100 text-green-700 px-3 py-1.5 rounded-lg font-semibold text-xs inline-block">
                                            ✔ Confirmed
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400 italic">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                        </path>
                                    </svg>
                                    <span>Belum ada riwayat peminjaman</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ================= POPUP PENGEMBALIAN ================= --}}
        @foreach ($transactions as $transaction)
            @if ($transaction->is_confirm)
                <div x-show="openReturn === '{{ $transaction->id }}'" x-cloak x-data="{ selected: [] }"
                    class="fixed inset-0 bg-black/40 backdrop-blur-md flex items-center justify-center z-[1000] p-4">

                    <div class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

                        <form action="{{ route('peminjaman.return.process', $transaction->id) }}" method="POST">
                            @csrf

                            {{-- HEADER MODAL --}}
                            <div class="px-6 py-4 flex justify-between items-center text-white"
                                style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                                <div>
                                    <h3 class="text-lg font-bold">Proses Pengembalian</h3>
                                    <p class="text-sm text-white/80">Pilih alat yang akan dikembalikan</p>
                                </div>
                                <button type="button" @click="openReturn = null"
                                    class="text-2xl text-white/80 hover:text-white transition">
                                    ✕
                                </button>
                            </div>

                            <div class="p-6 overflow-auto flex-1 bg-gray-50">
                                {{-- INFO SECTION --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Karyawan</label>
                                        <input type="text" value="{{ $transaction->borrower_name }}" readonly
                                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-inner focus:outline-none text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Pengembalian</label>
                                        <input type="date" name="return_date" value="{{ date('Y-m-d') }}" required
                                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-inner focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none text-sm">
                                    </div>
                                </div>

                                {{-- TABLE ITEM --}}
                                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                                    <table class="w-full text-sm">
                                        {{-- HEADER TABEL GRADASI --}}
                                        <thead class="text-white text-xs uppercase tracking-wider"
                                            style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                                            <tr class="text-white text-xs uppercase tracking-wider"
                                                style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                                                <th class="py-3 px-4 text-center w-16 font-semibold">Pilih</th>
                                                <th class="py-3 px-4 text-left font-semibold">Nama Alat</th>
                                                <th class="py-3 px-4 text-left font-semibold">No Seri</th>
                                                <th class="py-3 px-4 text-left font-semibold">Kondisi</th>
                                                <th class="py-3 px-4 text-left font-semibold">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-100">
                                            @foreach ($transaction->items->whereNull('return_date') as $item)
                                                <tr class="hover:bg-gray-50 transition">
                                                    <td class="px-4 py-3 text-center">
                                                        <input type="checkbox" class="w-4 h-4 accent-[#1CA7B6]" x-model="selected"
                                                            value="{{ $item->id }}" name="items[{{ $item->id }}][id]">
                                                    </td>

                                                    <td class="px-4 py-3 font-medium text-gray-800">
                                                        {{ $item->toolkit->toolkit_name ?? '-' }}
                                                    </td>

                                                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">
                                                        {{ $item->serial->serial_number ?? '-' }}
                                                    </td>

                                                    <td class="px-4 py-3">
                                                        <select name="items[{{ $item->id }}][condition]"
                                                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                                                            :disabled="!selected.includes('{{ $item->id }}')">
                                                            <option value="BAIK">Baik</option>
                                                            <option value="MAINTENANCE">Butuh Perbaikan</option>
                                                            <option value="RUSAK">Rusak</option>
                                                        </select>
                                                    </td>

                                                    <td class="px-4 py-3">
                                                        <input type="text" name="items[{{ $item->id }}][note]"
                                                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm"
                                                            :disabled="!selected.includes('{{ $item->id }}')">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- FOOTER --}}
                            <div class="px-6 py-4 bg-white border-t border-gray-100 flex justify-end gap-3">
                                <button type="button" @click="openReturn = null"
                                    class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                                    Batal
                                </button>

                                <button type="submit" :disabled="selected.length === 0"
                                    :class="selected.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'hover:opacity-90'"
                                    class="px-5 py-2.5 text-white rounded-xl font-semibold text-sm shadow-md transition"
                                    style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                                    Kembalikan <span x-text="selected.length"></span> Alat
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            @endif
        @endforeach


        {{-- ================= MODAL HAPUS TRANSAKSI ================= --}}
        <div id="deleteModal"
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

                <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-2">Hapus Transaksi?</h3>
                <p class="text-xs sm:text-sm text-gray-500 mb-1">Anda yakin ingin menghapus</p>
                <p id="deleteNameModal" class="text-xs sm:text-sm font-semibold text-[#1CA7B6] mb-5"></p>

                <div class="flex gap-3">
                    <button id="cancelDelete"
                        class="flex-1 px-5 py-2.5 bg-[#dcdcdc] text-gray-700 rounded-xl text-xs sm:text-sm font-semibold hover:bg-[#c5c5c5] transition">
                        Batal
                    </button>
                    <button id="confirmDelete"
                        class="flex-1 px-5 py-2.5 bg-red-500 text-white rounded-xl text-xs sm:text-sm font-semibold hover:bg-red-600 transition">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>


        {{-- ================= STYLE ================= --}}
        @push('styles')
            <style>
                [x-cloak] {
                    display: none !important;
                }

                #notifWrap {
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

                #notifWrap.hiding {
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

                #notifBar {
                    transition: width 3.5s linear;
                }
            </style>
        @endpush


        {{-- ================= SCRIPT ================= --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                // ================= NOTIF SYSTEM =================
                const notifWrap = document.getElementById('notifWrap');
                const notifBox = document.getElementById('notifBox');
                const notifIcon = document.getElementById('notifIcon');
                const notifText = document.getElementById('notifText');
                const notifBar = document.getElementById('notifBar');
                const notifClose = document.getElementById('notifClose');
                let notifTimer = null;

                window.showNotif = function (message, type) {
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


                // ================= MODAL HAPUS TRANSAKSI =================
                const deleteModal = document.getElementById('deleteModal');
                const deleteNameModal = document.getElementById('deleteNameModal');
                let pendingDeleteId = null;

                window.openDeleteModal = function (id, code) {
                    pendingDeleteId = id;
                    deleteNameModal.textContent = 'Transaksi ' + code;

                    deleteModal.classList.remove('hidden');
                    deleteModal.classList.add('flex');
                };

                function closeDeleteModal() {
                    deleteModal.classList.add('hidden');
                    deleteModal.classList.remove('flex');
                    pendingDeleteId = null;
                }

                document.getElementById('cancelDelete').addEventListener('click', closeDeleteModal);

                deleteModal.addEventListener('click', function (e) {
                    if (e.target === deleteModal) closeDeleteModal();
                });

                document.getElementById('confirmDelete').addEventListener('click', function () {
                    if (pendingDeleteId) {
                        const form = document.getElementById('deleteForm_' + pendingDeleteId);
                        if (form) {
                            form.submit();
                        }
                    }
                    closeDeleteModal();
                });

                // ================= ESC KEY GLOBAL =================
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') {
                        closeDeleteModal();
                    }
                });

            });
        </script>
@endsection