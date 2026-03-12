
@extends('layouts.app')

@section('content')

    <div class="max-w-7xl mx-auto" x-data="{ openDetail: null }">

        {{-- ================= HEADER ================= --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-3xl font-bold text-[#1CA7B6] tracking-tight">Laporan Transaksi Consumable</h2>
                <p class="text-sm text-gray-500 mt-1">Rekap data pengeluaran dan pengembalian barang consumable</p>
            </div>
            
            {{-- Tombol Kembali bisa ditambahkan di sini jika perlu, mengikuti contoh --}}
            {{-- <a href="{{ url()->previous() }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition">← Kembali</a> --}}
        </div>


        {{-- ================= FILTER ================= --}}
        <form method="GET" action="{{ route('laporan.consumable.transaksi') }}"
            class="mb-6 p-4 rounded-2xl shadow-md flex flex-wrap gap-4 items-end"
            style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">

            <input type="hidden" name="type" value="{{ $type }}">

            <div class="flex-1 min-w-[200px]">
                <label class="text-white text-sm font-semibold block mb-1">Nama Peminjam</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam..."
                    class="w-full px-4 py-2.5 rounded-xl bg-white border-0 shadow-inner focus:ring-2 focus:ring-white focus:outline-none text-sm">
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

                <a href="{{ route('laporan.consumable.transaksi', ['type' => $type]) }}"
                    class="bg-red-500 text-white px-4 py-2.5 rounded-xl shadow-sm hover:bg-red-600 transition text-sm font-semibold">
                    Reset
                </a>

                {{-- EXPORT PDF --}}
                <a href="{{ route('laporan.consumable.export.pdf', request()->all()) }}"
                    class="bg-gray-800 text-white px-4 py-2.5 rounded-xl shadow-sm hover:bg-gray-900 transition text-sm font-semibold">
                    📄 PDF
                </a>

                {{-- EXPORT EXCEL --}}
                <a href="{{ route('laporan.consumable.export.excel', request()->all()) }}"
                    class="bg-green-600 text-white px-4 py-2.5 rounded-xl shadow-sm hover:bg-green-700 transition text-sm font-semibold">
                    📊 Excel
                </a>

            </div>
        </form>


        {{-- ================= TOGGLE ================= --}}
        <div class="flex items-center gap-3 mb-6">

            <div class="flex bg-gray-200 p-1 rounded-xl shadow-inner">

                <a href="{{ route('laporan.consumable.transaksi', array_merge(request()->all(), ['type' => 'pengeluaran'])) }}"
                    class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $type == 'pengeluaran' ? 'bg-white shadow text-[#1CA7B6]' : 'text-gray-600 hover:text-gray-800' }}">
                    📤 Pengeluaran
                </a>

                <a href="{{ route('laporan.consumable.transaksi', array_merge(request()->all(), ['type' => 'pengembalian'])) }}"
                    class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $type == 'pengembalian' ? 'bg-white shadow text-[#1CA7B6]' : 'text-gray-600 hover:text-gray-800' }}">
                    📥 Pengembalian
                </a>
            </div>

            <div class="px-4 py-2 bg-white rounded-xl shadow text-sm font-medium text-gray-700">
                Total {{ ucfirst($type) }} : <span class="font-bold text-[#1CA7B6]">{{ $data->count() }}</span>
            </div>

            <div class="px-4 py-2 bg-white rounded-xl shadow text-sm font-medium text-gray-700">
                Total Item Diminta :
                <span class="font-bold text-[#1CA7B6]">
                    {{ $type == 'pengeluaran'
                        ? $data->getCollection()->flatMap->items->sum('qty')
                        : $data->getCollection()->sum('qty_return') }}
                </span>
            </div>

        </div>


        {{-- ================= TABLE ================= --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">

            <div class="max-h-[420px] overflow-y-auto">

                <table class="min-w-full text-sm text-gray-700">

                    {{-- Header dengan Gradasi --}}
                    <thead class="text-white text-xs uppercase tracking-wider sticky top-0 z-10"
                            style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                        <tr>
                            <th class="py-4 px-6 font-semibold text-center">No</th>
                            <th class="py-4 px-6 font-semibold text-left">Kode</th>
                            <th class="py-4 px-6 font-semibold text-left">Tanggal</th>
                            <th class="py-4 px-6 font-semibold text-left">Karyawan</th>
                            <th class="py-4 px-6 font-semibold text-left">Consumable</th>
                            <th class="py-4 px-6 font-semibold text-center">Jumlah</th>
                            @if($type == 'pengeluaran')
                                <th class="py-4 px-6 font-semibold text-center">Detail</th>
                            @else
                                <th class="py-4 px-6 font-semibold text-left">Keterangan</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse($data as $row)

                            <tr class="hover:bg-gray-50 transition">

                                <td class="py-4 px-6 text-center font-medium text-gray-600">
                                    {{ $loop->iteration }}
                                </td>

                                {{-- ================= KODE ================= --}}
                                <td class="py-4 px-6 font-bold text-[#1CA7B6]">
                                    @if($type == 'pengeluaran')
                                        {{ $row->transaction_code }}
                                    @else
                                        {{ $row->transaction->transaction_code }}
                                    @endif
                                </td>

                                {{-- ================= TANGGAL ================= --}}
                                <td class="py-4 px-6 text-gray-700">
                                    @if($type == 'pengeluaran')
                                        {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($row->transaction->return_date)->format('d M Y') }}
                                    @endif
                                </td>

                                {{-- ================= BORROWER ================= --}}
                                <td class="py-4 px-6 text-gray-700">
                                    @if($type == 'pengeluaran')
                                        {{ $row->borrower_name }}
                                    @else
                                        {{ $row->transaction->borrower_name }}
                                    @endif
                                </td>

                                {{-- ================= CONSUMABLE ================= --}}
                                <td class="py-4 px-6 text-gray-700">
                                    @if($type == 'pengeluaran')
                                        @foreach($row->items as $item)
                                            <div>{{ $item->consumable->name }}</div>
                                        @endforeach
                                    @else
                                        {{ $row->consumable->name }}
                                    @endif
                                </td>

                                {{-- ================= JUMLAH ================= --}}
                                <td class="py-4 px-6 text-center font-bold text-[#1CA7B6]">

                                    @if($type == 'pengeluaran')
                                        {{ $row->items->sum('qty') }}
                                    @else
                                        {{ $row->qty_return }}
                                    @endif
                                </td>

                                {{-- ================= DETAIL ================= --}}
                                @if($type == 'pengeluaran')
                                    <td class="py-4 px-6 text-center">
                                        <button @click="openDetail = {{ $row->id }}"
                                            class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-1.5 rounded-lg font-semibold text-xs transition shadow-sm">
                                            👁 Detail
                                        </button>
                                    </td>
                                @else
                                    <td class="py-4 px-6 text-gray-500">
                                        {{ $row->note ?? '-' }}
                                    </td>
                                @endif

                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        <span>Tidak ada data transaksi pada periode ini</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>


        {{-- ================= MODAL PENGELUARAN ================= --}}
        @if($type == 'pengeluaran')

            @foreach($data as $row)

                <div x-show="openDetail === {{ $row->id }}" x-transition x-cloak
                    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">

                    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

                        {{-- HEADER MODAL --}}
                        <div class="px-6 py-4 flex justify-between items-center text-white"
                             style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                            <div>
                                <h3 class="text-lg font-bold">Detail Transaksi Consumable</h3>
                                <p class="text-sm text-white/80">Informasi lengkap transaksi</p>
                            </div>
                            <button @click="openDetail = null" class="text-2xl text-white/80 hover:text-white transition">✕</button>
                        </div>

                        <div class="p-6 overflow-auto flex-1 bg-gray-50">
                            {{-- INFO GRID --}}
                            <div class="grid grid-cols-2 gap-6 text-sm mb-6">

                                <div>
                                    <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Kode Transaksi</p>
                                    <p class="text-gray-800 font-semibold">{{ $row->transaction_code }}</p>
                                </div>

                                <div>
                                    <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Tanggal</p>
                                    <p class="text-gray-800">{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}</p>
                                </div>

                                <div>
                                    <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Karyawan</p>
                                    <p class="text-gray-800">{{ $row->borrower_name }}</p>
                                </div>

                                <div>
                                    <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Client</p>
                                    <p class="text-gray-800">{{ $row->client ?? '-' }}</p>
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
                                            <th class="py-3 px-4 font-semibold text-left">NAMA CONSUMABLE</th>
                                            <th class="py-3 px-4 font-semibold text-center">QTY</th>
                                            <th class="py-3 px-4 font-semibold text-left">UNIT</th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach($row->items as $item)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-4 py-3 text-gray-800">{{ $item->consumable->name }}</td>
                                                <td class="px-4 py-3 text-center font-semibold text-[#1CA7B6]">{{ $item->qty }}</td>
                                                <td class="px-4 py-3 text-gray-600">{{ $item->consumable->unit }}</td>
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
        @endif

        {{-- ================= MODAL PENGEMBALIAN ================= --}}
        @if($type == 'pengembalian')

            @foreach($data as $row)

                <div x-show="openDetail === {{ $row->id }}" x-transition x-cloak
                    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">

                    <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

                        {{-- HEADER MODAL --}}
                        <div class="px-6 py-4 flex justify-between items-center text-white"
                             style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                            <div>
                                <h3 class="text-lg font-bold">Detail Pengembalian Consumable</h3>
                            </div>
                            <button @click="openDetail = null" class="text-2xl text-white/80 hover:text-white transition">✕</button>
                        </div>

                        <div class="p-6 overflow-auto flex-1 bg-gray-50">
                            {{-- INFO --}}
                            <div class="grid grid-cols-2 gap-6 text-sm mb-4">

                                <div>
                                    <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Kode Transaksi</p>
                                    <p class="text-gray-800 font-semibold">{{ $row->transaction->transaction_code }}</p>
                                </div>

                                <div>
                                    <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Tanggal Pengembalian</p>
                                    <p class="text-gray-800">{{ \Carbon\Carbon::parse($row->transaction->return_date)->format('d M Y') }}</p>
                                </div>

                                <div>
                                    <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Karyawan</p>
                                    <p class="text-gray-800">{{ $row->transaction->borrower_name }}</p>
                                </div>

                                <div>
                                    <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Consumable</p>
                                    <p class="text-gray-800">{{ $row->consumable->name }}</p>
                                </div>

                                <div class="col-span-2">
                                    <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Jumlah Dikembalikan</p>
                                    <p class="text-[#1CA7B6] font-bold text-lg">
                                        {{ $row->qty_return }} {{ $row->consumable->unit }}
                                    </p>
                                </div>

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
        @endif

        <style>
            [x-cloak] { display: none !important; }
        </style>

    </div>
@endsection
```