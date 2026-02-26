@extends('layouts.app')

@section('content')

<h2 class="text-xl font-bold mb-4">Laporan Transaksi Consumable</h2>

<form method="GET" action="{{ route('laporan.consumable.transaksi') }}"
      class="bg-gradient-to-r from-cyan-600 to-teal-500 p-4 rounded-xl shadow mb-6">

    <input type="hidden" name="type" value="{{ $type }}">

    <div class="flex flex-wrap items-end gap-4">

        <div>
            <label class="text-white text-sm">Dari Tanggal</label>
            <input type="date"
                   name="start_date"
                   value="{{ request('start_date') }}"
                   class="rounded-lg px-3 py-2 text-sm">
        </div>

        <div>
            <label class="text-white text-sm">Sampai Tanggal</label>
            <input type="date"
                   name="end_date"
                   value="{{ request('end_date') }}"
                   class="rounded-lg px-3 py-2 text-sm">
        </div>

        <div>
            <button class="bg-white text-black px-4 py-2 rounded-lg shadow text-sm">
                Filter
            </button>
        </div>

        <div>
            <a href="{{ route('laporan.consumable.transaksi', ['type' => $type]) }}"
               class="bg-gray-200 px-4 py-2 rounded-lg text-sm">
               Reset
            </a>
        </div>

    </div>
</form>


{{-- TOGGLE --}}
<div class="flex items-center gap-3 mb-4">

    <div class="flex bg-gray-200 p-1 rounded-xl shadow-inner">

        <a href="{{ route('laporan.consumable.transaksi', ['type' => 'pengeluaran']) }}"
           class="px-4 py-2 rounded-xl text-sm transition
           {{ $type == 'pengeluaran'
                ? 'bg-white shadow text-black font-semibold'
                : 'text-gray-600 hover:text-black' }}">
            📤 Pengeluaran
        </a>

        <a href="{{ route('laporan.consumable.transaksi', ['type' => 'pengembalian']) }}"
           class="px-4 py-2 rounded-xl text-sm transition
           {{ $type == 'pengembalian'
                ? 'bg-white shadow text-black font-semibold'
                : 'text-gray-600 hover:text-black' }}">
            📥 Pengembalian
        </a>

    </div>

    <div class="px-4 py-2 bg-gray-100 rounded-lg text-sm shadow">
        Total {{ ucfirst($type) }} : {{ $data->count() }}
    </div>

</div>


{{-- ================= TABEL ================= --}}
@if($type == 'pengeluaran')

<div class="bg-white rounded-xl shadow overflow-hidden">

    <table class="w-full text-sm">

        <thead class="bg-gradient-to-r from-cyan-600 to-teal-500 text-white">
            <tr>
                <th class="px-6 py-3">ID</th>
                <th class="px-6 py-3">TANGGAL</th>
                <th class="px-6 py-3">KARYAWAN</th>
                <th class="px-6 py-3">ITEM</th>
                <th class="px-6 py-3">QTY</th>
                <th class="px-6 py-3">KETERANGAN</th>
            </tr>
        </thead>

        <tbody class="bg-gray-50 divide-y divide-gray-200">

            @forelse($data as $row)
            <tr class="hover:bg-gray-100">

                <td class="px-6 py-4">
                    {{ $row->transaction_code }}
                </td>

                <td class="px-6 py-4">
                    {{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}
                </td>

                <td class="px-6 py-4">
                    {{ $row->employee_name }}
                </td>

                <td class="px-6 py-4">
                    @foreach($row->items as $item)
                        <div>{{ $item->consumable->name ?? '-' }}</div>
                    @endforeach
                </td>

                <td class="px-6 py-4">
                    @foreach($row->items as $item)
                        <div>{{ $item->qty }}</div>
                    @endforeach
                </td>

                <td class="px-6 py-4">
                    {{ $row->note ?? '-' }}
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-10 text-gray-500">
                    Tidak ada data pada periode ini
                </td>
            </tr>
            @endforelse

        </tbody>

    </table>

</div>

@else


<div class="bg-white rounded-xl shadow overflow-hidden">

    <table class="w-full text-sm">

        <thead class="bg-gradient-to-r from-cyan-600 to-teal-500 text-white">
            <tr>
                <th class="px-6 py-3">ID</th>
                <th class="px-6 py-3">TGL RETURN</th>
                <th class="px-6 py-3">KARYAWAN</th>
                <th class="px-6 py-3">ITEM</th>
                <th class="px-6 py-3">QTY RETURN</th>
                <th class="px-6 py-3">KETERANGAN</th>
            </tr>
        </thead>

        <tbody class="bg-gray-50 divide-y divide-gray-200">

            @forelse($data as $row)
            <tr class="hover:bg-gray-100">

                <td class="px-6 py-4">
                    {{ $row->transaction->transaction_code ?? '-' }}
                </td>

                <td class="px-6 py-4">
                    {{ \Carbon\Carbon::parse($row->return_date)->format('d M Y') }}
                </td>

                <td class="px-6 py-4">
                    {{ $row->transaction->employee_name ?? '-' }}
                </td>

                <td class="px-6 py-4">
                    {{ $row->consumable->name ?? '-' }}
                </td>

                <td class="px-6 py-4">
                    {{ $row->qty_return }}
                </td>

                <td class="px-6 py-4">
                    {{ $row->note ?? '-' }}
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-12 text-gray-500">
                    Tidak ada data pada periode ini
                </td>
            </tr>
            @endforelse

        </tbody>

    </table>

</div>

@endif

@endsection