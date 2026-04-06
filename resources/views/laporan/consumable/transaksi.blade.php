@extends('layouts.app')

@section('content')

    {{-- x-cloak harus di paling atas --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>

    {{-- x-data di root, semua modal harus di dalam ini --}}
    <div class="max-w-7xl mx-auto" x-data="{ openDetail: null }" x-cloak>

        {{-- ================= HEADER ================= --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-3xl font-bold text-[#1CA7B6] tracking-tight">Laporan Transaksi Consumable</h2>
                <p class="text-sm text-gray-500 mt-1">Rekap data pengeluaran dan pengembalian barang consumable</p>
            </div>
        </div>

        {{-- ================= FILTER ================= --}}
        <form method="GET" action="{{ route('laporan.consumable.transaksi') }}" id="filterForm"
            class="mb-6 p-4 rounded-2xl shadow-md flex flex-wrap gap-4 items-end"
            style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">

            {{-- Hidden type akan di-update oleh JS saat toggle diklik --}}
            <input type="hidden" name="type" id="filterType" value="{{ $type }}">

            <div class="flex-1 min-w-[200px]">
                <label class="text-white text-sm font-semibold block mb-1">Nama Peminta</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama Peminta..."
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

            {{-- Validasi tanggal error --}}
            <div id="dateError" class="hidden w-full text-sm text-white bg-red-500/80 px-4 py-2 rounded-xl">
                ⚠️ Tanggal akhir harus sama atau lebih besar dari tanggal awal
            </div>

            <div class="flex gap-2 items-end">
                <button type="submit" id="filterBtn"
                    class="bg-white text-[#5EA6FF] px-5 py-2.5 rounded-xl font-bold shadow-sm hover:bg-gray-100 hover:shadow-md transition-all duration-300 text-sm hover:-translate-y-0.5">
                    🔎 Filter
                </button>

                {{-- RESET --}}
                <a href="{{ route('laporan.consumable.transaksi', ['type' => $type]) }}"
                    class="group relative px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg overflow-hidden"
                    style="background: linear-gradient(135deg, #C084FC, #A855F7); color: white; box-shadow: 0 4px 15px rgba(168,85,247,0.35);">
                    <span class="relative z-10 flex items-center gap-1.5">
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:-rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182"/></svg>
                        Reset
                    </span>
                    <div class="absolute inset-0 bg-white/0 group-hover:bg-white/20 transition-all duration-300"></div>
                </a>

                {{-- PDF --}}
                <a href="{{ route('laporan.consumable.export.pdf', array_merge(request()->query(), ['type' => $type])) }}"
                    class="group relative px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg overflow-hidden"
                    style="background: linear-gradient(135deg, #FB7185, #E11D48); color: white; box-shadow: 0 4px 15px rgba(225,29,72,0.35);">
                    <span class="relative z-10 flex items-center gap-1.5">
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6zm3-7h6v1.5H9V13zm0 3h6v1.5H9V16zm0-6h3v1.5H9V10z"/></svg>
                        PDF
                    </span>
                    <div class="absolute inset-0 bg-white/0 group-hover:bg-white/20 transition-all duration-300"></div>
                </a>

                {{-- EXCEL --}}
                <a href="{{ route('laporan.consumable.export.excel', array_merge(request()->query(), ['type' => $type])) }}"
                    class="group relative px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg overflow-hidden"
                    style="background: linear-gradient(135deg, #34D399, #059669); color: white; box-shadow: 0 4px 15px rgba(5,150,105,0.35);">
                    <span class="relative z-10 flex items-center gap-1.5">
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 20V4h7v5h5v11H6zm2-6h3v1.5H8V14zm0 3h3v1.5H8V17zm5-6h3v1.5h-3V11zm0 3h3v1.5h-3V14zm0 3h3v1.5h-3V17z"/></svg>
                        Excel
                    </span>
                    <div class="absolute inset-0 bg-white/0 group-hover:bg-white/20 transition-all duration-300"></div>
                </a>
            </div>
        </form>

        {{-- ================= TOGGLE ================= --}}
        <div class="flex items-center gap-4 mb-6">
            <div class="flex bg-gray-200 p-1 rounded-xl shadow-inner">
                <button type="button" id="togglePengeluaran"
                    class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $type == 'pengeluaran' ? 'bg-white shadow text-[#1CA7B6]' : 'text-gray-600 hover:text-gray-800' }}">
                    📤 Pengeluaran
                </button>
                <button type="button" id="togglePengembalian"
                    class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $type == 'pengembalian' ? 'bg-white shadow text-[#1CA7B6]' : 'text-gray-600 hover:text-gray-800' }}">
                    📥 Pengembalian
                </button>
            </div>
            <div class="text-sm text-gray-500">
                Total {{ ucfirst($type) }} : <span class="font-bold text-[#1CA7B6]">{{ $totalTransaksi }}</span>
            </div>
        </div>

        {{-- ================= TABLE ================= --}}
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
                                    {{ $data->firstItem() + $loop->index }}
                                </td>
                                <td class="py-4 px-6 font-bold text-[#1CA7B6]">
                                    @if($type == 'pengeluaran')
                                        {{ $row->transaction_code }}
                                    @else
                                        {{ $row->transaction->transaction_code }}
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-gray-700">
                                    @if($type == 'pengeluaran')
                                        {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($row->transaction->return_date)->format('d M Y') }}
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-gray-700">
                                    @if($type == 'pengeluaran')
                                        {{ $row->borrower_name }}
                                    @else
                                        {{ $row->transaction->borrower_name }}
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-gray-700">
                                    @if($type == 'pengeluaran')
                                        @foreach($row->items as $item)
                                            <div>{{ $item->consumable->name }}</div>
                                        @endforeach
                                    @else
                                        {{ $row->consumable->name }}
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center font-bold text-[#1CA7B6]">
                                    @if($type == 'pengeluaran')
                                        {{ $row->items->sum('qty') }}
                                    @else
                                        {{ $row->qty_return }}
                                    @endif
                                </td>
                                @if($type == 'pengeluaran')
                                    <td class="py-4 px-6 text-center">
                                        <button @click="openDetail = {{ $row->id }}"
                                            class="group relative inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg overflow-hidden"
                                            style="background: linear-gradient(135deg, #5FD0DF, #1CA7B6); box-shadow: 0 3px 12px rgba(28,167,182,0.35);">
                                            <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span class="relative z-10">Detail</span>
                                            <div class="absolute inset-0 bg-white/0 group-hover:bg-white/20 transition-all duration-300"></div>
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
                                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <span>Tidak ada data transaksi pada periode ini</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= PAGINATION ================= --}}
        <div class="mt-6 flex justify-center">
            {{ $data->appends(request()->query())->links() }}
        </div>

        {{-- ================= MODAL PENGELUARAN ================= --}}
        @if($type == 'pengeluaran')
            @foreach($data as $row)
                <div x-show="openDetail === {{ $row->id }}"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
                    @click.self="openDetail = null">

                    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
                        @click.stop>

                        <div class="px-6 py-4 flex justify-between items-center text-white"
                            style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                            <div>
                                <h3 class="text-lg font-bold">Detail Transaksi Consumable</h3>
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
                                    <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Tanggal</p>
                                    <p class="text-gray-800">{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</p>
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

                        <div class="px-6 py-4 bg-white border-t border-gray-100 flex justify-end">
                            <button @click="openDetail = null"
                                class="group relative px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg overflow-hidden"
                                style="background: linear-gradient(135deg, #5FD0DF, #1CA7B6); box-shadow: 0 3px 12px rgba(28,167,182,0.35);">
                                <span class="relative z-10">Tutup</span>
                                <div class="absolute inset-0 bg-white/0 group-hover:bg-white/20 transition-all duration-300"></div>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        {{-- ================= MODAL PENGEMBALIAN ================= --}}
        @if($type == 'pengembalian')
            @foreach($data as $row)
                <div x-show="openDetail === {{ $row->id }}"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4"
                    @click.self="openDetail = null">

                    <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
                        @click.stop>

                        <div class="px-6 py-4 flex justify-between items-center text-white"
                            style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                            <div>
                                <h3 class="text-lg font-bold">Detail Pengembalian Consumable</h3>
                            </div>
                            <button @click="openDetail = null" class="text-2xl text-white/80 hover:text-white transition">✕</button>
                        </div>

                        <div class="p-6 overflow-auto flex-1 bg-gray-50">
                            <div class="grid grid-cols-2 gap-6 text-sm mb-4">
                                <div>
                                    <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Kode Transaksi</p>
                                    <p class="text-gray-800 font-semibold">{{ $row->transaction->transaction_code }}</p>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-1">Tanggal Pengembalian</p>
                                    <p class="text-gray-800">
                                        {{ \Carbon\Carbon::parse($row->transaction->return_date)->format('d M Y') }}
                                    </p>
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

                        <div class="px-6 py-4 bg-white border-t border-gray-100 flex justify-end">
                            <button @click="openDetail = null"
                                class="group relative px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg overflow-hidden"
                                style="background: linear-gradient(135deg, #5FD0DF, #1CA7B6); box-shadow: 0 3px 12px rgba(28,167,182,0.35);">
                                <span class="relative z-10">Tutup</span>
                                <div class="absolute inset-0 bg-white/0 group-hover:bg-white/20 transition-all duration-300"></div>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const filterForm = document.getElementById('filterForm');
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');
            const filterType = document.getElementById('filterType');
            const dateError = document.getElementById('dateError');
            const togglePengeluaran = document.getElementById('togglePengeluaran');
            const togglePengembalian = document.getElementById('togglePengembalian');

            // ★ VALIDASI TANGGAL ★
            filterForm?.addEventListener('submit', function (e) {
                const start = startDate.value;
                const end = endDate.value;

                // Kalau keduanya diisi, cek logic
                if (start && end && end < start) {
                    e.preventDefault();
                    dateError.classList.remove('hidden');
                    endDate.classList.add('ring-2', 'ring-red-400');
                    return;
                }

                dateError.classList.add('hidden');
                endDate.classList.remove('ring-2', 'ring-red-400');
            });

            // Auto-hide error saat user rubah tanggal
            endDate?.addEventListener('change', function () {
                const start = startDate.value;
                if (start && this.value && this.value >= start) {
                    dateError.classList.add('hidden');
                    this.classList.remove('ring-2', 'ring-red-400');
                }
            });

            startDate?.addEventListener('change', function () {
                const end = endDate.value;
                if (this.value && end && end >= this.value) {
                    dateError.classList.add('hidden');
                    endDate.classList.remove('ring-2', 'ring-red-400');
                }
            });

            // ★ TOGGLE TYPE (pakai form submit, bukan link) ★
            function switchType(type) {
                filterType.value = type;

                // Update tampilan toggle
                if (type === 'pengeluaran') {
                    togglePengeluaran.classList.add('bg-white', 'shadow', 'text-[#1CA7B6]');
                    togglePengeluaran.classList.remove('text-gray-600');
                    togglePengembalian.classList.remove('bg-white', 'shadow', 'text-[#1CA7B6]');
                    togglePengembalian.classList.add('text-gray-600');
                } else {
                    togglePengembalian.classList.add('bg-white', 'shadow', 'text-[#1CA7B6]');
                    togglePengembalian.classList.remove('text-gray-600');
                    togglePengeluaran.classList.remove('bg-white', 'shadow', 'text-[#1CA7B6]');
                    togglePengeluaran.classList.add('text-gray-600');
                }

                // Submit form dengan type baru
                filterForm.submit();
            }

            togglePengeluaran?.addEventListener('click', () => switchType('pengeluaran'));
            togglePengembalian?.addEventListener('click', () => switchType('pengembalian'));
        });
    </script>

@endsection