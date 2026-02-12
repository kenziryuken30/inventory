@extends('layouts.app')

@section('content')
<div class="p-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-sky-600">Transaksi Consumable</h1>
            <p class="text-gray-500 text-sm">Kelola permintaan consumable</p>
        </div>

        <a href="{{ route('transaksi.create') }}"
           class="px-3 py-1 rounded border text-sky-600 hover:bg-sky-50">
            + Permintaan Consumable
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gradient-to-r from-sky-400 to-cyan-400 text-white">
                <tr>
                    <th class="px-4 py-3 text-left">ID</th>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Karyawan</th>
                    <th class="px-4 py-3 text-left">Consumable</th>
                    <th class="px-4 py-3 text-center">Jumlah Minta</th>
                    <th class="px-4 py-3 text-center">Jumlah kembali</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($transactions as $trx)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $trx->id }}</td>

                        <td class="px-4 py-2">
                            {{ optional($trx->date)->format('d-m-Y') }}
                        </td>

                        <td class="px-4 py-2">
                            {{ $trx->borrower_name }}
                        </td>

                        <td class="px-4 py-2">
                            @foreach($trx->items as $item)
                                <div>{{ $item->consumable->name }}</div>
                            @endforeach
                        </td>

                        <td class="px-4 py-2 text-center">
                            @foreach($trx->items as $item)
                                <div>{{ $item->qty }} {{ $item->consumable->unit }}</div>
                            @endforeach
                        </td>

                        <td class="px-4 py-2 text-center">
                            @if($trx->is_confirm)
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded">
                                    Confirmed
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded">
                                    Draft
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-2 text-center">
                            <a href="{{ route('transaksi.edit', $trx->id) }}"
                            class="text-sky-600 hover:underline text-sm">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-400">
                            Belum ada transaksi consumable
                        </td>
                    </tr>
                @endforelse
                </tbody>

        </table>
    </div>

</div>
@endsection
