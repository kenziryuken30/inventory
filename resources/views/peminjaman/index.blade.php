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
    

    {{-- ================= TABLE RIWAYAT ================= --}}
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 border">No</th>
                    <th class="p-3 border">Tanggal</th>
                    <th class="p-3 border">Peminjam</th>
                    <th class="p-3 border">Tools</th>
                    <th class="p-3 border">No Seri</th>
                    <th class="p-3 border w-64">Aksi</th>
                </tr>
            </thead>

            <tbody>
            @php $no = 1; @endphp

            @forelse ($transactions as $transaction)
                @foreach ($transaction->items as $item)

                <tr class="hover:bg-gray-50">
                    <td class="p-3 border text-center">{{ $no++ }}</td>

                    <td class="p-3 border">
                        {{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}
                    </td>

                    <td class="p-3 border">
                        {{ $transaction->borrower_name }}
                    </td>

                    <td class="p-3 border">
                        {{ $item->toolkit->toolkit_name ?? '-' }}
                    </td>

                    <td class="p-3 border">
                        {{ $item->serial->serial_number ?? '-' }}
                    </td>

                    <td class="p-3 border text-center space-x-2">

                        {{-- ================= BELUM CONFIRM ================= --}}
                        @if (! $transaction->is_confirm)

                            <a href="{{ route('peminjaman.edit', $transaction->id) }}"
                               class="bg-blue-500 text-white px-3 py-1 rounded text-xs">
                                Edit
                            </a>

                            <form action="{{ route('peminjaman.destroy', $transaction->id) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Hapus transaksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-500 text-white px-3 py-1 rounded text-xs">
                                    Hapus
                                </button>
                            </form>

                            <form action="{{ route('peminjaman.confirm', $transaction->id) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                <button class="bg-green-600 text-white px-3 py-1 rounded text-xs">
                                    Confirm
                                </button>
                            </form>

                        {{-- ================= SUDAH CONFIRM ================= --}}
                        @else

                            {{-- Kalau masih ada item yang belum kembali --}}
                            @if ($transaction->items->whereNull('return_date')->count() > 0)

                                <button
                                    @click="openReturn = '{{ $transaction->id }}'"
                                    class="bg-yellow-500 text-white px-3 py-1 rounded text-xs">
                                    Pengembalian
                                </button>

                            @endif

                            <span class="bg-green-200 text-green-800 px-2 py-1 rounded text-xs">
                                Confirmed
                            </span>

                        @endif
                    </td>
                </tr>

                @endforeach

            @empty
                <tr>
                    <td colspan="6" class="p-6 text-center text-gray-500">
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
                               value="{{ date('Y-m-d') }}"
                               class="w-full border rounded px-3 py-2 bg-gray-100"
                               readonly>
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
