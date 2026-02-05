<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Inventory</title>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
<div class="flex min-h-screen">

   <aside class="w-64 bg-white shadow-md p-4"
       x-data="{ openBarang: false, openTransaksi: false }">

    <h1 class="text-lg font-bold mb-6">Inventory</h1>

    <ul class="space-y-2 text-sm">

        <li>
            <a href="{{ route('dashboard') }}"
               class="block px-3 py-2 rounded hover:bg-gray-100">
               Dashboard
            </a>
        </li>

        <li>
            <button @click="openBarang = !openBarang"
                class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-100">
                <span>Data Barang</span>
                <span x-text="openBarang ? '▾' : '▸'"></span>
            </button>

            <ul x-show="openBarang" x-transition class="ml-4 mt-1 space-y-1">
                <li>
                    <a href="{{ route('tools.index') }}"
                       class="block px-3 py-1 rounded hover:bg-gray-100">
                       Data Tools
                    </a>
                </li>
                <li>
                    <a href="/consumable"
                       class="block px-3 py-1 rounded hover:bg-gray-100">
                       Data Consumable
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <button @click="openTransaksi = !openTransaksi"
                class="w-full flex justify-between items-center px-3 py-2 rounded hover:bg-gray-100">
                <span>Transaksi</span>
                <span x-text="openTransaksi ? '▾' : '▸'"></span>
            </button>

            <ul x-show="openTransaksi" x-transition class="ml-4 mt-1 space-y-1">
                <li>
                    <a href="{{ route('peminjaman.index') }}"
                       class="block px-3 py-1 rounded hover:bg-gray-100">
                       Peminjaman Tools
                    </a>
                </li>
                <li>
                    <a href="/transaksi-consumable"
                       class="block px-3 py-1 rounded hover:bg-gray-100">
                       Permintaan Consumable
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="/laporan"
               class="block px-3 py-2 rounded hover:bg-gray-100">
               Laporan
            </a>
        </li>

    </ul>

    <form method="POST" action="{{ route('logout') }}" class="mt-8">
        @csrf
        <button class="text-red-500 text-sm">Logout</button>
    </form>
</aside>

    <main class="flex-1 p-6">
        @yield('content')
    </main>

</div>
</body>
</html>