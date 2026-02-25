@extends('layouts.app')

@section('content')

<h2 class="text-xl font-bold mb-4">Laporan Transaksi Tools</h2>

<form method="GET" action="{{ route('laporan.transaksi-tools') }}"
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
            <a href="{{ route('laporan.transaksi-tools', ['type' => $type]) }}"
               class="bg-gray-200 px-4 py-2 rounded-lg text-sm">
               Reset
            </a>
        </div>

    </div>
</form>

{{-- Toggle --}}
<div class="flex items-center gap-3 mb-4">

    <div class="flex bg-gray-200 p-1 rounded-xl shadow-inner">

        <a href="{{ route('laporan.transaksi-tools', ['type' => 'peminjaman']) }}"
           class="px-4 py-2 rounded-xl text-sm transition
           {{ $type == 'peminjaman'
                ? 'bg-white shadow text-black font-semibold'
                : 'text-gray-600 hover:text-black' }}">
            ↩ Peminjaman Tools
        </a>

        <a href="{{ route('laporan.transaksi-tools', ['type' => 'pengembalian']) }}"
           class="px-4 py-2 rounded-xl text-sm transition
           {{ $type == 'pengembalian'
                ? 'bg-white shadow text-black font-semibold'
                : 'text-gray-600 hover:text-black' }}">
            ⟳ Pengembalian Tools
        </a>

    </div>

    <div class="px-4 py-2 bg-gray-100 rounded-lg text-sm shadow">
        Total {{ ucfirst($type) }} : {{ $data->count() }}
    </div>

</div>


{{-- TABEL DINAMIS --}}
@if($type == 'peminjaman')

<div x-data="{ openDetail: null }">

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">

            <thead class="bg-gradient-to-r from-cyan-600 to-teal-500 text-white">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">TGL PINJAM</th>
                    <th class="px-6 py-3">KARYAWAN</th>
                    <th class="px-6 py-3">ALAT PINJAM</th>
                    <th class="px-6 py-3 text-center">AKSI</th>
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
                        {{ $row->borrower_name }}
                    </td>

                    <td class="px-6 py-4">
                        @foreach($row->items as $item)
                            <div>{{ $item->toolkit->toolkit_name ?? '-' }}</div>
                        @endforeach
                    </td>

                    <td class="px-6 py-4 text-center">
                        <button
                            @click="openDetail = {{ $row->id }}"
                            class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded-lg">
                            👁 Detail
                        </button>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-10 text-gray-500">
                        Tidak ada data pada periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    {{-- MODAL DI LUAR TABLE --}}
    @foreach($data as $row)
    <div
        x-show="openDetail === {{ $row->id }}"
        x-transition
        x-cloak
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

        <div class="bg-white w-full max-w-md rounded-xl shadow-xl p-6">

            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Detail Peminjaman</h3>
                <button @click="openDetail = null">✕</button>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                <div>
                    <p class="font-semibold">Karyawan</p>
                    <p>{{ $row->borrower_name }}</p>
                </div>

                <div>
                    <p class="font-semibold">Tanggal Pinjam</p>
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
                    <thead class="bg-gradient-to-r from-cyan-600 to-teal-500 text-white">
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


@else

    <div class="bg-white rounded-xl shadow overflow-hidden">

    <table class="w-full text-sm">

        <thead class="bg-gradient-to-r from-cyan-600 to-teal-500 text-white">
            <tr class="text-left">
                <th class="px-6 py-3">ID</th>
                <th class="px-6 py-3">TGL KEMBALI</th>
                <th class="px-6 py-3">KARYAWAN</th>
                <th class="px-6 py-3">ALAT DIPINJAM</th>
                <th class="px-6 py-3">NO SERI</th>
                <th class="px-6 py-3">KONDISI</th>
                <th class="px-6 py-3">KETERANGAN</th>
            </tr>
        </thead>

        <tbody class="bg-gray-50 divide-y divide-gray-200">

            @forelse($data as $row)
            <tr class="hover:bg-gray-100 transition">

                <td class="px-6 py-4 font-medium text-gray-600">
                    {{ $row->transaction->transaction_code ?? '-' }}
                </td>

                <td class="px-6 py-4">
                    {{ \Carbon\Carbon::parse($row->return_date)->format('d M Y') }}
                </td>

                <td class="px-6 py-4">
                    {{ $row->transaction->borrower_name ?? '-' }}
                </td>

                <td class="px-6 py-4">
                    {{ $row->toolkit->toolkit_name ?? '-' }}
                </td>

                <td class="px-6 py-4">
                    {{ $row->serial->serial_number ?? '-' }}
                </td>

                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-lg text-xs font-semibold
                        {{ strtolower($row->condition) == 'baik'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $row->condition }}
                    </span>
                </td>

                <td class="px-6 py-4">
                    {{ $row->note ?? '-' }}
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-12 text-gray-500">
                    <div class="flex flex-col items-center gap-2">
                        <div class="text-3xl">↔</div>
                        <div>Tidak ada data pada periode ini</div>
                    </div>
                </td>
            </tr>
            @endforelse

        </tbody>

    </table>

</div>

@endif

@endsection