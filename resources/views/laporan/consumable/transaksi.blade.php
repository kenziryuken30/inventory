@extends('layouts.app')

@section('content')

    <div class="max-w-7xl mx-auto" x-data="{ openDetail: null }">

        {{-- ================= HEADER ================= --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Laporan Transaksi Consumable</h2>
        </div>


        {{-- ================= FILTER ================= --}}
        <form method="GET" action="{{ route('laporan.consumable.transaksi') }}"
            class="mb-6 bg-gradient-to-r from-cyan-600 to-teal-500 p-6 rounded-2xl shadow-lg flex flex-wrap gap-4 items-end">

            <input type="hidden" name="type" value="{{ $type }}">

            <div>
                <label class="text-white text-sm block mb-1">Nama Peminjam</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                    class="px-4 py-2 rounded-lg shadow w-64">
            </div>

            <div>
                <label class="text-white text-sm block mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="px-3 py-2 rounded-lg shadow">
            </div>

            <div>
                <label class="text-white text-sm block mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-3 py-2 rounded-lg shadow">
            </div>

            <div class="flex gap-2">

                <button class="bg-white text-teal-600 px-4 py-2 rounded-lg shadow font-semibold">
                    🔎 Filter
                </button>

                <a href="{{ route('laporan.consumable.transaksi', ['type' => $type]) }}"
                    class="bg-red-500 text-white px-4 py-2 rounded-lg shadow">
                    Reset
                </a>

                {{-- EXPORT PDF --}}
                <a href="{{ route('laporan.consumable.export.pdf', request()->all()) }}"
                    class="bg-gray-800 text-white px-4 py-2 rounded-lg shadow hover:bg-black">
                    📄 Export PDF
                </a>

                {{-- EXPORT EXCEL --}}
                <a href="{{ route('laporan.consumable.export.excel', request()->all()) }}"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg shadow hover:bg-green-700">
                     Export Excel
                </a>

            </div>
        </form>


        {{-- ================= TOGGLE ================= --}}
        <div class="flex items-center gap-3 mb-4">

            <div class="flex bg-gray-200 p-1 rounded-xl shadow-inner">

                <a href="{{ route('laporan.consumable.transaksi', array_merge(request()->all(), ['type' => 'pengeluaran'])) }}"
                    class="px-4 py-2 rounded-xl text-sm {{ $type == 'pengeluaran' ? 'bg-white shadow font-semibold' : 'text-gray-600' }}">
                    📤 Pengeluaran
                </a>

                <a href="{{ route('laporan.consumable.transaksi', array_merge(request()->all(), ['type' => 'pengembalian'])) }}"
                    class="px-4 py-2 rounded-xl text-sm {{ $type == 'pengembalian' ? 'bg-white shadow font-semibold' : 'text-gray-600' }}">
                    📥 Pengembalian
                </a>
            </div>

            <div class="px-4 py-2 bg-gray-100 rounded-lg text-sm shadow">
                Total {{ ucfirst($type) }} : {{ $data->count() }}
            </div>

            <div class="px-4 py-2 bg-white rounded-xl shadow text-sm">
                Total Item Diminta :
                <span class="font-bold text-teal-600">
                    {{ $type == 'pengeluaran'
        ? $data->flatMap->items->sum('qty')
        : $data->sum('qty') }}
                </span>
            </div>

        </div>


        {{-- ================= TABLE ================= --}}
        <div class="bg-white shadow-[0_10px_25px_rgba(0,0,0,0.1)] 
        rounded-2xl overflow-hidden">

            <div class="max-h-[420px] overflow-y-auto">

                <table class="min-w-full text-sm text-gray-700">

                    <thead class="bg-gray-100 text-xs uppercase tracking-wider text-gray-600 sticky top-0 z-10">
                        <tr>
                            <th class="px-4 py-3 text-center bg-gray-100">No</th>
                            <th class="px-4 py-3 bg-gray-100">Kode</th>
                            <th class="px-4 py-3 bg-gray-100">Tanggal</th>
                            <th class="px-4 py-3 bg-gray-100">Karyawan</th>
                            <th class="px-4 py-3 bg-gray-100">Consumable</th>
                            <th class="px-4 py-3 text-center bg-gray-100">Jumlah</th>
                            @if($type == 'pengeluaran')
                                <th class="px-4 py-3 text-center bg-gray-100">Detail</th>
                            @else
                                <th class="px-4 py-3 bg-gray-100">Keterangan</th>
                            @endif
                        </tr>
                    </thead>

                    <tbody class="divide-y">

                        @forelse($data as $row)

                            <tr class="hover:bg-gray-50">

                                <td class="px-4 py-3 text-center">
                                    {{ $loop->iteration }}
                                </td>

                                {{-- ================= KODE ================= --}}
                                <td class="px-4 py-3 font-semibold text-blue-600">
                                    @if($type == 'pengeluaran')
                                        {{ $row->transaction_code }}
                                    @else
                                        {{ $row->transaction->transaction_code }}
                                    @endif
                                </td>

                                {{-- ================= TANGGAL ================= --}}
                                <td class="px-4 py-3">
                                    @if($type == 'pengeluaran')
                                        {{ \Carbon\Carbon::parse($row->date)->format('d-m-Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($row->transaction->return_date)->format('d-m-Y') }}
                                    @endif
                                </td>

                                {{-- ================= BORROWER ================= --}}
                                <td class="px-4 py-3">
                                    @if($type == 'pengeluaran')
                                        {{ $row->borrower_name }}
                                    @else
                                        {{ $row->transaction->borrower_name }}
                                    @endif
                                </td>

                                {{-- ================= CONSUMABLE ================= --}}
                                <td class="px-4 py-3">
                                    @if($type == 'pengeluaran')
                                        @foreach($row->items as $item)
                                            <div>{{ $item->consumable->name }}</div>
                                        @endforeach
                                    @else
                                        {{ $row->consumable->name }}
                                    @endif
                                </td>

                                {{-- ================= JUMLAH ================= --}}
                                <td class="px-4 py-3 text-center font-semibold text-teal-600">

                                    @if($type == 'pengeluaran')
                                        {{ $row->items->sum('qty') }}
                                    @else
                                        {{ $row->qty_return }}
                                    @endif
                                </td>

                                {{-- ================= DETAIL ================= --}}
                                @if($type == 'pengeluaran')
                                    <td class="px-4 py-3 text-center">
                                        <button @click="openDetail = {{ $row->id }}"
                                            class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded text-xs">
                                            👁 Detail
                                        </button>
                                    </td>
                                @else
                                    <td class="px-4 py-3">
                                        {{ $row->note ?? '-' }}
                                    </td>
                                @endif

                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-400">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>


            {{-- ================= MODAL ================= --}}
            @if($type == 'pengeluaran')

                @foreach($data as $row)

                    <div x-show="openDetail === {{ $row->id }}" x-transition x-cloak
                        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

                        <div class="bg-white w-full max-w-2xl rounded-xl shadow-xl p-6">

                            {{-- HEADER --}}
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold">Detail Transaksi Consumable</h3>
                                <button @click="openDetail = null">✕</button>
                            </div>

                            {{-- INFO GRID --}}
                            <div class="grid grid-cols-2 gap-4 text-sm mb-4">

                                <div>
                                    <p class="font-semibold">Kode Transaksi</p>
                                    <p>{{ $row->transaction_code }}</p>
                                </div>

                                <div>
                                    <p class="font-semibold">Tanggal kembali</p>
                                    {{ \Carbon\Carbon::parse($row->created_at)->format('d-m-Y') }}
                                </div>

                                <div>
                                    <p class="font-semibold">Karyawan</p>
                                    <p>{{ $row->borrower_name }}</p>
                                </div>

                                <div>
                                    <p class="font-semibold">Client</p>
                                    <p>{{ $row->client ?? '-' }}</p>
                                </div>

                                <div>
                                    <p class="font-semibold">Project</p>
                                    <p>{{ $row->project ?? '-' }}</p>
                                </div>

                                <div class="col-span-2">
                                    <p class="font-semibold">Keperluan</p>
                                    <div class="bg-gray-100 rounded-xl px-4 py-3 shadow">
                                        {{ $row->purpose ?? '-' }}
                                    </div>
                                </div>

                            </div>

                            {{-- TABLE ITEM --}}
                            <div class="bg-gray-50 rounded-lg overflow-hidden">

                                <table class="w-full text-sm">

                                    <thead class="bg-gradient-to-r from-cyan-600 to-teal-500 text-white">
                                        <tr>
                                            <th class="px-4 py-2 text-left">NAMA CONSUMABLE</th>
                                            <th class="px-4 py-2 text-left">QTY</th>
                                            <th class="px-4 py-2 text-left">UNIT</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($row->items as $item)
                                            <tr class="border-b">
                                                <td class="px-4 py-2">
                                                    {{ $item->consumable->name }}
                                                </td>
                                                <td class="px-4 py-2">
                                                    {{ $item->qty }}
                                                </td>
                                                <td class="px-4 py-2">
                                                    {{ $item->consumable->unit }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>

                            </div>

                            {{-- FOOTER --}}
                            <div class="text-right mt-4">
                                <button @click="openDetail = null" class="bg-gray-200 px-4 py-2 rounded-lg">
                                    Tutup
                                </button>
                            </div>

                        </div>
                    </div>

                @endforeach
            @endif

            @if($type == 'pengembalian')

                @foreach($data as $row)

                    <div x-show="openDetail === {{ $row->id }}" x-transition x-cloak
                        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

                        <div class="bg-white w-full max-w-xl rounded-xl shadow-xl p-6">

                            {{-- HEADER --}}
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold">Detail Pengembalian Consumable</h3>
                                <button @click="openDetail = null">✕</button>
                            </div>

                            {{-- INFO --}}
                            <div class="grid grid-cols-2 gap-4 text-sm mb-4">

                                <div>
                                    <p class="font-semibold">Kode Transaksi</p>
                                    <p>{{ $row->transaction->transaction_code }}</p>
                                </div>

                                <div>
                                    <p class="font-semibold">Tanggal Pengembalian</p>
                                    <p>{{ \Carbon\Carbon::parse($row->transaction->return_date)->format('d M Y') }}</p>
                                </div>

                                <div>
                                    <p class="font-semibold">Karyawan</p>
                                    <p>{{ $row->transaction->borrower_name }}</p>
                                </div>

                                <div>
                                    <p class="font-semibold">Consumable</p>
                                    <p>{{ $row->consumable->name }}</p>
                                </div>

                                <div>
                                    <p class="font-semibold">Jumlah Dikembalikan</p>
                                    <p class="text-teal-600 font-bold">
                                        {{ $row->qty_return }} {{ $row->consumable->unit }}
                                    </p>
                                </div>

                            </div>

                            {{-- FOOTER --}}
                            <div class="text-right mt-4">
                                <button @click="openDetail = null" class="bg-gray-200 px-4 py-2 rounded-lg">
                                    Tutup
                                </button>
                            </div>

                        </div>
                    </div>

                @endforeach
            @endif

        </div>
@endsection