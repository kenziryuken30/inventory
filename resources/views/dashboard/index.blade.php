@extends('layouts.app')

@section('content')
<h2 class="text-xl font-semibold mb-6">Dashboard</h2>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Total Barang</p>
        <p class="text-2xl font-bold">{{ $totalBarang }}</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Alat Tersedia</p>
        <p class="text-2xl font-bold">{{ $alatTersedia }}</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Alat Dipinjam</p>
        <p class="text-2xl font-bold">{{ $alatDipinjam }}</p>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <p class="text-sm text-gray-500">Consumable Menipis</p>
        <p class="text-2xl font-bold">{{ $consumableMenipis }}</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    
    <div class="bg-white rounded shadow p-4">
        <h3 class="font-semibold mb-3">Peminjaman Terbaru</h3>
        @foreach($peminjamanTerbaru as $trx)
            <div class="border-b py-2">
                <p class="text-sm font-medium">
                    {{ $trx->employee->full_name ?? '-' }}
                </p>
                <p class="text-xs text-gray-500">
                    {{ $trx->date }}
                </p>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded shadow p-4">
        <h3 class="font-semibold mb-3">Pengeluaran Consumable</h3>
        @foreach($consumableTerbaru as $item)
            <div class="border-b py-2 flex justify-between">
                <span class="text-sm">{{ $item->consumable->name ?? '-' }}</span>
                <span class="text-sm text-red-500">-{{ $item->qty }}</span>
            </div>
        @endforeach
    </div>
</div>
@endsection
