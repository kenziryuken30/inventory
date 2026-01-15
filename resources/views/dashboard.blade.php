@extends('layouts.app')

@section('content')

<h1 class="text-2xl font-bold mb-1">Dashboard</h1>
<p class="text-gray-500 mb-6">
    Selamat datang di Sistem Inventory Management
</p>

<!-- CARD STAT -->
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">

    <div class="bg-white rounded-xl p-4 shadow">
        <p class="text-sm text-gray-500">Total Barang</p>
        <h2 class="text-2xl font-bold">14</h2>
    </div>

    <div class="bg-white rounded-xl p-4 shadow">
        <p class="text-sm text-gray-500">Alat Tersedia</p>
        <h2 class="text-2xl font-bold">0</h2>
    </div>

    <div class="bg-white rounded-xl p-4 shadow">
        <p class="text-sm text-gray-500">Alat Dipinjam</p>
        <h2 class="text-2xl font-bold">5</h2>
    </div>

    <div class="bg-white rounded-xl p-4 shadow">
        <p class="text-sm text-gray-500">Tidak Tersedia</p>
        <h2 class="text-2xl font-bold">2</h2>
    </div>

    <div class="bg-white rounded-xl p-4 shadow">
        <p class="text-sm text-gray-500">Stok Sparepart</p>
        <h2 class="text-2xl font-bold">844</h2>
    </div>

    <div class="bg-white rounded-xl p-4 shadow">
        <p class="text-sm text-gray-500">Sparepart Menipis</p>
        <h2 class="text-2xl font-bold text-red-500">3</h2>
    </div>

</div>

<!-- INFO -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">

    <div class="bg-white rounded-xl shadow p-5">
        <h3 class="font-semibold mb-3">Peminjaman Terbaru</h3>
        <p class="text-sm text-gray-600">
            Bor Listrik Bosch, Las Listrik Lakoni, Kompresor Angin
        </p>
        <span class="text-xs text-gray-400">15 Jan 2026</span>
    </div>

    <div class="bg-white rounded-xl shadow p-5">
        <h3 class="font-semibold mb-3">Pengeluaran Sparepart Terbaru</h3>
        <p class="text-sm text-gray-600">
            Baut M10 x 50mm
        </p>
        <span class="text-xs text-gray-400">-99 pcs • 15 Jan 2026</span>
    </div>

</div>

@endsection
