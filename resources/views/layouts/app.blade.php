<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Inventory</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            background: #e9edf2;
            overflow-x: hidden;
        }

        .sidebar-bg {
            background:url("{{ asset('images/aad0b61f0a65cdbea89666bd0bbd7107.jpg') }}");
            background-size: cover;
            background-position: center;
        }

        .sidebar-overlay {
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.7), rgba(200, 230, 255, 0.7));
        }

        .card-stat {
            background: linear-gradient(135deg, #2ea3b7, #3ec6d3);
            border-radius: 16px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            color: white;
        }

        .box-section {
            border-radius: 18px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .menu-item {
            transition: all .2s ease;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.6);
            transform: translateX(4px);
        }

        .divider {
            border-top: 1px solid rgba(0, 0, 0, 0.15);
        }
        
    </style>
</head>

<body>

    <div class="flex min-h-screen relative overflow-hidden" x-data="{ sidebarOpen: true }">

        <!-- TOOLS KIRI -->
        <img src="{{ asset('images/kiri.png') }}"
            :class="sidebarOpen ? 'left-64' : 'left-16'"
            class="absolute bottom-0 w-[340px] pointer-events-none select-none transition-all duration-300 z-0">

        <!-- TOOLS KANAN -->
        <img src="{{ asset('images/kana1.png') }}"
            class="absolute right-0 bottom-0 w-[340px] opacity-90 pointer-events-none select-none">

        <!-- SIDEBAR -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-16'"
            class="relative flex flex-col rounded-r-3xl shadow-2xl transition-all duration-300 overflow-hidden">

            <div class="absolute inset-0 sidebar-bg"></div>
            <div class="absolute inset-0 sidebar-overlay"></div>

            <div class="relative z-10 flex flex-col h-full p-4">

                <!-- LOGO -->
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ asset('images/tecno.png') }}" class="w-10">
                    <div x-show="sidebarOpen">
                        <h1 class="font-bold text-gray-800">Inventory</h1>
                        <p class="text-xs text-gray-600">Management System</p>
                    </div>
                </div>

                <div class="divider -mx-4 mb-6"></div>

                <ul class="space-y-2 text-sm flex-1">

                    <!-- DASHBOARD -->
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3" />
                            </svg>

                            <span x-show="sidebarOpen">Dashboard</span>
                        </a>
                    </li>

                    <!-- DATA BARANG -->
                    <li x-data="{ openBarang: false }">
                        <button @click="openBarang = !openBarang"
                            class="menu-item w-full flex justify-between items-center px-3 py-2 rounded-lg">

                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4" />
                                </svg>
                                <span x-show="sidebarOpen">Data Barang</span>
                            </div>

                            <span x-show="sidebarOpen" x-text="openBarang ? '▾' : '▸'"></span>
                        </button>

                        <ul x-show="openBarang && sidebarOpen" x-transition x-cloak class="ml-8 mt-1 space-y-1">
                            <li>
                                <a href="{{ route('tools.index') }}"
                                    class="block px-3 py-1 rounded hover:bg-white/60">
                                    Data Tools
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('consumable.index') }}"
                                    class="block px-3 py-1 rounded hover:bg-white/60">
                                    Data Consumable
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- TRANSAKSI -->
                    <li x-data="{ openTransaksi: false }">
                        <button @click="openTransaksi = !openTransaksi"
                            class="menu-item w-full flex justify-between items-center px-3 py-2 rounded-lg">

                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582M20 20v-5h-.581M5.5 9A7.5 7.5 0 0119 12m0 0a7.5 7.5 0 01-13.5 3" />
                                </svg>
                                <span x-show="sidebarOpen">Transaksi</span>
                            </div>

                            <span x-show="sidebarOpen" x-text="openTransaksi ? '▾' : '▸'"></span>
                        </button>

                        <ul x-show="openTransaksi && sidebarOpen" x-transition x-cloak class="ml-8 mt-1 space-y-1">
                            <li>
                                <a href="{{ route('peminjaman.index') }}"
                                    class="block px-3 py-1 rounded hover:bg-white/60">
                                    Peminjaman Tools
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('transaksi.index') }}"
                                    class="block px-3 py-1 rounded hover:bg-white/60">
                                    Permintaan Consumable
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- LAPORAN -->
                    <li x-data="{ openLaporan: false }">
                        <button @click="openLaporan = !openLaporan"
                            class="menu-item w-full flex justify-between items-center px-3 py-2 rounded-lg">

                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-6m4 6v-10m4 10v-4M5 21h14" />
                                </svg>
                                <span x-show="sidebarOpen">Laporan</span>
                            </div>

                            <span x-show="sidebarOpen" x-text="openLaporan ? '▾' : '▸'"></span>
                        </button>

                        <ul x-show="openLaporan && sidebarOpen" x-transition x-cloak class="ml-8 mt-1 space-y-1">
                            <li>
                                <a href="{{ route('laporan.tools.transaksi') }}"
                                    class="block px-3 py-1 rounded hover:bg-white/60">
                                    Transaksi Tools
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('laporan.consumable.transaksi') }}"
                                    class="block px-3 py-1 rounded hover:bg-white/60">
                                    Laporan Consumable
                                </a>
                            </li>
                        </ul>
                    </li>

                </ul>

                <div class="divider -mx-4 my-4"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg w-full text-red-600">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1" />
                        </svg>

                        <span x-show="sidebarOpen">Logout</span>
                    </button>
                </form>

            </div>
        </aside>

        <!-- TOGGLE -->
        <button
            @click="sidebarOpen = !sidebarOpen"
            :class="sidebarOpen ? 'left-60' : 'left-12'"
            class="fixed top-5 bg-cyan-500 text-white w-10 h-10 rounded-full shadow-lg transition-all duration-300 z-50">
            ☰
        </button>

        <!-- CONTENT -->
        <main class="flex-1 p-8 relative z-10">
            @yield('content')
        </main>

    </div>
</body>

</html>