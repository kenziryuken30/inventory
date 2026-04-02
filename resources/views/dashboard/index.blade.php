@extends('layouts.app')

@section('content')

<style>
    .sidebar,
    aside,
    .layout-sidebar,
    .app-sidebar,
    [class*="sidebar"],
    [class*="side-bar"],
    [class*="Sidebar"],
    [class*="SideBar"] {
        position: fixed !important;
        z-index: 9999 !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        filter: none !important;
        opacity: 1 !important;
    }

    .layout-content,
    .main-content,
    main,
    [class*="main-content"],
    [class*="content-wrapper"],
    [class*="content-area"],
    [class*="MainContent"],
    [class*="ContentWrapper"] {
        margin-left: 0 !important;
        padding-left: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        transition: none !important;
    }

    .sidebar-open .layout-content,
    .sidebar-open .main-content,
    .sidebar-open main,
    .sidebar-open [class*="main-content"],
    .sidebar-open [class*="content-wrapper"] {
        margin-left: 0 !important;
        padding-left: 0 !important;
    }

    body,
    .app-layout,
    .layout-root,
    [class*="app-layout"],
    [class*="layout-root"],
    [class*="LayoutRoot"],
    [class*="AppLayout"] {
        background-size: cover !important;
        background-position: center !important;
        background-attachment: fixed !important;
    }

    @media (max-width: 768px) {
        body,
        .app-layout,
        .layout-root,
        [class*="app-layout"],
        [class*="layout-root"],
        [class*="LayoutRoot"],
        [class*="AppLayout"] {
            background-attachment: scroll !important;
        }
    }

    .sidebar-overlay,
    .sidebar-backdrop,
    [class*="sidebar-overlay"],
    [class*="sidebar-backdrop"],
    [class*="SidebarOverlay"],
    [class*="SidebarBackdrop"] {
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        background: rgba(0,0,0,0.3) !important;
    }

    @media (max-width: 640px) {
        .stat-card:hover {
            transform: none !important;
        }
    }
</style>

