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
    </style>
</head>

<body class="bg-gray-100">

    <div class="flex min-h-screen" x-data="{ sidebarOpen: true }">

        <!-- SIDEBAR -->
        <aside
            :class="sidebarOpen ? 'w-64' : 'w-16'"
            class="bg-white shadow-md p-4 transition-all duration-300 relative">

            <h1 class="text-lg font-bold mb-6" x-show="sidebarOpen">Inventory</h1>

            <ul class="space-y-2 text-sm">

                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded hover:bg-gray-100">
                        <span x-show="sidebarOpen">Dashboard</span>
                        <span x-show="!sidebarOpen">🏠</span>
                    </a>
                </li>

                <!-- Data Barang -->
                <li x-data="{ openBarang: false }">
                    <button @click="openBarang = !openBarang"
                        class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-100">
                        <span x-show="sidebarOpen">Data Barang</span>
                        <span x-show="!sidebarOpen">📦</span>
                        <span x-show="sidebarOpen" x-text="openBarang ? '▾' : '▸'"></span>
                    </button>

                    <ul x-show="openBarang && sidebarOpen" x-transition x-cloak class="ml-4 mt-1 space-y-1">
                        <li>
                            <a href="{{ route('tools.index') }}" class="block px-3 py-1 rounded hover:bg-gray-100">
                                Data Tools
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('consumable.index') }}" class="block px-3 py-1 rounded hover:bg-gray-100">
                                Data Consumable
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Transaksi -->
                <li x-data="{ openTransaksi: false }">
                    <button @click="openTransaksi = !openTransaksi"
                        class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-100">
                        <span x-show="sidebarOpen">Transaksi</span>
                        <span x-show="!sidebarOpen">🔄</span>
                        <span x-show="sidebarOpen" x-text="openTransaksi ? '▾' : '▸'"></span>
                    </button>

                    <ul x-show="openTransaksi && sidebarOpen" x-transition x-cloak class="ml-4 mt-1 space-y-1">
                        <li>
                            <a href="{{ route('peminjaman.index') }}" class="block px-3 py-1 rounded hover:bg-gray-100">
                                Peminjaman Tools
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('transaksi.index') }}" class="block px-3 py-1 rounded hover:bg-gray-100">
                                Permintaan Consumable
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Laporan -->
                <li x-data="{ openLaporan: false }">
                    <button @click="openLaporan = !openLaporan"
                        class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-100">
                        <span x-show="sidebarOpen">Laporan</span>
                        <span x-show="!sidebarOpen">📊</span>
                        <span x-show="sidebarOpen" x-text="openLaporan ? '▾' : '▸'"></span>
                    </button>

                    <ul x-show="openLaporan && sidebarOpen" x-transition x-cloak class="ml-4 mt-1 space-y-1">
                        <li>
                            <a href="{{ route('laporan.tools.transaksi') }}"
                                class="block px-3 py-1 rounded hover:bg-gray-100">
                                Transaksi Tools
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('laporan.consumable.transaksi') }}"
                                class="block px-3 py-1 rounded hover:bg-gray-100">
                                Permintaan Consumable
                            </a>
                        </li>
                    </ul>
                </li>

                <form method="POST" action="{{ route('logout') }}" class="mt-8">
                    @csrf
                    <button class="text-red-500 text-sm">
                        <span x-show="sidebarOpen">Logout</span>
                        <span x-show="!sidebarOpen">🚪</span>
                    </button>
                </form>

            </ul>
        </aside>

        <!-- TOMBOL BULAT -->
        <button
            @click="sidebarOpen = !sidebarOpen"
            :class="sidebarOpen ? 'left-60' : 'left-12'"
            class="fixed top-5 bg-blue-600 text-white w-10 h-10 rounded-full shadow-lg transition-all duration-300 z-50">
            ☰
        </button>

        <!-- CONTENT -->
        <main class="flex-1 p-6 transition-all duration-300">
            @yield('content')
        </main>

    </div>

</body>
</html>