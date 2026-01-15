<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System</title>

    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-blue-900 text-white flex flex-col">

        <!-- LOGO -->
        <div class="px-6 py-5 border-b border-blue-800">
            <h1 class="text-xl font-bold">Inventory</h1>
            <p class="text-sm text-blue-300">Management System</p>
        </div>

        <!-- MENU -->
        <nav class="flex-1 px-4 py-4 space-y-2">
            <a href="#"
               class="flex items-center gap-3 px-4 py-2 rounded-lg bg-blue-800">
                📊 Dashboard
            </a>

            <a href="#"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-800">
                📦 Data Barang
            </a>

            <a href="#"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-800">
                🔄 Peminjaman Alat
            </a>

            <a href="#"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-800">
                🛒 Kasir Sparepart
            </a>

            <a href="#"
               class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-blue-800">
                📑 Laporan
            </a>
        </nav>

        <!-- LOGOUT (GANTI © 2024) -->
        <div class="p-4 border-t border-blue-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2
                               text-red-300 hover:text-white
                               hover:bg-red-600
                               px-4 py-2 rounded-lg transition">
                    🚪 Logout
                </button>
            </form>
        </div>

    </aside>

    <!-- CONTENT -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>

</div>

</body>
</html>
