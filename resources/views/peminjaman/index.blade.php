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

    {{-- ================= FLASH MESSAGE ================= --}}
    @if (session('success'))
    <div class="mb-4 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="mb-4 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm">
        {{ session('error') }}
    </div>
    @endif

    {{-- ================= FILTER ================= --}}
    <form method="GET" action="{{ route('peminjaman.index') }}"
        class="mb-6 p-4 rounded-2xl shadow-md flex flex-wrap gap-4 items-center"
        style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">

        <div class="relative flex-1 min-w-[200px]">
            <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Masukan nama peminjam"
                class="w-full bg-white border-0 rounded-xl px-4 py-2.5 pr-8 text-sm shadow-inner focus:ring-2 focus:ring-white focus:outline-none">

            {{-- Tombol X Langsung Reset --}}
            <button type="button"
                onclick="document.getElementById('searchInput').value=''; this.closest('form').submit();"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition"
                style="display:{{ request('search') ? 'block' : 'none' }}"
                id="clearSearchBtn">
                X
            </button>
        </div>

        <label class="text-white font-semibold text-sm">Tanggal</label>
        <input type="date" name="start_date" value="{{ request('start_date') }}"
            class="bg-white border-0 rounded-xl px-4 py-2.5 text-sm shadow-inner focus:outline-none">

        <span class="text-white font-bold text-sm">s/d</span>

        <input type="date" name="end_date" value="{{ request('end_date') }}"
            class="bg-white border-0 rounded-xl px-4 py-2.5 text-sm shadow-inner focus:outline-none">

        <button type="submit" class="bg-white text-[#1CA7B6] px-5 py-2.5 rounded-xl font-bold shadow-sm hover:bg-gray-100 transition text-sm">
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
                    <th class="py-4 px-6 font-semibold text-center">NO</th>
                    <th class="py-4 px-6 font-semibold text-left">KODE</th>
                    <th class="py-4 px-6 font-semibold text-left">TGL PINJAM</th>
                    <th class="py-4 px-6 font-semibold text-left">KARYAWAN</th>
                    <th class="py-4 px-6 font-semibold text-left">NAMA TOOLS</th>
                    <th class="py-4 px-6 font-semibold text-left">NO SERI</th>
                    <th class="py-4 px-6 font-semibold text-center">AKSI</th>
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
                            @if (! $transaction->is_confirm)
                            <a href="{{ route('peminjaman.edit', $transaction->id) }}"
                                class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg font-semibold text-xs transition">
                                Edit
                            </a>

                            <form action="{{ route('peminjaman.destroy', $transaction->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus transaksi ini?')">
                                @csrf @method('DELETE')
                                <button class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg font-semibold text-xs transition">
                                    Hapus
                                </button>
                            </form>

                            <form action="{{ route('peminjaman.confirm', $transaction->id) }}" method="POST" class="inline">
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

                            <span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-lg font-semibold text-xs inline-block">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
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
    <div x-show="openReturn === '{{ $transaction->id }}'"
        x-cloak
        x-data="{ selected: [] }"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
        style="display: none;">

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
                    <button type="button" @click="openReturn = null" class="text-2xl text-white/80 hover:text-white transition">
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
                                <tr>
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
                                        <input type="checkbox" class="w-4 h-4 accent-[#1CA7B6] rounded border-gray-300"
                                            x-model="selected" value="{{ $item->id }}" name="items[{{ $item->id }}][id]">
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $item->toolkit->toolkit_name }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 font-mono text-xs">
                                        {{ $item->serial->serial_number }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <select name="items[{{ $item->id }}][condition]"
                                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-[#1CA7B6] focus:outline-none"
                                            :disabled="!selected.includes('{{ $item->id }}')"
                                            :class="!selected.includes('{{ $item->id }}') ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700'">
                                            <option value="BAIK">Baik</option>
                                            <option value="MAINTENANCE">Butuh Perbaikan</option>
                                            <option value="RUSAK">Rusak</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="items[{{ $item->id }}][note]"
                                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm shadow-inner focus:ring-1 focus:ring-[#1CA7B6] focus:outline-none"
                                            placeholder="Keterangan..."
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
</div>

@push('styles')
<style>
    [x-cloak] {
        display: none !important;
    }
</style>
@endpush
@endsection