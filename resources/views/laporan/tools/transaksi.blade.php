@extends('layouts.app')

@section('content')

<div class="w-full min-h-screen flex flex-col">

    {{-- HEADER --}}
    <div class="flex justify-between items-start mb-6 relative z-10">
        <div>
            <h1 class="text-4xl font-extrabold text-[#24a0b9] tracking-tight">Laporan</h1>
            <p class="text-[#24a0b9] text-sm font-semibold opacity-90">
                Laporan {{ ucfirst($type) }} Tools
            </p>
        </div>
    </div>


    {{-- FILTER BOX --}}
    <div class="bg-gradient-to-r from-[#2db3d3] to-[#42c5d6] p-6 rounded-2xl shadow-lg mb-4 border border-white/20">

        <form method="GET"
            action="{{ route('laporan.tools.transaksi') }}"
            class="flex flex-wrap items-end gap-4">

            <input type="hidden" name="type" value="{{ $type }}">

            <div class="flex items-center gap-2 text-white mb-2">
                <span class="text-sm font-bold tracking-wide italic">
                    Filter Periode
                </span>
            </div>


            <div class="flex gap-4">

                <div>
                    <label class="block text-[10px] text-white/90 mb-1 font-bold uppercase tracking-wider">
                        Dari Tanggal
                    </label>

                    <input type="date"
                        name="start_date"
                        value="{{ request('start_date') }}"
                        class="bg-white rounded-xl border-none py-2 px-4 text-sm text-gray-600 shadow-inner">
                </div>


                <div>
                    <label class="block text-[10px] text-white/90 mb-1 font-bold uppercase tracking-wider">
                        Sampai Tanggal
                    </label>

                    <input type="date"
                        name="end_date"
                        value="{{ request('end_date') }}"
                        class="bg-white rounded-xl border-none py-2 px-4 text-sm text-gray-600 shadow-inner">
                </div>

            </div>


            <div class="flex items-center gap-2 ml-auto">

                <button type="submit"
                    class="bg-white text-[#2eb3d3] px-6 py-2 rounded-xl shadow-md text-sm font-bold hover:bg-gray-50">
                    Filter
                </button>


                <a href="{{ route('laporan.tools.transaksi', ['type'=>$type]) }}"
                    class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl shadow text-sm font-bold">
                    Reset
                </a>


                <a href="{{ route('laporan.tools.export.pdf',[
'type'=>$type,
'start_date'=>request('start_date'),
'end_date'=>request('end_date')
]) }}"
                    class="bg-white text-gray-600 px-4 py-2 rounded-xl shadow-md text-sm font-bold border">
                    📄 Export PDF
                </a>


                <a href="{{ route('laporan.tools.export.excel',[
