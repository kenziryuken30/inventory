@extends('layouts.app')

@section('content')
<h2 class="text-xl font-semibold mb-4">Data Tools</h2>

<div class="bg-white rounded shadow p-4">

    {{-- SEARCH & FILTER --}}
    <div class="flex justify-between mb-4">
        <input type="text" placeholder="Cari barang..."
            class="border rounded px-3 py-2 w-1/3">

        <select class="border rounded px-3 py-2">
            <option value="">Semua Kondisi</option>
            <option value="baik">Baik</option>
            <option value="rusak">Rusak</option>
            <option value="maintenance">Maintenance</option>
        </select>
    </div>

    {{-- TABLE --}}
    <table class="w-full border-collapse">
        <thead>
            <tr class="border-b text-left text-sm text-gray-600">
                <th class="py-2">Foto</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>No Seri</th>
                <th>Status</th>
                <th>Kondisi</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($tools as $tool)
            <tr class="border-b text-sm">
                {{-- FOTO --}}
                <td class="py-2">
                    <img src="{{ asset('storage/' . $tool->image) }}"
                        class="w-12 h-12 rounded object-cover">
                </td>

                {{-- NAMA --}}
                <td>{{ $tool->toolkit->toolkit_name }}</td>

                {{-- KATEGORI --}}
                <td>
                    <span class="px-2 py-1 border rounded text-xs">
                        {{ $tool->toolkit->category->name ?? '-' }}
                    </span>
                </td>

                {{-- NO SERI --}}
                <td>{{ $tool->serial_number }}</td>

                {{-- STATUS --}}
                <td>
                    @if ($tool->status === 'tersedia')
                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                            Tersedia
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-gray-200 text-gray-700">
                            Dipinjam
                        </span>
                    @endif
                </td>

                {{-- KONDISI --}}
                <td>
                    @if ($tool->condition === 'baik')
                        <span class="px-2 py-1 text-xs rounded border">BAIK</span>
                    @elseif ($tool->condition === 'rusak')
                        <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                            RUSAK
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">
                            MAINTENANCE
                        </span>
                    @endif
                </td>

                {{-- AKSI --}}
                <td class="flex gap-2">
                    @if ($tool->condition === 'maintenance')
                        <form action="{{ route('tools.finishMaintenance', $tool->id) }}"
                              method="POST">
                            @csrf
                            <button class="text-xs px-2 py-1 border rounded">
                                Selesai maintenance
                            </button>
                        </form>
                    @endif

                    <a href="#" class="text-blue-600 text-sm">✏️</a>
                    <a href="#" class="text-red-600 text-sm">🗑️</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
