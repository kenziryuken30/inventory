@extends('layouts.app')

@section('content')

<h2 class="text-3xl font-bold text-[#268397] mb-1">Dashboard</h2>
<p class="text-gray-500 mb-10">Selamat datang di Sistem Inventory Management</p>


<div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">

    <div class="p-6 flex justify-between items-center rounded-2xl shadow-xl text-white
        bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)]">

        <div>
            <p class="text-sm opacity-90">Total Barang</p>
            <p class="text-3xl font-bold">{{ $totalBarang }}</p>
            <p class="text-xs opacity-75">Semua item dalam inventory</p>
        </div>

        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-10 h-10"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor"
             stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 7l9-4 9 4-9 4-9-4z" />
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 7v10l9 4 9-4V7" />
        </svg>
    </div>

    <div class="p-6 flex justify-between items-center rounded-2xl shadow-xl text-white
        bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)]">

        <div>
            <p class="text-sm opacity-90">Alat Tersedia</p>
            <p class="text-3xl font-bold">{{ $alatTersedia }}</p>
            <p class="text-xs opacity-75">dari {{ $totalBarang }} total alat</p>
        </div>

        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-10 h-10"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor"
             stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M5 13l4 4L19 7" />
        </svg>
    </div>

    <div class="p-6 flex justify-between items-center rounded-2xl shadow-xl text-white
        bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)]">

        <div>
            <p class="text-sm opacity-90">Alat Di Pinjam</p>
            <p class="text-3xl font-bold">{{ $alatDipinjam }}</p>
            <p class="text-xs opacity-75">dari {{ $totalBarang }} total alat</p>
        </div>

        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-10 h-10"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor"
             stroke-width="1.8">
            <circle cx="12" cy="12" r="9" />
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 7v5l3 3" />
        </svg>
    </div>

    <div class="p-6 flex justify-between items-center rounded-2xl shadow-xl text-white
        bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)]">

        <div>
            <p class="text-sm opacity-90">Consumable Menipis</p>
            <p class="text-3xl font-bold">{{ $consumableMenipis }}</p>
            <p class="text-xs opacity-75">Di bawah minimum stok</p>
        </div>

        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-10 h-10"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor"
             stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 9v2m0 4h.01M5 19h14L12 4 5 19z" />
        </svg>
    </div>

</div>

{{-- ===================== --}}
{{-- BOX STYLE --}}
{{-- ===================== --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-10">

    <div class="rounded-2xl bg-white shadow-xl overflow-hidden">

        <div class="px-6 py-4 text-white
            bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)]">
            <h3 class="font-semibold">
                Peminjaman Terbaru
            </h3>
        </div>

        <div class="px-6 py-4">

            @forelse($peminjamanTerbaru as $trx)
                <div class="flex justify-between items-center py-4 border-b last:border-none">

                    <div>
                        <p class="font-medium text-gray-800">
                            {{ $trx->tool->name ?? '-' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $trx->employee->full_name ?? '-' }} • 
                            {{ \Carbon\Carbon::parse($trx->date)->format('d M Y') }}
                        </p>
                    </div>

                    @if($trx->status == 'dipinjam')
                        <span class="bg-yellow-400 text-xs px-4 py-1 rounded-full shadow">
                            Dipinjam
                        </span>
                    @else
                        <span class="bg-green-500 text-white text-xs px-4 py-1 rounded-full shadow">
                            Selesai
                        </span>
                    @endif

                </div>
            @empty
                <div class="py-6 text-gray-400 text-sm text-center">
                    Belum ada data
                </div>
            @endforelse

        </div>
    </div>


    <div class="rounded-2xl bg-white shadow-xl overflow-hidden">

        <div class="px-6 py-4 text-white
            bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)]">
            <h3 class="font-semibold">
                Pengeluaran Consumable Terbaru
            </h3>
        </div>

        <div class="px-6 py-4">

            @forelse($consumableTerbaru as $item)
                <div class="flex justify-between items-center py-4 border-b last:border-none">

                    <div>
                        <p class="font-medium text-gray-800">
                            {{ $item->consumable->name ?? '-' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $item->created_at->format('d M Y') }}
                        </p>
                    </div>

                    <span class="bg-gray-200 text-gray-700 text-xs px-4 py-1 rounded-full shadow-sm">
                        -{{ $item->qty }} pcs
                    </span>

                </div>
            @empty
                <div class="py-6 text-gray-400 text-sm text-center">
                    Belum ada data
                </div>
            @endforelse

        </div>
    </div>

</div>

@endsection