<div class="py-3 px-3 sm:px-4 md:px-6 lg:px-8 w-full min-w-0 max-w-full overflow-hidden">

    {{-- Header --}}
    <div class="mb-4 sm:mb-6 md:mb-8">
        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-[#268397] mb-0.5 leading-tight break-words">Dashboard</h2>
        <p class="text-xs sm:text-sm text-gray-500 break-words">Selamat datang di Sistem Inventory Management</p>
    </div>

    {{-- ===================== --}}
    {{-- STATS CARDS --}}
    {{-- ===================== --}}
    {{-- HP kecil: 1 kolom | HP besar: 2 kolom | Desktop: 4 kolom --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-5 lg:gap-6 mb-5 sm:mb-8 md:mb-10">

        {{-- Total Barang --}}
        <div class="stat-card p-3 sm:p-4 md:p-5 flex justify-between items-center rounded-xl sm:rounded-2xl shadow-lg text-white
            bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)] transform hover:scale-105 transition-transform duration-300">
            <div class="flex-1 min-w-0">
                <p class="text-[11px] sm:text-sm opacity-90 leading-tight break-words">Total Tools</p>
                <p class="text-xl sm:text-2xl md:text-3xl font-bold mt-0.5 sm:mt-1 leading-tight break-words">{{ $totalBarang }}</p>
                <p class="text-[10px] sm:text-xs opacity-75 mt-0.5 sm:mt-1 truncate hidden sm:block">Semua item dalam inventory</p>
            </div>
            <div class="ml-2 sm:ml-3 md:ml-4 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 lg:w-12 lg:h-12 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9-4 9 4-9 4-9-4z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10l9 4 9-4V7" />
                </svg>
            </div>
        </div>

        {{-- Alat Tersedia --}}
        <div class="stat-card p-3 sm:p-4 md:p-5 flex justify-between items-center rounded-xl sm:rounded-2xl shadow-lg text-white
            bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)] transform hover:scale-105 transition-transform duration-300">
            <div class="flex-1 min-w-0">
                <p class="text-[11px] sm:text-sm opacity-90 leading-tight break-words">Tools Tersedia</p>
                <p class="text-xl sm:text-2xl md:text-3xl font-bold mt-0.5 sm:mt-1 leading-tight break-words">{{ $alatTersedia }}</p>
                <p class="text-[10px] sm:text-xs opacity-75 mt-0.5 sm:mt-1 truncate hidden sm:block">dari {{ $totalBarang }} total tools</p>
            </div>
            <div class="ml-2 sm:ml-3 md:ml-4 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 lg:w-12 lg:h-12 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        {{-- Alat Dipinjam --}}
        <div class="stat-card p-3 sm:p-4 md:p-5 flex justify-between items-center rounded-xl sm:rounded-2xl shadow-lg text-white
            bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)] transform hover:scale-105 transition-transform duration-300">
            <div class="flex-1 min-w-0">
                <p class="text-[11px] sm:text-sm opacity-90 leading-tight break-words">Tools Di Pinjam</p>
                <p class="text-xl sm:text-2xl md:text-3xl font-bold mt-0.5 sm:mt-1 leading-tight break-words">{{ $alatDipinjam }}</p>
                <p class="text-[10px] sm:text-xs opacity-75 mt-0.5 sm:mt-1 truncate hidden sm:block">dari {{ $totalBarang }} total tools</p>
            </div>
            <div class="ml-2 sm:ml-3 md:ml-4 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 lg:w-12 lg:h-12 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="9" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 3" />
                </svg>
            </div>
        </div>

        {{-- Consumable Menipis --}}
        <div class="stat-card p-3 sm:p-4 md:p-5 flex justify-between items-center rounded-xl sm:rounded-2xl shadow-lg text-white
            bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)] transform hover:scale-105 transition-transform duration-300">
            <div class="flex-1 min-w-0">
                <p class="text-[11px] sm:text-sm opacity-90 leading-tight break-words">Consumable Menipis</p>
                <p class="text-xl sm:text-2xl md:text-3xl font-bold mt-0.5 sm:mt-1 leading-tight break-words">{{ $consumableMenipis }}</p>
                <p class="text-[10px] sm:text-xs opacity-75 mt-0.5 sm:mt-1 truncate hidden sm:block">Di bawah minimum stok</p>
            </div>
            <div class="ml-2 sm:ml-3 md:ml-4 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 lg:w-12 lg:h-12 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5 19h14L12 4 5 19z" />
                </svg>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5 md:gap-6 lg:gap-8">

        {{-- Peminjaman Terbaru --}}
        <div class="rounded-xl sm:rounded-2xl bg-white shadow-xl overflow-hidden flex flex-col">
            <div class="px-4 sm:px-5 md:px-6 py-3 sm:py-3.5 md:py-4 text-white bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)] flex-shrink-0">
                <h3 class="text-sm sm:text-base md:text-lg font-semibold leading-tight break-words">Peminjaman Terbaru</h3>
            </div>

            <div class="flex-grow overflow-x-auto max-h-[280px] sm:max-h-[360px] md:max-h-[440px] lg:max-h-none overflow-y-auto">
                @forelse($peminjamanTerbaru as $trx)
                <div class="flex justify-between items-center gap-2 sm:gap-3 md:gap-4 px-3 sm:px-4 md:px-6 py-2.5 sm:py-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors min-w-0">

                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-800 truncate break-words">
                            {{ $trx->items->pluck('toolkit.toolkit_name')->filter()->join(', ') ?: '-' }}
                        </p>
                        <p class="text-[10px] sm:text-xs text-gray-400 truncate mt-0.5 break-words">
                            {{ $trx->borrower_name ?? '-' }} &bull; {{ \Carbon\Carbon::parse($trx->date)->format('d M Y') }}
                        </p>
                    </div>

                    <div class="flex-shrink-0">
                        @if($trx->is_confirm)
                        <span class="bg-yellow-100 text-yellow-700 text-[10px] sm:text-xs font-semibold px-2 sm:px-3 py-0.5 sm:py-1 rounded-full border border-yellow-200 whitespace-nowrap">
                            Dipinjam
                        </span>
                        @else
                        <span class="bg-green-100 text-green-700 text-[10px] sm:text-xs font-semibold px-2 sm:px-3 py-0.5 sm:py-1 rounded-full border border-green-200 whitespace-nowrap">
                            Selesai
                        </span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="py-8 sm:py-10 text-gray-400 text-xs sm:text-sm text-center">
                    Belum ada data peminjaman
                </div>
                @endforelse
            </div>
        </div>


        {{-- Pengeluaran Consumable Terbaru --}}
        <div class="rounded-xl sm:rounded-2xl bg-white shadow-xl overflow-hidden flex flex-col">
            <div class="px-4 sm:px-5 md:px-6 py-3 sm:py-3.5 md:py-4 text-white bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)] flex-shrink-0">
                <h3 class="text-sm sm:text-base md:text-lg font-semibold leading-tight break-words">Pengeluaran Consumable Terbaru</h3>
            </div>

            <div class="flex-grow overflow-x-auto max-h-[280px] sm:max-h-[360px] md:max-h-[440px] lg:max-h-none overflow-y-auto">
                @forelse($consumableTerbaru as $item)
                <div class="flex justify-between items-center gap-2 sm:gap-3 md:gap-4 px-3 sm:px-4 md:px-6 py-2.5 sm:py-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors min-w-0">

                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-800 truncate break-words">
                            {{ $item->consumable->name ?? '-' }}
                        </p>
                        <p class="text-[10px] sm:text-xs text-gray-400 mt-0.5 break-words">
                            {{ $trx->borrower_name ?? '-' }} &bull;  {{ $item->created_at->format('d M Y') }}
                        </p>
                    </div>

                    <div class="flex-shrink-0">
                        <span class="bg-red-50 text-red-600 text-[10px] sm:text-xs font-bold px-2 sm:px-3 py-0.5 sm:py-1 rounded-full border border-red-100 whitespace-nowrap">
                            {{ $item->qty }} pcs
                        </span>
                    </div>
                </div>
                @empty
                <div class="py-8 sm:py-10 text-gray-400 text-xs sm:text-sm text-center">
                    Belum ada data pengeluaran
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection