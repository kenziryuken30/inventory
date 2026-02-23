@extends('layouts.app')

@section('content')

<div class="p-6"
     x-data="{ openReturn: null }">

```
{{-- HEADER --}}
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

{{-- ALERT --}}
@if(session('success'))
    <div class="mb-3 px-4 py-2 bg-green-100 text-green-700 rounded">
        {{ session('success') }}
    </div>
@endif


{{-- TABLE --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gradient-to-r from-sky-400 to-cyan-400 text-white">
            <tr>
                <th class="px-4 py-3 text-left">ID</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-left">Karyawan</th>
                <th class="px-4 py-3 text-left">Consumable</th>
                <th class="px-4 py-3 text-center">Jumlah Minta </th>
                <th class="px-4 py-3 text-center">Jumlah Kembali</th>
                <th class="px-4 py-3 text-center">Sisa</th>
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
            <td class="px-4 py-2">{{ $trx->borrower_name }}</td>

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
                @foreach($trx->items as $item)
                    <div>
                        {{ $item->qty_return ?? 0 }} {{ $item->consumable->unit }}
                    </div>
                @endforeach
            </td>

            <td class="px-4 py-2 text-center">
                @foreach($trx->items as $item)

                    @if(($item->qty_return ?? 0) == 0)
                        <div class="text-yellow-600 font-semibold">
                            Belum dikembalikan
                        </div>
                    @else
                        <div>
                            {{ $item->qty - $item->qty_return }} {{ $item->consumable->unit }}
                        </div>
                    @endif

                @endforeach
            </td>

            <td class="px-4 py-2 text-center">

                @if(!$trx->is_confirm)

                    <div class="flex justify-center gap-2">

                        {{-- EDIT --}}
                        <a href="{{ route('transaksi.edit', $trx->id) }}"
                        class="text-sky-600 hover:underline text-xs">
                            Edit
                        </a>

                        {{-- HAPUS --}}
                        <form action="{{ route('transaksi.destroy', $trx->id) }}"
                            method="POST"
                            onsubmit="return confirm('Hapus transaksi?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline text-xs">
                                Hapus
                            </button>
                        </form>

                        {{-- CONFIRM --}}
                        <form action="{{ route('transaksi.confirm', $trx->id) }}"
                            method="POST">
                            @csrf
                            <button class="bg-green-600 text-white px-2 py-1 rounded text-xs">
                                Confirm
                            </button>
                        </form>

                    </div>

                @else


                    {{-- BUTTON OPEN POPUP --}}
                    <button
                        @click="openReturn = '{{ $trx->id }}'"
                        class="bg-yellow-500 text-white px-3 py-1 rounded text-xs">
                        Return
                    </button>

                    <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-xs">
                        Confirmed
                    </span>

                @endif

            </td>
        </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-12 text-gray-400">
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

<div x-show="openReturn === '{{ $trx->id }}'"
     x-cloak
     x-data="{ selected: [] }"
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

    <div class="bg-white w-4/5 rounded-xl shadow-xl p-6">

        <form action="{{ route('transaksi.return', $trx->id) }}"
              method="POST">
            @csrf

            {{-- HEADER --}}
            <div class="flex justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold">
                        Pengembalian Consumable
                    </h3>
                </div>

                <button type="button"
                        @click="openReturn = null"
                        class="text-xl">&times;</button>
            </div>


            {{-- INFO --}}
            <div class="grid grid-cols-2 gap-4 mb-5">

                <div>
                    <label class="text-sm">Karyawan</label>
                    <input type="text"
                           value="{{ $trx->borrower_name }}"
                           class="w-full border rounded px-3 py-2 bg-gray-100"
                           readonly>
                </div>

                <div>
                    <label class="text-sm">Tanggal Return</label>
                    <input type="date"
                           name="return_date"
                           value="{{ date('Y-m-d') }}"
                           class="w-full border rounded px-3 py-2">
                </div>

            </div>


            {{-- TABLE ITEM --}}
            <div class="border rounded-lg overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-3 border">Pilih</th>
                            <th class="p-3 border">Consumable</th>
                            <th class="p-3 border">Qty</th>
                            <th class="p-3 border">Return</th>
                            <th class="p-3 border">Keterangan</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($trx->items as $item)
                    <tr>
                        <td class="p-3 border text-center">
                            <input type="checkbox"
                                   x-model="selected"
                                   value="{{ $item->id }}">
                        </td>

                        <td class="p-3 border">
                            {{ $item->consumable->name }}
                        </td>

                        <td class="p-3 border">
                            {{ $item->qty }}
                        </td>

                        <td class="p-3 border">
                            <input type="number"
                                   name="items[{{ $item->id }}][qty]"
                                   class="border rounded px-2 py-1 w-full"
                                   min="0"
                                   max="{{ $item->qty }}"
                                   :disabled="!selected.includes('{{ $item->id }}')">
                        </td>

                        <td class="p-3 border">
                            <input type="text"
                                   name="items[{{ $item->id }}][note]"
                                   class="border rounded px-2 py-1 w-full"
                                   :disabled="!selected.includes('{{ $item->id }}')">
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>


            {{-- FOOTER --}}
            <div class="flex justify-end gap-3 mt-6">

                <button type="button"
                        @click="openReturn = null"
                        class="px-4 py-2 border rounded-lg">
                    Batal
                </button>

                <button type="submit"
                        :disabled="selected.length === 0"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg">
                    Return
                </button>

            </div>

        </form>
    </div>
</div>

@endif
@endforeach
```

</div>

@endsection
