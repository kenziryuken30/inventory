@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto"
     x-data="{ openReturn: null }">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Peminjaman Tools</h2>

        <a href="{{ route('peminjaman.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700">
            + Pinjam Tools
        </a>
    </div>

    {{-- ================= FLASH MESSAGE ================= --}}
    @if (session('success'))
        <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- ================= FILTER ================= --}}
<form method="GET"
      action="{{ route('peminjaman.index') }}"
      class="mb-6 bg-gradient-to-r from-cyan-500 to-teal-500 p-4 rounded-xl shadow flex flex-wrap gap-4 items-center">

    {{-- Search Nama --}}
    <input type="text"
           name="search"
           value="{{ request('search') }}"
           placeholder="Masukan nama peminjam"
           class="px-4 py-2 rounded-lg shadow w-64">

    {{-- Tanggal Dari --}}
    <label class="text-white font-semibold">Tanggal</label>
    <input type="date"
           name="start_date"
           value="{{ request('start_date') }}"
           class="px-3 py-2 rounded-lg shadow">

    <span class="text-white font-semibold">S/D</span>

    {{-- Tanggal Sampai --}}
    <input type="date"
           name="end_date"
           value="{{ request('end_date') }}"
           class="px-3 py-2 rounded-lg shadow">

    <button type="submit"
            class="bg-white text-teal-600 px-4 py-2 rounded-lg shadow font-semibold">
        Filter
    </button>

    <a href="{{ route('peminjaman.index') }}"
       class="text-white underline">
        Reset
    </a>
</form>
    

{{-- ================= TABLE RIWAYAT ================= --}}
<div class="bg-white shadow-lg rounded-xl overflow-hidden">
    <table class="min-w-full text-sm text-gray-700">
        
        <thead class="bg-gray-100 text-xs uppercase tracking-wider text-gray-600">
            <tr>
                <th class="px-4 py-3 text-center w-12">No</th>
                <th class="px-4 py-3 text-left">Kode Transaksi</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-left">Peminjam</th>
                <th class="px-4 py-3 text-left">Tools</th>
                <th class="px-4 py-3 text-left">No Seri</th>
                <th class="px-4 py-3 text-center w-64">Aksi</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">

        @forelse ($transactions as $transaction)

            <tr class="hover:bg-gray-50 transition">

                <td class="px-4 py-3 text-center font-medium">
                    {{ $loop->iteration }}
                </td>

                <td class="px-4 py-3 font-semibold text-blue-600">
                    {{ $transaction->kode_transaksi ?? $transaction->transaction_code }}
                </td>

                <td class="px-4 py-3">
                    {{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}
                </td>

                <td class="px-4 py-3">
                    {{ $transaction->borrower_name }}
                </td>

                <td class="px-4 py-3">
                    @foreach ($transaction->items as $item)
                        <div>{{ $item->toolkit->toolkit_name ?? '-' }}</div>
                    @endforeach
                </td>

                <td class="px-4 py-3">
                    @foreach ($transaction->items as $item)
                        <div>{{ $item->serial->serial_number ?? '-' }}</div>
                    @endforeach
                </td>

                <td class="px-4 py-3 text-center space-x-2">

                    @if (! $transaction->is_confirm)

                        <a href="{{ route('peminjaman.edit', $transaction->id) }}"
                           class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-xs transition">
                            Edit
                        </a>

                        <form action="{{ route('peminjaman.destroy', $transaction->id) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Hapus transaksi ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-xs transition">
                                Hapus
                            </button>
                        </form>

                        <form action="{{ route('peminjaman.confirm', $transaction->id) }}"
                              method="POST"
                              class="inline">
                            @csrf
                            <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md text-xs transition">
                                Confirm
                            </button>
                        </form>

                    @else

                        @if ($transaction->items->whereNull('return_date')->count() > 0)
                            <button
                                @click="openReturn = '{{ $transaction->id }}'"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md text-xs transition">
                                Pengembalian
                            </button>
                        @endif

                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-md text-xs font-medium">
                            Confirmed
                        </span>

                    @endif
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="7" class="p-6 text-center text-gray-400">
                    Tidak ada data peminjaman
                </td>
            </tr>
        @endforelse

        </tbody>
    </table>
</div>

    {{-- ================= POPUP PENGEMBALIAN ================= --}}
    @foreach ($transactions as $transaction)
    @if ($transaction->is_confirm)

    <div x-show="openReturn === '{{ $transaction->id }}'"
         x-cloak
         x-data="{ selected: [] }"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

        <div class="bg-white w-4/5 rounded-xl shadow-xl p-6">

            <form action="{{ route('peminjaman.return.process', $transaction->id) }}"
                  method="POST">
                @csrf

                {{-- HEADER --}}
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold">Proses Pengembalian</h3>
                        <p class="text-sm text-gray-500">
                            Pilih alat yang akan dikembalikan
                        </p>
                    </div>

                    <button type="button"
                            @click="openReturn = null"
                            class="text-xl text-gray-500">&times;</button>
                </div>


                {{-- INFO --}}
                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label class="text-sm text-gray-600">Nama Karyawan</label>
                        <input type="text"
                               value="{{ $transaction->borrower_name }}"
                               class="w-full border rounded px-3 py-2 bg-gray-100"
                               readonly>
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Tanggal Pengembalian</label>
                        <input type="date"
                               name="return_date"
                               value="{{ date('Y-m-d') }}"
                               class="w-full border rounded px-3 py-2 "
                               required>
                    </div>
                </div>


                {{-- TABLE ITEM --}}
                <div class="border rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3 border">Pilih</th>
                                <th class="p-3 border">Nama Alat</th>
                                <th class="p-3 border">No Seri</th>
                                <th class="p-3 border">Kondisi</th>
                                <th class="p-3 border">Keterangan</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach ($transaction->items->whereNull('return_date') as $item)
                        <tr>
                            <td class="p-3 border text-center">
                                <input type="checkbox"
                                       x-model="selected"
                                       value="{{ $item->id }}"
                                       name="items[{{ $item->id }}][id]">
                            </td>

                            <td class="p-3 border">
                                {{ $item->toolkit->toolkit_name }}
                            </td>

                            <td class="p-3 border">
                                {{ $item->serial->serial_number }}
                            </td>

                            <td class="p-3 border">
                                <select name="items[{{ $item->id }}][condition]"
                                        class="border rounded px-2 py-1 w-full"
                                        :disabled="!selected.includes('{{ $item->id }}')">
                                    <option value="BAIK">Baik</option>
                                    <option value="MAINTENANCE">Butuh Perbaikan</option>
                                    <option value="RUSAK">Rusak</option>
                                </select>
                            </td>

                            <td class="p-3 border">
                                <input type="text"
                                       name="items[{{ $item->id }}][note]"
                                       class="border rounded px-2 py-1 w-full"
                                       placeholder="Keterangan..."
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
                            :class="selected.length === 0
                                ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                : 'bg-blue-600 text-white'"
                            class="px-6 py-2 rounded-lg transition">
                        Kembalikan <span x-text="selected.length"></span> Alat
                    </button>

                </div>

            </form>
        </div>
    </div>

    @endif
    @endforeach

</div>
@endsection