'type'=>$type,
'start_date'=>request('start_date'),
'end_date'=>request('end_date')
]) }}"
                    class="bg-white text-gray-600 px-4 py-2 rounded-xl shadow-md text-sm font-bold border">
                    📊 Export Excel
                </a>

            </div>

        </form>

    </div>


    {{-- TABS --}}
    <div class="flex items-center gap-3 mb-6">

        <div class="flex bg-gray-200/50 p-1 rounded-2xl border border-gray-200">

            <a href="{{ route('laporan.tools.transaksi',['type'=>'peminjaman']) }}"
                class="px-6 py-2 rounded-xl text-sm font-bold transition
{{ $type == 'peminjaman'
? 'bg-white shadow text-gray-700'
: 'text-gray-400' }}">

                ⇄ Peminjaman Tools

            </a>


            <a href="{{ route('laporan.tools.transaksi',['type'=>'pengembalian']) }}"
                class="px-6 py-2 rounded-xl text-sm font-bold transition
{{ $type == 'pengembalian'
? 'bg-white shadow text-gray-700'
: 'text-gray-400' }}">

                ↺ Pengembalian Tools

            </a>

        </div>


        <div class="bg-white border border-gray-200 rounded-2xl px-5 py-2 text-sm text-gray-400 font-bold shadow-sm">
            Total {{ ucfirst($type) }} :
            <span class="text-gray-600 ml-1">{{ $data->count() }}</span>
        </div>

    </div>



    {{-- ========================= --}}
    {{-- TABEL PEMINJAMAN --}}
    {{-- ========================= --}}

    @if($type == 'peminjaman')

    <div x-data="{ openDetail: null }">

        <div class="overflow-hidden rounded-3xl border border-gray-200 shadow-2xl bg-white">

            <table class="w-full text-sm border-collapse">

                <thead>

                    <tr class="bg-gradient-to-r from-[#2db3d3] to-[#42c5d6] text-white uppercase text-[11px] tracking-widest text-center">

                        <th class="px-6 py-4">NO</th>
                        <th class="px-6 py-4">KODE</th>
                        <th class="px-6 py-4">TANGGAL</th>
                        <th class="px-6 py-4">KARYAWAN</th>
                        <th class="px-6 py-4">ALAT</th>
                        <th class="px-6 py-4">AKSI</th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100">

                    @forelse($data as $row)

                    <tr class="hover:bg-gray-50 text-center">

                        <td class="px-6 py-5">
                            {{ $data->firstItem() + $loop->index }}
                        </td>


                        <td class="px-6 py-5 font-mono text-gray-500">
                            {{ $row->transaction_code }}
                        </td>


                        <td class="px-6 py-5 italic text-gray-600">
                            {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                        </td>


                        <td class="px-6 py-5 font-bold text-gray-700 uppercase">
                            {{ $row->borrower_name }}
                        </td>


                        <td class="px-6 py-5 text-[11px] font-semibold uppercase">

                            @foreach($row->items as $item)
                            <div>
                                {{ $item->toolkit->toolkit_name ?? '-' }}
                            </div>
                            @endforeach

                        </td>


                        <td class="px-6 py-5">

                            <button
                                @click="openDetail = {{ $row->id }}"
                                class="bg-gradient-to-b from-gray-400 to-gray-600 text-white px-5 py-1.5 rounded-full text-xs shadow-md hover:from-gray-500 hover:to-gray-700 flex items-center gap-2 mx-auto">

                                👁 Detail

                            </button>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="6"
                            class="py-20 text-center text-gray-400 italic">

                            Data tidak ditemukan

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>



        {{-- MODAL DETAIL --}}
        @foreach($data as $row)

        <div
            x-show="openDetail === {{ $row->id }}"
            x-transition
            x-cloak
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

            <div class="bg-white w-full max-w-md rounded-xl shadow-xl p-6">

                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold">Detail Peminjaman</h3>

                    <button @click="openDetail = null">
                        ✕
                    </button>
                </div>


                <div class="grid grid-cols-2 gap-4 text-sm mb-4">

                    <div>
                        <p class="font-semibold">Kode</p>
                        <p>{{ $row->transaction_code }}</p>
                    </div>

                    <div>
                        <p class="font-semibold">Karyawan</p>
                        <p>{{ $row->borrower_name }}</p>
                    </div>

                    <div>
                        <p class="font-semibold">Tanggal</p>
                        <p>{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</p>
                    </div>

                    <div>
                        <p class="font-semibold">Client</p>
                        <p>{{ $row->client_name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="font-semibold">Project</p>
                        <p>{{ $row->project ?? '-' }}</p>
                    </div>

                    <div class="col-span-2">
                        <div class="bg-gray-100 rounded-xl px-4 py-3 shadow">
                            {{ $row->purpose ?? '-' }}
                        </div>
                    </div>

                </div>


                <div class="bg-gray-50 rounded-lg overflow-hidden">

                    <table class="w-full text-sm">

                        <thead class="bg-gradient-to-r from-[#2db3d3] to-[#42c5d6] text-white">

                            <tr>
                                <th class="px-4 py-2 text-left">NO SERI</th>
                                <th class="px-4 py-2 text-left">NAMA ALAT</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($row->items as $item)

                            <tr>

                                <td class="px-4 py-2">
                                    {{ $item->serial->serial_number ?? '-' }}
                                </td>

                                <td class="px-4 py-2">
                                    {{ $item->toolkit->toolkit_name ?? '-' }}
                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                <div class="text-right mt-4">

                    <button
                        @click="openDetail = null"
                        class="bg-gray-200 px-4 py-2 rounded-lg">

                        Tutup

                    </button>

                </div>

            </div>

        </div>

        @endforeach

    </div>


    {{-- ========================= --}}
    {{-- TABEL PENGEMBALIAN --}}
    {{-- ========================= --}}

    @else

    <div class="overflow-hidden rounded-3xl border border-gray-200 shadow-2xl bg-white">

        <table class="w-full text-sm">

            <thead>

                <tr class="bg-gradient-to-r from-[#2db3d3] to-[#42c5d6] text-white text-center text-[11px] uppercase tracking-widest">

                    <th class="px-6 py-4">NO</th>
                    <th class="px-6 py-4">KODE</th>
                    <th class="px-6 py-4">TGL KEMBALI</th>
                    <th class="px-6 py-4">KARYAWAN</th>
                    <th class="px-6 py-4">ALAT</th>
                    <th class="px-6 py-4">NO SERI</th>
                    <th class="px-6 py-4">KONDISI</th>
                    <th class="px-6 py-4">KETERANGAN</th>

                </tr>

            </thead>


            <tbody class="divide-y divide-gray-100">

                @forelse($data as $row)

                <tr class="text-center hover:bg-gray-50">

                    <td class="px-6 py-5">
                        {{ $data->firstItem() + $loop->index }}
                    </td>

                    <td class="px-6 py-5">
                        {{ $row->transaction->transaction_code }}
                    </td>

                    <td class="px-6 py-5">
                        {{ \Carbon\Carbon::parse($row->return_date)->format('d M Y') }}
                    </td>

                    <td class="px-6 py-5">
                        {{ $row->transaction->borrower_name }}
                    </td>

                    <td class="px-6 py-5">
                        {{ $row->toolkit->toolkit_name ?? '-' }}
                    </td>

                    <td class="px-6 py-5">
                        {{ $row->serial->serial_number ?? '-' }}
                    </td>

                    <td class="px-6 py-5">
                        {{ $row->return_condition ?? '-' }}
                    </td>

                    <td class="px-6 py-5">
                        {{ $row->return_note ?? '-' }}
                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="8"
                        class="py-20 text-center text-gray-400 italic">

                        Tidak ada data

                    </td>

                </tr>

                @endforelse



            </tbody>

        </table>

        <div class="flex justify-center mt-6">
            {{ $data->links() }}
        </div>

    </div>

    @endif

</div>



<style>
    body {
        background: transparent !important;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: .6;
    }
</style>

@endsection