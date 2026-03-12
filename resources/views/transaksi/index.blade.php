
@extends('layouts.app')

@section('content')

    <div class="relative max-w-7xl mx-auto pb-40" x-data="{ openReturn: null }">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6">

            <div>
                <h1 class="text-3xl font-bold text-[#1CA7B6] tracking-tight">
                    Transaksi Consumable
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Kelola peminjaman dan pengembalian Consumable
                </p>
            </div>

            <div class="flex gap-3">

                <a href="{{ route('transaksi.create') }}"
                    class="text-white px-6 py-2.5 rounded-xl font-bold shadow-md hover:opacity-90 transition-all duration-200 tracking-wide"
                    style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                    + Permintaan Consumable
                </a>

            </div>

        </div>


        {{-- FILTER --}}
        <form method="GET" action="{{ route('transaksi.index') }}" class="mb-6">

            <div class="p-4 rounded-2xl shadow-md flex flex-wrap items-center gap-3"
                 style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">

                <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari alat atau karyawan..."
                    class="flex-1 min-w-[200px] bg-white border-0 rounded-xl shadow-inner px-4 py-2.5 text-sm focus:ring-2 focus:ring-white focus:outline-none">

                <span class="text-white font-semibold text-sm">Tanggal</span>

                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="px-4 py-2.5 rounded-xl bg-white text-gray-700 text-sm shadow-inner border-0 focus:outline-none">

                <span class="text-white font-bold text-sm">s/d</span>

                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="px-4 py-2.5 rounded-xl bg-white text-gray-700 text-sm shadow-inner border-0 focus:outline-none">

                <button type="submit"
                    class="px-5 py-2.5 text-sm bg-white text-[#1CA7B6] font-bold rounded-xl shadow-sm hover:bg-gray-100 transition">
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
                        style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">

                        <th class="py-4 px-6 font-semibold text-center">No</th>
                        <th class="py-4 px-6 font-semibold text-left">Kode</th>
                        <th class="py-4 px-6 font-semibold text-left">Tanggal</th>
                        <th class="py-4 px-6 font-semibold text-left">Karyawan</th>
                        <th class="py-4 px-6 font-semibold text-left">Consumable</th>
                        <th class="py-4 px-6 font-semibold text-center">Minta</th>
                        <th class="py-4 px-6 font-semibold text-center">Kembali</th>
                        <th class="py-4 px-6 font-semibold text-center">Sisa</th>
                        <th class="py-4 px-6 font-semibold text-center">Aksi</th>

                    </tr>
                </thead>

                <tbody class="text-gray-700 divide-y divide-gray-100">

                    @forelse($transactions as $trx)

                        <tr class="hover:bg-gray-50 transition">

                            <td class="py-4 px-6 text-center font-medium text-gray-600">
                                {{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}
                            </td>

                            <td class="py-4 px-6 font-bold text-[#1CA7B6]">
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

                                    <div class="flex justify-center gap-2 flex-wrap">

                                        <a href="{{ route('transaksi.edit', $trx->id) }}"
                                            class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg font-semibold text-xs transition">
                                            Edit
                                        </a>

                                        <form action="{{ route('transaksi.destroy', $trx->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg font-semibold text-xs transition">
                                                Hapus
                                            </button>

                                        </form>

                                        <form action="{{ route('transaksi.confirm', $trx->id) }}" method="POST">
                                            @csrf

                                            <button class="text-white px-3 py-1.5 rounded-lg font-semibold text-xs transition"
                                                style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
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

        <div class="mt-6 flex justify-center">
            {{ $transactions->appends(request()->query())->links('pagination::tailwind') }}
        </div>

        </div>

        {{-- ================= POPUP RETURN ================= --}}
        @foreach($transactions as $trx)
            @if($trx->is_confirm)

                <div x-show="openReturn === '{{ $trx->id }}'" 
                     x-cloak 
                     x-data="{ selected: null }"
                     class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
                     style="display: none;">

                    <div class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

                        <form action="{{ route('transaksi.return', $trx->id) }}" method="POST">
                            @csrf

                            {{-- HEADER --}}
                            <div class="px-6 py-4 flex justify-between items-center text-white"
                                 style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
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
                                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-inner focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none text-sm">
                                    </div>

                                </div>

                                {{-- TABLE ITEM --}}
                                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                                    <table class="w-full text-sm">

                                        <thead class="text-white text-xs uppercase tracking-wider"
                                               style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
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
                                                                   class="w-4 h-4 accent-[#1CA7B6] border-gray-300">
                                                        </td>

                                                        <td class="px-4 py-3 font-medium text-gray-800">
                                                            {{ $item->consumable->name }}
                                                        </td>

                                                        <td class="px-4 py-3 text-center text-gray-600">
                                                            {{ $sisa }}
                                                        </td>

                                                        <td class="px-4 py-3 text-center">
                                                            <input type="number" name="items[{{ $item->id }}][qty]" min="1" max="{{ $sisa }}"
                                                                class="w-20 border border-gray-200 rounded-lg px-3 py-2 text-sm shadow-inner focus:ring-1 focus:ring-[#1CA7B6] focus:outline-none text-center"
                                                                :disabled="selected != '{{ $item->id }}'">
                                                        </td>

                                                        <td class="px-4 py-3">
                                                            <input type="text" name="items[{{ $item->id }}][note]" placeholder="Keterangan"
                                                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm shadow-inner focus:ring-1 focus:ring-[#1CA7B6] focus:outline-none"
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
                                        style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                                    Kembalikan
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            @endif
        @endforeach

        <style>
            [x-cloak] { display: none !important; }
        </style>

    </div>

@endsection
```