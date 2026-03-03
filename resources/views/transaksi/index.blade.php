@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto" x-data="{ openReturn: null }">

        {{-- ================= HEADER ================= --}}
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Transaksi Consumable</h2>

            <a href="{{ route('transaksi.create') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                + Permintaan Consumable
            </a>
        </div>


        {{-- ================= FLASH ================= --}}
        @if(session('success'))
            <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif


        {{-- ================= FILTER ================= --}}
        <form method="GET" action="{{ route('transaksi.index') }}"
            class="mb-6 bg-gradient-to-r from-cyan-500 to-teal-500 p-4 rounded-xl shadow flex flex-wrap gap-4 items-end">
            <div>
                <label class="text-white text-sm">Nama Karyawan</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                    class="px-4 py-2 rounded-lg shadow w-64">
            </div>

            <div>
                <label class="text-white text-sm">Dari</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="px-3 py-2 rounded-lg shadow">
            </div>

            <div>
                <label class="text-white text-sm">Sampai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-3 py-2 rounded-lg shadow">
            </div>

            <div class="flex gap-2">
                <button class="bg-white text-teal-600 px-4 py-2 rounded-lg shadow font-semibold">
                    Filter
                </button>

                <a href="{{ route('transaksi.index') }}" class="bg-red-500 text-white px-4 py-2 rounded-lg shadow">
                    Reset
                </a>
            </div>

        </form>


        {{-- ================= TABLE ================= --}}
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <table class="min-w-full text-sm text-gray-700">

                <thead class="bg-gray-100 text-xs uppercase tracking-wider text-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-center">No</th>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Karyawan</th>
                        <th class="px-4 py-3 text-left">Consumable</th>
                        <th class="px-4 py-3 text-center">Minta</th>
                        <th class="px-4 py-3 text-center">Kembali</th>
                        <th class="px-4 py-3 text-center">Sisa</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($transactions as $trx)

                        <tr class="hover:bg-gray-50 transition">

                            <td class="px-4 py-3 text-center">
                                {{ $transactions->count() - $loop->index }}
                            </td>

                            <td class="px-4 py-3 font-semibold text-blue-600">
                                {{ $trx->transaction_code }}
                            </td>

                            <td class="px-4 py-3">
                                {{ \Carbon\Carbon::parse($trx->date)->format('d-m-Y') }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $trx->borrower_name }}
                            </td>

                            <td class="px-4 py-3">
                                @foreach($trx->items as $item)
                                    <div>{{ $item->consumable->name }}</div>
                                @endforeach
                            </td>

                            <td class="px-4 py-3 text-center">
                                @foreach($trx->items as $item)
                                    <div>{{ $item->qty }} {{ $item->consumable->unit }}</div>
                                @endforeach
                            </td>

                            <td class="px-4 py-3 text-center">
                                @foreach($trx->items as $item)
                                    <div>
                                        {{ ($item->qty_return ?? 0) == 0 ? '-' : $item->qty_return }}
                                    </div>
                                @endforeach
                            </td>

                            <td class="px-4 py-3 text-center">

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

                            <td class="px-4 py-3 text-center space-x-2">

                                @if(!$trx->is_confirm)

                                    <a href="{{ route('transaksi.edit', $trx->id) }}"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-xs">
                                        Edit
                                    </a>

                                    <form action="{{ route('transaksi.destroy', $trx->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-xs">
                                            Hapus
                                        </button>
                                    </form>

                                    <form action="{{ route('transaksi.confirm', $trx->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md text-xs">
                                            Confirm
                                        </button>
                                    </form>

                                @else

                                    <button @click="openReturn = '{{ $trx->id }}'"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md text-xs">
                                        Return
                                    </button>

                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">
                                        Confirmed
                                    </span>

                                @endif

                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-gray-400">
                                Belum ada transaksi
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>


        {{-- ================= POPUP RETURN ================= --}}
        @foreach($transactions as $trx)
            @if($trx->is_confirm)

                <div x-show="openReturn === '{{ $trx->id }}'" x-cloak x-data="{ selected: null }"
                    class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">

                    <div class="bg-white w-[900px] rounded-2xl shadow-2xl p-6 relative">

                        <form action="{{ route('transaksi.return', $trx->id) }}" method="POST">
                            @csrf

                            {{-- HEADER --}}
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        Proses Pengembalian
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        Pengembalian sisa Consumable
                                    </p>
                                </div>

                                <button type="button" @click="openReturn = null" class="text-gray-400 hover:text-gray-600 text-xl">
                                    &times;
                                </button>
                            </div>

                            {{-- INFO ATAS --}}
                            <div class="grid grid-cols-2 gap-4 mb-6">

                                <div>
                                    <label class="text-sm text-gray-600">Nama Karyawan</label>
                                    <input type="text" value="{{ $trx->borrower_name }}"
                                        class="w-full mt-1 px-3 py-2 border rounded-lg bg-gray-100" readonly>
                                </div>

                                <div>
                                    <label class="text-sm text-gray-600">Tanggal Return</label>
                                    <input type="date" name="return_date" value="{{ date('Y-m-d') }}"
                                        class="w-full mt-1 px-3 py-2 border rounded-lg">
                                </div>

                            </div>

                            {{-- TABLE --}}
                            <div class="rounded-xl overflow-hidden border">

                                <table class="w-full text-sm">
                                    <thead class="bg-gradient-to-r from-cyan-500 to-teal-500 text-white">
                                        <tr>
                                            <th class="p-3 text-center">Pilih</th>
                                            <th class="p-3 text-left">Nama Consumable</th>
                                            <th class="p-3 text-center">Stok</th>
                                            <th class="p-3 text-center">Qty Return</th>
                                            <th class="p-3 text-left">Keterangan</th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white divide-y">
                                        @foreach($trx->items as $item)

                                            @php
                                                $sisa = $item->qty - ($item->qty_return ?? 0);
                                            @endphp

                                            @if($sisa > 0)

                                                <tr class="hover:bg-gray-50 transition">

                                                    <td class="p-3 text-center">
                                                        <input type="radio" name="selected_item" value="{{ $item->id }}" x-model="selected">
                                                    </td>

                                                    <td class="p-3">
                                                        {{ $item->consumable->name }}
                                                    </td>

                                                    <td class="p-3 text-center">
                                                        {{ $sisa }}
                                                    </td>

                                                    <td class="p-3 text-center">
                                                        <input type="number" name="items[{{ $item->id }}][qty]" min="1" max="{{ $sisa }}"
                                                            class="w-24 px-2 py-1 border rounded-md text-center"
                                                            :disabled="selected != '{{ $item->id }}'">
                                                    </td>

                                                    <td class="p-3">
                                                        <input type="text" name="items[{{ $item->id }}][note]" placeholder="Keterangan..."
                                                            class="w-full px-2 py-1 border rounded-md"
                                                            :disabled="selected != '{{ $item->id }}'">
                                                    </td>

                                                </tr>

                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- BUTTON --}}
                            <div class="flex justify-end gap-3 mt-6">
                                <button type="button" @click="openReturn = null" class="px-4 py-2 rounded-lg border">
                                    Batal
                                </button>

                                <button type="submit" :disabled="!selected"
                                    class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg shadow">
                                    Kembalikan
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            @endif
        @endforeach

    </div>
@endsection