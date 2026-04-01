@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto" x-data="{ openDetail: null }">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-[#1CA7B6] tracking-tight">Laporan Tools</h2>
            <p class="text-sm text-gray-500 mt-1">Laporan {{ ucfirst($type) }} Tools</p>
        </div>

        <a href="{{ url()->previous() }}"
            class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition">
            ← Kembali
        </a>
    </div>


    {{-- ================= FILTER ================= --}}
    <form method="GET" action="{{ route('laporan.tools.transaksi') }}"
        class="mb-6 p-4 rounded-2xl shadow-md flex flex-wrap gap-4 items-end"
        style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">

        <input type="hidden" name="type" value="{{ $type }}">

        <div>
            <label class="text-white text-sm font-semibold block mb-1">Cari Karyawan</label>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Nama karyawan..."
                class="px-4 py-2.5 rounded-xl bg-white border-0 shadow-inner focus:outline-none text-sm">
        </div>

        <div>
            <label class="text-white text-sm font-semibold block mb-1">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}"
                class="px-4 py-2.5 rounded-xl bg-white border-0 shadow-inner focus:outline-none text-sm">
        </div>

        <div>
            <label class="text-white text-sm font-semibold block mb-1">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}"
                class="px-4 py-2.5 rounded-xl bg-white border-0 shadow-inner focus:outline-none text-sm">
        </div>

        <div class="flex gap-2 items-end">
            <button type="submit" class="bg-white text-[#1CA7B6] px-5 py-2.5 rounded-xl font-bold shadow-sm hover:bg-gray-100 transition text-sm">
                🔎 Filter
            </button>

            <a href="{{ route('laporan.tools.transaksi', ['type'=>$type]) }}"
                class="bg-red-500 text-white px-4 py-2.5 rounded-xl shadow-sm hover:bg-red-600 transition text-sm font-semibold">
                Reset
            </a>

            {{-- EXPORT PDF --}}
            <a href="{{ route('laporan.tools.export.pdf', ['type'=>$type, 'start_date'=>request('start_date'), 'end_date'=>request('end_date')]) }}"
                class="bg-gray-800 text-white px-4 py-2.5 rounded-xl shadow-sm hover:bg-gray-900 transition text-sm font-semibold">
                📄 PDF
            </a>

            {{-- EXPORT EXCEL --}}
            <a href="{{ route('laporan.tools.export.excel', ['type'=>$type, 'start_date'=>request('start_date'), 'end_date'=>request('end_date')]) }}"
                class="bg-green-600 text-white px-4 py-2.5 rounded-xl shadow-sm hover:bg-green-700 transition text-sm font-semibold">
                📊 Excel
            </a>
        </div>
    </form>


    {{-- ================= TOGGLE ================= --}}
    <div class="flex items-center gap-3 mb-6">

        <div class="flex bg-gray-200 p-1 rounded-xl shadow-inner">

            <a href="{{ route('laporan.tools.transaksi', ['type'=>'peminjaman']) }}"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $type == 'peminjaman' ? 'bg-white shadow text-[#1CA7B6]' : 'text-gray-600 hover:text-gray-800' }}">
                ⇄ Peminjaman Tools
            </a>

            <a href="{{ route('laporan.tools.transaksi', ['type'=>'pengembalian']) }}"
                class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $type == 'pengembalian' ? 'bg-white shadow text-[#1CA7B6]' : 'text-gray-600 hover:text-gray-800' }}">
                ↺ Pengembalian Tools
            </a>
        </div>

        <div class="px-4 py-2 bg-white rounded-xl shadow text-sm font-medium text-gray-700">
            Total {{ ucfirst($type) }} : <span class="font-bold text-[#1CA7B6]">{{ $data->count() }}</span>
        </div>

    </div>


    {{-- ========================= --}}
    {{-- TABEL PEMINJAMAN --}}
    {{-- ========================= --}}

    @if($type == 'peminjaman')

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="max-h-[420px] overflow-y-auto">
            <table class="min-w-full text-sm text-gray-700">
                {{-- Header dengan Gradasi --}}
                <thead class="text-white text-xs uppercase tracking-wider sticky top-0 z-10"
                    style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                    <tr>
                        <th class="py-4 px-6 font-semibold text-center">NO</th>
                        <th class="py-4 px-6 font-semibold text-left">KODE</th>
                        <th class="py-4 px-6 font-semibold text-left">TANGGAL</th>
                        <th class="py-4 px-6 font-semibold text-left">KARYAWAN</th>
                        <th class="py-4 px-6 font-semibold text-left">ALAT</th>
                        <th class="py-4 px-6 font-semibold text-center">AKSI</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse($data as $row)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6 text-center font-medium text-gray-600">
                            {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                        </td>

                        <td class="py-4 px-6 font-bold text-[#1CA7B6]">
                            {{ $row->transaction_code }}
                        </td>

                        <td class="py-4 px-6 text-gray-700">
                            {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                        </td>

                        <td class="py-4 px-6 text-gray-700 font-semibold uppercase text-xs">
                            {{ $row->borrower_name }}
                        </td>

                        <td class="py-4 px-6 text-gray-700 text-xs">
                            @foreach($row->items as $item)
                            <div>{{ $item->toolkit->toolkit_name ?? '-' }}</div>
                            @endforeach
                        </td>

                        <td class="py-4 px-6 text-center">
                            <button @click="openDetail = {{ $row->id }}"
                                class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-1.5 rounded-lg font-semibold text-xs transition shadow-sm">
                                👁 Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <span>Data tidak ditemukan</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>




    {{-- MODAL DETAIL PEMINJAMAN --}}
    @foreach($data as $row)
    <div x-show="openDetail === {{ $row->id }}" x-transition x-cloak
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">

        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

            {{-- HEADER MODAL --}}
            <div class="px-6 py-4 flex justify-between items-center text-white"
                style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                <div>
                    <h3 class="text-lg font-bold">Detail Peminjaman</h3>
                    <p class="text-sm text-white/80">Informasi lengkap transaksi</p>
                </div>
                <button @click="openDetail = null" class="text-2xl text-white/80 hover:text-white transition">✕</button>
            </div>

            <div class="p-6 overflow-auto flex-1 bg-gray-50">
                {{-- INFO GRID --}}
                <div class="grid grid-cols-2 gap-6 text-sm mb-6">

                    <div>
                        <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Kode</p>
                        <p class="text-gray-800 font-semibold">{{ $row->transaction_code }}</p>
                    </div>

                    <div>
                        <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Karyawan</p>
                        <p class="text-gray-800">{{ $row->borrower_name }}</p>
                    </div>

                    <div>
                        <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Tanggal</p>
                        <p class="text-gray-800">{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</p>
                    </div>

                    <div>
                        <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Client</p>
                        <p class="text-gray-800">{{ $row->client_name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Project</p>
                        <p class="text-gray-800">{{ $row->project ?? '-' }}</p>
                    </div>

                    <div class="col-span-2">
                        <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Keperluan</p>
                        <div class="bg-white rounded-xl px-4 py-3 shadow-inner text-gray-700 border border-gray-200">
                            {{ $row->purpose ?? '-' }}
                        </div>
                    </div>

                </div>

                {{-- TABLE ITEM --}}
                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                    <table class="w-full text-sm">
                        <thead class="text-white text-xs uppercase tracking-wider"
                            style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                            <tr>
                                <th class="py-3 px-4 font-semibold text-left">NO SERI</th>
                                <th class="py-3 px-4 font-semibold text-left">NAMA ALAT</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($row->items as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $item->serial->serial_number ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-800">{{ $item->toolkit->toolkit_name ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="px-6 py-4 bg-white border-t border-gray-100 flex justify-end">
                <button @click="openDetail = null"
                    class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                    Tutup
                </button>
            </div>

        </div>
    </div>
    @endforeach


    {{-- ========================= --}}
    {{-- TABEL PENGEMBALIAN --}}
    {{-- ========================= --}}

    @else

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="max-h-[420px] overflow-y-auto">
            <table class="min-w-full text-sm text-gray-700">
                {{-- Header dengan Gradasi --}}
                <thead class="text-white text-xs uppercase tracking-wider sticky top-0 z-10"
                    style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                    <tr>
                        <th class="py-4 px-6 font-semibold text-center">NO</th>
                        <th class="py-4 px-6 font-semibold text-left">KODE</th>
                        <th class="py-4 px-6 font-semibold text-left">TGL KEMBALI</th>
                        <th class="py-4 px-6 font-semibold text-left">KARYAWAN</th>
                        <th class="py-4 px-6 font-semibold text-left">ALAT</th>
                        <th class="py-4 px-6 font-semibold text-left">NO SERI</th>
                        <th class="py-4 px-6 font-semibold text-left">KONDISI</th>
                        <th class="py-4 px-6 font-semibold text-left">KETERANGAN</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse($data as $row)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-6 text-center font-medium text-gray-600">
                            {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                        </td>

                        <td class="py-4 px-6 font-bold text-[#1CA7B6]">
                            {{ $row->transaction->transaction_code }}
                        </td>

                        <td class="py-4 px-6 text-gray-700">
                            {{ \Carbon\Carbon::parse($row->return_date)->format('d M Y') }}
                        </td>

                        <td class="py-4 px-6 text-gray-700">
                            {{ $row->transaction->borrower_name }}
                        </td>

                        <td class="py-4 px-6 text-gray-700">
                            {{ $row->toolkit->toolkit_name ?? '-' }}
                        </td>

                        <td class="py-4 px-6 text-gray-500 font-mono text-xs">
                            {{ $row->serial->serial_number ?? '-' }}
                        </td>

                        <td class="py-4 px-6 text-gray-700">
                            <span class="px-2 py-1 rounded-lg text-xs font-semibold {{ $row->return_condition == 'BAIK' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $row->return_condition ?? '-' }}
                            </span>
                        </td>

                        <td class="py-4 px-6 text-gray-500">
                            {{ $row->return_note ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <span>Data tidak ditemukan</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

    @endif

    <div class="mt-6 flex justify-center">
        {{ $data->links() }}
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: .6;
        }
    </style>

</div>
@endsection