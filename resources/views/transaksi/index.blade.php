@extends('layouts.app')

@section('content')

    <div class="relative max-w-7xl mx-auto pb-40" x-data="{ openReturn: null }">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6">

            <div>
                <h1 class="text-3xl font-bold text-[#1CA7B6] tracking-wide">
                    Transaksi Consumable
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Kelola peminjaman dan pengembalian Consumable
                </p>
            </div>

            <div class="flex gap-3">

                <a href="{{ url()->previous() }}"
                    class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">
                    ← Kembali
                </a>

                <a href="{{ route('transaksi.create') }}"
                    class="px-4 py-2 text-sm text-white rounded-lg shadow-md hover:opacity-90 transition"
                    style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                    + Permintaan Consumable
                </a>

            </div>

        </div>


        {{-- FILTER --}}
        <form method="GET" action="{{ route('transaksi.index') }}" class="mb-5">

            <div
                class="bg-gradient-to-b from-[#7ED6DF] to-[#1CA7B6] p-4 rounded-2xl shadow-lg flex flex-wrap items-center gap-3">

                <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari alat atau karyawan..."
                    class="flex-1 bg-white rounded-xl shadow-inner px-4 py-2 text-sm outline-none">

                <span class="text-white text-sm">Tanggal</span>

                <input type="date" name="start_date" value="{{ request('start_date') }}"
                    class="px-3 py-2 rounded-lg bg-white text-gray-700 text-sm">

                <span class="text-white text-sm">s/d</span>

                <input type="date" name="end_date" value="{{ request('end_date') }}"
                    class="px-3 py-2 rounded-lg bg-white text-gray-700 text-sm">

                <button
                    class="px-4 py-2 text-sm bg-white text-[#1CA7B6] font-semibold rounded-lg shadow hover:bg-gray-100 transition">
                    Filter
                </button>

                <a href="{{ route('transaksi.index') }}"
                    class="px-4 py-2 text-sm bg-red-500 hover:bg-red-600 text-white rounded-lg shadow">
                    Reset
                </a>

            </div>

        </form>


        {{-- TABLE --}}
        <div class="rounded-2xl shadow-lg overflow-hidden bg-white">

            <table class="w-full text-sm">

                <thead>
                    <tr class="text-white text-xs uppercase tracking-wider"
                        style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">

                        <th class="py-3 text-center">No</th>
                        <th class="py-3 text-left pl-4">Kode</th>
                        <th class="py-3 text-left">Tanggal</th>
                        <th class="py-3 text-left">Karyawan</th>
                        <th class="py-3 text-left">Consumable</th>
                        <th class="py-3 text-center">Minta</th>
                        <th class="py-3 text-center">Kembali</th>
                        <th class="py-3 text-center">Sisa</th>
                        <th class="py-3 text-center">Aksi</th>

                    </tr>
                </thead>

                <tbody class="text-gray-700">

                    @forelse($transactions as $trx)

                        <tr class="border-b hover:bg-gray-50 transition">

                            <td class="py-3 text-center">
                                {{ $loop->iteration }}
                            </td>

                            <td class="py-3 pl-4 font-semibold text-[#1CA7B6]">
                                {{ $trx->transaction_code }}
                            </td>

                            <td class="py-3">
                                {{ \Carbon\Carbon::parse($trx->date)->format('d M Y') }}
                            </td>

                            <td class="py-3">
                                {{ $trx->borrower_name }}
                            </td>

                            <td class="py-3">

                                @foreach($trx->items as $item)
                                    <div>{{ $item->consumable->name }}</div>
                                @endforeach

                            </td>

                            <td class="py-3 text-center">

                                @foreach($trx->items as $item)
                                    <div>{{ $item->qty }} {{ $item->consumable->unit }}</div>
                                @endforeach

                            </td>

                            <td class="py-3 text-center">

                                @foreach($trx->items as $item)
                                    <div>{{ ($item->qty_return ?? 0) == 0 ? '-' : $item->qty_return }}</div>
                                @endforeach

                            </td>

                            <td class="py-3 text-center">

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

                            <td class="py-3 text-center">

                                @if(!$trx->is_confirm)

                                    <div class="flex justify-center gap-4 text-lg">

                                        <a href="{{ route('transaksi.edit', $trx->id) }}"
                                            class="text-gray-600 hover:text-blue-600 transition">
                                            ✏
                                        </a>

                                        <form action="{{ route('transaksi.destroy', $trx->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button class="text-gray-600 hover:text-red-600 transition">
                                                🗑
                                            </button>

                                        </form>

                                        <form action="{{ route('transaksi.confirm', $trx->id) }}" method="POST">
                                            @csrf

                                            <button class="px-3 py-1 text-xs text-white rounded-full"
                                                style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                                                confirm
                                            </button>

                                        </form>

                                    </div>

                                @else

                                    <button @click="openReturn = '{{ $trx->id }}'"
                                        class="px-3 py-1 text-xs text-[#1CA7B6] rounded-full bg-gray-100 hover:bg-gray-200">
                                        Pengembalian
                                    </button>

                                    <span class="px-3 py-1 text-xs text-white bg-green-500 rounded-full">
                                        ✔ confirmed
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9" class="py-8 text-center text-gray-400">
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

                <div x-show="openReturn === '{{ $trx->id }}'" x-cloak x-data="{ selected: null }" class="modal">

                    <div class="modal-box">

                        <form action="{{ route('transaksi.return', $trx->id) }}" method="POST">
                            @csrf

                            {{-- HEADER --}}
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">
                                    Proses Pengembalian
                                </h3>

                                <button type="button" @click="openReturn = null" class="text-gray-500 text-xl">
                                    &times;
                                </button>
                            </div>

                            {{-- INFO --}}
                            <div class="grid grid-cols-2 gap-4 mb-4">

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

                            {{-- TABLE ITEM --}}
                            <table class="w-full text-sm border">

                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="p-2 text-center">Pilih</th>
                                        <th class="p-2 text-left">Consumable</th>
                                        <th class="p-2 text-center">Sisa</th>
                                        <th class="p-2 text-center">Qty Return</th>
                                        <th class="p-2 text-left">Keterangan</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @foreach($trx->items as $item)

                                        @php
                                            $sisa = $item->qty - ($item->qty_return ?? 0);
                                        @endphp

                                        @if($sisa > 0)

                                            <tr>

                                                <td class="p-2 text-center">
                                                    <input type="radio" name="selected_item" value="{{ $item->id }}" x-model="selected">
                                                </td>

                                                <td class="p-2">
                                                    {{ $item->consumable->name }}
                                                </td>

                                                <td class="p-2 text-center">
                                                    {{ $sisa }}
                                                </td>

                                                <td class="p-2 text-center">
                                                    <input type="number" name="items[{{ $item->id }}][qty]" min="1" max="{{ $sisa }}"
                                                        class="w-20 border rounded text-center" :disabled="selected != '{{ $item->id }}'">
                                                </td>

                                                <td class="p-2">
                                                    <input type="text" name="items[{{ $item->id }}][note]" placeholder="Keterangan"
                                                        class="w-full border rounded px-2" :disabled="selected != '{{ $item->id }}'">
                                                </td>

                                            </tr>

                                        @endif

                                    @endforeach

                                </tbody>

                            </table>

                            {{-- BUTTON --}}
                            <div class="flex justify-end gap-2 mt-4">

                                <button type="button" @click="openReturn = null" class="px-4 py-2 border rounded">
                                    Batal
                                </button>

                                <button type="submit" :disabled="!selected" class="btn-primary">
                                    Kembalikan
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            @endif
        @endforeach

        <style>
            .modal {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, .7);
                display: flex;
                align-items: center;
                justify-content: center;
                backdrop-filter: blur(4px);
            }

            .modal-box {
                background: white;
                padding: 1.5rem;
                width: 100%;
                max-width: 500px;
                border-radius: 1rem;
            }

            .btn-primary {
                background: linear-gradient(180deg, #5FD0DF, #1CA7B6);
                color: white;
                padding: .6rem 1.2rem;
                border-radius: .5rem;
            }
        </style>

    </div>

@endsection