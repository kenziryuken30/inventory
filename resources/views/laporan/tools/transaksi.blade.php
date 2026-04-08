@extends('layouts.app')

@section('content')

    <div class="max-w-7xl mx-auto" x-data="{ openDetail: null }">

        {{-- ================= HEADER ================= --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-3xl font-bold text-[#5EA6FF] tracking-tight">Laporan Tools</h2>
                <p class="text-sm text-gray-500 mt-1">Laporan {{ ucfirst($type) }} Tools</p>
            </div>
        </div>


        {{-- ================= FILTER ================= --}}
        <form method="GET" action="{{ route('laporan.tools.transaksi') }}" id="filterForm"
            class="mb-6 p-4 rounded-2xl shadow-md flex flex-wrap gap-4 items-end"
            style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">

            <input type="hidden" name="type" value="{{ $type }}">

            <div class="flex-1 min-w-[200px]">
                <label class="text-white text-sm font-semibold block mb-1">Cari Karyawan</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama karyawan..."
                    class="w-full px-4 py-2.5 rounded-xl bg-white border-0 shadow-inner focus:ring-2 focus:ring-white focus:outline-none text-sm">
            </div>

            <div>
                <label class="text-white text-sm font-semibold block mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" id="startDate" value="{{ request('start_date') }}"
                    class="px-4 py-2.5 rounded-xl bg-white border-0 shadow-inner focus:outline-none text-sm">
            </div>

            <div>
                <label class="text-white text-sm font-semibold block mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" id="endDate" value="{{ request('end_date') }}"
                    class="px-4 py-2.5 rounded-xl bg-white border-0 shadow-inner focus:outline-none text-sm">
            </div>


            <div class="flex gap-2 items-end">

                <button type="submit"
                    class="bg-white text-[#5EA6FF] px-5 py-2.5 rounded-xl font-bold shadow-sm hover:bg-gray-100 hover:shadow-md transition-all duration-300 text-sm hover:-translate-y-0.5">
                    🔎 Filter
                </button>

                {{-- RESET --}}
                <a href="{{ route('laporan.tools.transaksi', ['type' => $type]) }}"
                    class="group relative px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg overflow-hidden"
                    style="background: linear-gradient(135deg, #C084FC, #A855F7); color: white; box-shadow: 0 4px 15px rgba(168,85,247,0.35);">
                    <span class="relative z-10 flex items-center gap-1.5">
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:-rotate-180" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182" />
                        </svg>
                        Reset
                    </span>
                    <div class="absolute inset-0 bg-white/0 group-hover:bg-white/20 transition-all duration-300"></div>
                </a>

                {{-- PDF --}}
                <a href="{{ route('laporan.tools.export.pdf', ['type' => $type, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                    class="group relative px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg overflow-hidden"
                    style="background: linear-gradient(135deg, #FB7185, #E11D48); color: white; box-shadow: 0 4px 15px rgba(225,29,72,0.35);">
                    <span class="relative z-10 flex items-center gap-1.5">
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:scale-110" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6zm3-7h6v1.5H9V13zm0 3h6v1.5H9V16zm0-6h3v1.5H9V10z" />
                        </svg>
                        PDF
                    </span>
                    <div class="absolute inset-0 bg-white/0 group-hover:bg-white/20 transition-all duration-300"></div>
                </a>

                {{-- EXCEL --}}
                <a href="{{ route('laporan.tools.export.excel', ['type' => $type, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                    class="group relative px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg overflow-hidden"
                    style="background: linear-gradient(135deg, #34D399, #059669); color: white; box-shadow: 0 4px 15px rgba(5,150,105,0.35);">
                    <span class="relative z-10 flex items-center gap-1.5">
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:scale-110" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6zm2-6h3v1.5H8V14zm0 3h3v1.5H8V17zm5-6h3v1.5h-3V11zm0 3h3v1.5h-3V14zm0 3h3v1.5h-3V17z" />
                        </svg>
                        Excel
                    </span>
                    <div class="absolute inset-0 bg-white/0 group-hover:bg-white/20 transition-all duration-300"></div>
                </a>

            </div>
        </form>


        {{-- ================= TOGGLE ================= --}}
        <div class="flex items-center gap-4 mb-6">

            <div class="flex bg-gray-200 p-1 rounded-xl shadow-inner">

                <a href="{{ route('laporan.tools.transaksi', ['type' => 'peminjaman']) }}"
                    class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $type == 'peminjaman' ? 'bg-white shadow text-[#5EA6FF]' : 'text-gray-600 hover:text-gray-800' }}">
                    ⇄ Peminjaman Tools
                </a>

                <a href="{{ route('laporan.tools.transaksi', ['type' => 'pengembalian']) }}"
                    class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $type == 'pengembalian' ? 'bg-white shadow text-[#5EA6FF]' : 'text-gray-600 hover:text-gray-800' }}">
                    ↺ Pengembalian Tools
                </a>
            </div>
            <div class="text-sm text-gray-500">
                Total {{ ucfirst($type) }} : <span class="font-bold text-[#5EA6FF]">{{ $data->count() }}</span>
            </div>

        </div>


        {{-- ========================= --}}
        {{-- TABEL PEMINJAMAN --}}
        {{-- ========================= --}}

        @if($type == 'peminjaman')

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div class="max-h-[420px] overflow-y-auto">
                    <table class="min-w-full text-sm text-gray-700">
                        <thead class="text-white text-xs uppercase tracking-wider sticky top-0 z-10"
                            style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">
                            <tr>
                                <th class="py-4 px-6 font-semibold text-center">No</th>
                                <th class="py-4 px-6 font-semibold text-left">Kode</th>
                                <th class="py-4 px-6 font-semibold text-left">Tanggal</th>
                                <th class="py-4 px-6 font-semibold text-left">Karyawan</th>
                                <th class="py-4 px-6 font-semibold text-left">Alat</th>
                                <th class="py-4 px-6 font-semibold text-center">Detail</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @php
                                $invalidDate = request('start_date') && request('end_date') && request('end_date') < request('start_date');
                            @endphp

                            {{-- ❌ Kalau tanggal salah --}}
                            @if($invalidDate)

                                <tr>
                                    <td colspan="6" class="py-12 text-center text-red-500 font-semibold">
                                        ⚠️ Tanggal akhir harus sama atau lebih besar dari tanggal awal
                                    </td>
                                </tr>

                            {{-- ✅ Kalau data ada --}}
                            @elseif($data->count() > 0)

                            @foreach($data as $row)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-4 px-6 text-center font-medium text-gray-600">
                                        {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                                    </td>

                                    <td class="py-4 px-6 font-bold text-[#5EA6FF]">
                                        {{ $row->transaction_code }}
                                    </td>

                                    <td class="py-4 px-6 text-gray-700">
                                        {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                                    </td>

                                    <td class="py-4 px-6 text-gray-700">
                                        {{ $row->borrower_name }}
                                    </td>

                                    <td class="py-4 px-6 text-gray-700 text-xs">
                                        @foreach($row->items as $item)
                                            <div>{{ $item->toolkit->toolkit_name ?? '-' }}</div>
                                        @endforeach
                                    </td>

                                    <td class="py-4 px-6 text-center">
                                        <button @click="openDetail = {{ $row->id }}"
                                            class="group relative inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg overflow-hidden"
                                            style="background: linear-gradient(135deg, #7FC4FF, #5EA6FF); box-shadow: 0 3px 12px rgba(94,166,255,0.35);">
                                            <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:scale-110"
                                                fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="relative z-10">Detail</span>
                                            <div
                                                class="absolute inset-0 bg-white/0 group-hover:bg-white/20 transition-all duration-300">
                                            </div>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                             {{-- ⚪ Kalau kosong --}}
                            @else

                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400">
                                        Tidak ada data transaksi pada periode ini
                                    </td>
                                </tr>

                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MODAL DETAIL PEMINJAMAN --}}
            @foreach($data as $row)
                <div x-show="openDetail === {{ $row->id }}" x-transition x-cloak
                    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">

                    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

                        <div class="px-6 py-4 flex justify-between items-center text-white"
                            style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">
                            <div>
                                <h3 class="text-lg font-bold">Detail Peminjaman Tools</h3>
                                <p class="text-sm text-white/80">Informasi lengkap transaksi</p>
                            </div>
                            <button @click="openDetail = null" class="text-2xl text-white/80 hover:text-white transition">✕</button>
                        </div>

                        <div class="p-6 overflow-auto flex-1 bg-gray-50">
                            <div class="grid grid-cols-2 gap-6 text-sm mb-6">

                                <div>
                                    <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Kode Transaksi</p>
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

                            <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                                <table class="w-full text-sm">
                                    <thead class="text-white text-xs uppercase tracking-wider"
                                        style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">
                                        <tr>
                                            <th class="py-3 px-4 font-semibold text-left">No Seri</th>
                                            <th class="py-3 px-4 font-semibold text-left">Nama Alat</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach($row->items as $item)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-4 py-3 text-gray-600 font-mono text-xs">
                                                    {{ $item->serial->serial_number ?? '-' }}</td>
                                                <td class="px-4 py-3 text-gray-800">{{ $item->toolkit->toolkit_name ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-white border-t border-gray-100 flex justify-end">
                            <button @click="openDetail = null"
                                class="group relative px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg overflow-hidden"
                                style="background: linear-gradient(135deg, #7FC4FF, #5EA6FF); box-shadow: 0 3px 12px rgba(94,166,255,0.35);">
                                <span class="relative z-10">Tutup</span>
                                <div class="absolute inset-0 bg-white/0 group-hover:bg-white/20 transition-all duration-300"></div>
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
                        <thead class="text-white text-xs uppercase tracking-wider sticky top-0 z-10"
                            style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">
                            <tr>
                                <th class="py-4 px-6 font-semibold text-center">No</th>
                                <th class="py-4 px-6 font-semibold text-left">Kode</th>
                                <th class="py-4 px-6 font-semibold text-left">Tgl Kembali</th>
                                <th class="py-4 px-6 font-semibold text-left">Karyawan</th>
                                <th class="py-4 px-6 font-semibold text-left">Alat</th>
                                <th class="py-4 px-6 font-semibold text-left">No Seri</th>
                                <th class="py-4 px-6 font-semibold text-left">Kondisi</th>
                                <th class="py-4 px-6 font-semibold text-left">Keterangan</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @php
                                $invalidDate = request('start_date') && request('end_date') && request('end_date') < request('start_date');
                            @endphp

                            {{-- ❌ Kalau tanggal salah --}}
                            @if($invalidDate)

                                <tr>
                                    <td colspan="8" class="py-12 text-center text-red-500 font-semibold">
                                        ⚠️ Tanggal akhir harus sama atau lebih besar dari tanggal awal
                                    </td>
                                </tr>

                            {{-- ✅ Kalau data ada --}}
                            @elseif($data->count() > 0)

                            @foreach($data as $row)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-4 px-6 text-center font-medium text-gray-600">
                                        {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                                    </td>

                                    <td class="py-4 px-6 font-bold text-[#5EA6FF]">
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
                                        <span
                                            class="px-2 py-1 rounded-lg text-xs font-semibold {{ $row->return_condition == 'BAIK' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $row->return_condition ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="py-4 px-6 text-gray-500">
                                        {{ $row->return_note ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                            {{-- ⚪ Kalau data kosong --}}
                            @else

                                <tr>
                                    <td colspan="8" class="py-12 text-center text-gray-400">
                                        Tidak ada data transaksi pada periode ini
                                    </td>
                                </tr>

                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        @endif

        <style>
            [x-cloak] {
                display: none !important;
            }

            input[type="date"]::-webkit-calendar-picker-indicator {
                cursor: pointer;
                opacity: .6;
            }
        </style>
        <div class="mt-6 flex justify-center">
            {{ $data->links() }}
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const filterForm = document.getElementById('filterForm');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');

    filterForm?.addEventListener('submit', function (e) {

    const start = startDate.value;
    const end = endDate.value;

    if (start && end && end < start) {

        endDate.classList.add('ring-2', 'ring-red-400');
        return;

    } else {

        endDate.classList.remove('ring-2', 'ring-red-400');

    }

});

    endDate?.addEventListener('change', function () {
        if (this.value >= startDate.value) {
            this.classList.remove('ring-2', 'ring-red-400');
        }
    });

    startDate?.addEventListener('change', function () {
        if (endDate.value >= this.value) {
            endDate.classList.remove('ring-2', 'ring-red-400');
        }
    });

});
</script>
@endsection
