@extends('layouts.app')

@section('content')

    <div class="relative max-w-7xl mx-auto pb-40" x-data="{ openReturn: null, deleteConfirm: false, deleteId: null, deleteCode: '' }">


        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6">

            <div>
                <h1 class="text-3xl font-bold text-[#5EA6FF] tracking-tight">
                    Transaksi Consumable
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Kelola Transaksi dan pengembalian Consumable
                </p>
            </div>

            <div class="flex gap-3">

                {{-- TOMBOL PERMINTAAN (UKURAN DIKECILKAN) --}}
                <a href="{{ route('transaksi.create') }}"
                    class="group inline-flex items-center px-4 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all duration-200 tracking-wide border-2 border-[#5EA6FF] bg-white text-sm text-[#5EA6FF] hover:bg-[#5EA6FF] hover:text-white hover:shadow-blue-500/40 hover:-translate-y-0.5">
                    
                    <svg class="w-4 h-4 mr-2 transition-transform duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    
                    Permintaan Consumable
                </a>

            </div>
        </div>

        {{-- ================= NOTIF TOAST ================= --}}
        <div id="notifWrap" class="hidden mb-4">
            <div id="notifBox"
                class="relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border">
                <div id="notifIcon" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"></div>
                <p id="notifText" class="text-sm font-medium"></p>
                <button id="notifClose" class="ml-auto flex-shrink-0 opacity-50 hover:opacity-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <div id="notifBar" class="absolute bottom-0 left-0 h-1 rounded-b-2xl" style="width:0%"></div>
            </div>
        </div>
        
        {{-- FILTER --}}
        <form method="GET" action="{{ route('transaksi.index') }}" class="mb-6">

            <div class="p-4 rounded-2xl shadow-md flex flex-wrap items-center gap-3"
                 style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">

                <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari Barang atau Karyawan..."
                    class="flex-1 min-w-[200px] bg-white border-0 rounded-xl shadow-inner px-4 py-2.5 text-sm focus:ring-2 focus:ring-white focus:outline-none">

                <span class="text-white font-semibold text-sm">Tanggal</span>

                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="px-4 py-2.5 rounded-xl bg-white text-gray-700 text-sm shadow-inner border-0 focus:outline-none">

                <span class="text-white font-bold text-sm">s/d</span>

                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="px-4 py-2.5 rounded-xl bg-white text-gray-700 text-sm shadow-inner border-0 focus:outline-none">

                <button type="submit"
                    class="px-5 py-2.5 text-sm bg-white text-[#5EA6FF] font-bold rounded-xl shadow-sm hover:bg-gray-100 transition">
                    Filter
                </button>

                <a href="{{ route('transaksi.index') }}"
                    class="px-4 py-2 text-sm text-white underline hover:text-gray-100 transition">
                    Reset
                </a>

            </div>

        </form>


        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

            <table class="w-full text-sm">

                <thead>
                    <tr class="text-white text-xs uppercase tracking-wider"
                        style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">

                        <th class="py-4 px-6 font-semibold text-center">No</th>
                        <th class="py-4 px-6 font-semibold text-left">Kode</th>
                        <th class="py-4 px-6 font-semibold text-left">Tanggal</th>
                        <th class="py-4 px-6 font-semibold text-left">Karyawan</th>
                        <th class="py-4 px-6 font-semibold text-left">Consumable</th>
                        <th class="py-4 px-6 font-semibold text-center">Minta</th>
                        <th class="py-4 px-6 font-semibold text-center">Kembali</th>
                        <th class="py-4 px-6 font-semibold text-center">Terpakai</th>
                        <th class="py-4 px-6 font-semibold text-center">Aksi</th>

                    </tr>
                </thead>

                <tbody class="text-gray-700 divide-y divide-gray-100">

                    @forelse($transactions as $trx)

                        <tr class="hover:bg-gray-50 transition">

                            <td class="py-4 px-6 text-center font-medium text-gray-600">
                                {{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}
                            </td>

                            <td class="py-4 px-6 font-bold text-[#5EA6FF]">
                                {{ $trx->transaction_code }}
                            </td>

                            <td class="py-4 px-6 text-gray-700">
                                {{ \Carbon\Carbon::parse($trx->date)->format('d M Y') }}
                            </td>

                            <td class="py-4 px-6 text-gray-700">
                                {{ $trx->borrower_name }}
                            </td>

                            <td class="py-4 px-6 text-gray-700">

                                @foreach($trx->items as $item)
                                    <div>{{ $item->consumable->name }}</div>
                                @endforeach

                            </td>

                            <td class="py-4 px-6 text-center text-gray-700">

                                @foreach($trx->items as $item)
                                    <div>{{ $item->qty }} {{ $item->consumable->unit }}</div>
                                @endforeach

                            </td>

                            <td class="py-4 px-6 text-center text-gray-700">

                                @foreach($trx->items as $item)
                                    <div>{{ ($item->qty_return ?? 0) == 0 ? '-' : $item->qty_return }}</div>
                                @endforeach

                            </td>

                            <td class="py-4 px-6 text-center">

                                @foreach($trx->items as $item)

                                    @php
                                        $sisa = $item->qty - ($item->qty_return ?? 0);
                                    @endphp

                                    @if($sisa == $item->qty)

                                        <div class="text-yellow-600 font-semibold">-</div>

                                    @elseif($sisa == 0)

                                        <div class="text-green-600 font-semibold">Selesai</div>

                                    @else

                                        <div class="text-orange-600 font-semibold">
                                            {{ $sisa }}
                                        </div>

                                    @endif

                                @endforeach

                            </td>

                            <td class="py-4 px-6 text-center">

                                @if(!$trx->is_confirm)

                                    <div class="flex justify-center gap-2 items-center">
                                        
                                        {{-- TOMBOL EDIT (ICON) --}}
                                        <a href="{{ route('transaksi.edit', $trx->id) }}"
                                            class="p-2 rounded-lg text-gray-400 hover:text-blue-500 hover:bg-blue-50 transition"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                            </svg>
                                        </a>

                                        {{-- TOMBOL HAPUS (ICON) --}}
                                        <button type="button"
                                            @click="deleteId = '{{ $trx->id }}'; deleteCode = '{{ $trx->transaction_code }}'; deleteConfirm = true"
                                            class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition"
                                            title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>

                                        <form action="{{ route('transaksi.confirm', $trx->id) }}" method="POST">
                                            @csrf
                                            <button class="text-white px-3 py-1.5 rounded-lg font-semibold text-xs transition"
                                                style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">
                                                Confirm
                                            </button>
                                        </form>

                                    </div>

                                @else

                                    <div class="flex justify-center gap-2 flex-wrap">
                                        <button @click="openReturn = '{{ $trx->id }}'"
                                            class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-1.5 rounded-lg font-semibold text-xs transition shadow-sm">
                                            Pengembalian
                                        </button>

                                        <span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-lg font-semibold text-xs inline-block">
                                            ✔ Confirmed
                                        </span>
                                    </div>
                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9" class="py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    <span>Belum ada transaksi</span>
                                </div>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- ================= MODAL KONFIRMASI HAPUS ================= --}}
        <div x-show="deleteConfirm" x-cloak
            @click.self="deleteConfirm = false"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[1002] p-4">

            <div x-show="deleteConfirm"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-90"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-90"
                class="bg-white w-full max-w-sm rounded-2xl shadow-[0_15px 40px_rgba(0,0,0,0.25)] p-6 text-center"
                style="font-family: 'Plus Jakarta Sans', sans-serif;">

                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                        </svg>
                    </div>
                </div>

                <h3 class="text-lg font-bold text-gray-800 mb-2">Hapus Transaksi?</h3>
                <p class="text-sm text-gray-500 mb-1">Anda yakin ingin menghapus transaksi</p>
                <p class="text-sm font-bold text-[#5EA6FF] mb-6" x-text="deleteCode"></p>

                <div class="flex gap-3">
                    <button @click="deleteConfirm = false"
                        class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <form method="POST" :action="'{{ route('transaksi.index') }}/' + deleteId" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full px-4 py-2.5 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 transition">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================= POPUP RETURN ================= --}}
        @foreach($transactions as $trx)
            @if($trx->is_confirm)

                <div x-show="openReturn === '{{ $trx->id }}'" 
                     x-cloak 
                     x-data="{ selected: null }"
                     class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[1000] p-4">

                    <div class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

                        <form action="{{ route('transaksi.return', $trx->id) }}" method="POST">
                            @csrf

                            {{-- HEADER --}}
                            <div class="px-6 py-4 flex justify-between items-center text-white"
                                 style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">
                                <div>
                                    <h3 class="text-lg font-bold">Proses Pengembalian</h3>
                                    <p class="text-sm text-white/80">Pilih item yang akan dikembalikan</p>
                                </div>

                                <button type="button" @click="openReturn = null" class="text-2xl text-white/80 hover:text-white transition">
                                    ✕
                                </button>
                            </div>

                            <div class="p-6 overflow-auto flex-1 bg-gray-50">
                                {{-- INFO --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Karyawan</label>
                                        <input type="text" value="{{ $trx->borrower_name }}"
                                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-inner focus:outline-none text-sm" readonly>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Return</label>
                                        <input type="date" name="return_date" value="{{ date('Y-m-d') }}"
                                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-inner focus:ring-2 focus:ring-[#5EA6FF] focus:outline-none text-sm">
                                    </div>

                                </div>

                                {{-- TABLE ITEM --}}
                                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                                    <table class="w-full text-sm">

                                        <thead class="text-white text-xs uppercase tracking-wider"
                                               style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">
                                            <tr>
                                                <th class="py-3 px-4 text-center w-16 font-semibold">Pilih</th>
                                                <th class="py-3 px-4 text-left font-semibold">Consumable</th>
                                                <th class="py-3 px-4 text-center font-semibold">Sisa</th>
                                                <th class="py-3 px-4 text-center font-semibold">Qty Return</th>
                                                <th class="py-3 px-4 text-left font-semibold">Keterangan</th>
                                            </tr>
                                        </thead>

                                        <tbody class="bg-white divide-y divide-gray-100">

                                            @foreach($trx->items as $item)

                                                @php
                                                    $sisa = $item->qty - ($item->qty_return ?? 0);
                                                @endphp

                                                @if($sisa > 0)

                                                    <tr class="hover:bg-gray-50 transition">

                                                        <td class="px-4 py-3 text-center">
                                                            <input type="radio" name="selected_item" value="{{ $item->id }}" x-model="selected"
                                                                   class="w-4 h-4 accent-[#5EA6FF] border-gray-300">
                                                        </td>

                                                        <td class="px-4 py-3 font-medium text-gray-800">
                                                            {{ $item->consumable->name }}
                                                        </td>

                                                        <td class="px-4 py-3 text-center text-gray-600">
                                                            {{ $sisa }}
                                                        </td>

                                                        <td class="px-4 py-3 text-center">
                                                            <input type="number" name="items[{{ $item->id }}][qty]" min="1" max="{{ $sisa }}"
                                                                class="w-20 border border-gray-200 rounded-lg px-3 py-2 text-sm shadow-inner focus:ring-1 focus:ring-[#5EA6FF] focus:outline-none text-center"
                                                                :disabled="selected != '{{ $item->id }}'">
                                                        </td>

                                                        <td class="px-4 py-3">
                                                            <input type="text" name="items[{{ $item->id }}][note]" placeholder="Keterangan"
                                                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm shadow-inner focus:ring-1 focus:ring-[#5EA6FF] focus:outline-none"
                                                                :disabled="selected != '{{ $item->id }}'">
                                                        </td>

                                                    </tr>

                                                @endif

                                            @endforeach

                                        </tbody>

                                    </table>
                                </div>
                            </div>

                            {{-- BUTTON --}}
                            <div class="px-6 py-4 bg-white border-t border-gray-100 flex justify-end gap-3">

                                <button type="button" @click="openReturn = null" 
                                        class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                                    Batal
                                </button>

                                <button type="submit" :disabled="!selected"
                                        :class="!selected ? 'bg-gray-300 cursor-not-allowed' : 'hover:opacity-90'"
                                        class="px-5 py-2.5 text-white rounded-xl font-semibold text-sm shadow-md transition"
                                        style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">
                                    Kembalikan
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            @endif
        @endforeach

        <div class="mt-6 flex justify-center">
            {{ $transactions->links() }}
        </div>

    </div>

        <style>
        [x-cloak] { display: none !important; }

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

        <script>
    document.addEventListener('DOMContentLoaded', function() {

        const notifWrap = document.getElementById('notifWrap');
        const notifBox = document.getElementById('notifBox');
        const notifIcon = document.getElementById('notifIcon');
        const notifText = document.getElementById('notifText');
        const notifBar = document.getElementById('notifBar');
        const notifClose = document.getElementById('notifClose');
        let notifTimer = null;

        function showNotif(message, type) {
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
            
            // --- YANG DIUBAH DI SINI ---
            notifBar.style.transition = 'none';
            notifBar.style.width = '0%';

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    notifBar.style.transition = 'width 3.5s linear';
                    notifBar.style.width = '100%';
                });
            });
            // ---------------------------

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

        @if(session('success'))
            showNotif('{{ session("success") }}', 'success');
        @endif
        @if(session('error'))
            showNotif('{{ session("error") }}', 'error');
        @endif

    });
    </script>

@endsection