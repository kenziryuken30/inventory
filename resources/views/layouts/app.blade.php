<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Inventory</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">
<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-white shadow-md p-4">
        <h1 class="text-lg font-bold mb-6">Inventory</h1>

        <ul class="space-y-2">
            <li><a href="#" class="block px-3 py-2 rounded bg-blue-100">Dashboard</a></li>
            <li><a href="#" class="block px-3 py-2 rounded hover:bg-gray-100">Data Barang</a></li>
            <li><a href="#" class="block px-3 py-2 rounded hover:bg-gray-100">Transaksi</a></li>
            <li><a href="#" class="block px-3 py-2 rounded hover:bg-gray-100">Laporan</a></li>
        </ul>

        <form method="POST" action="{{ route('logout') }}" class="mt-10">
            @csrf
            <button class="text-red-500 text-sm">Logout</button>
        </form>
    </aside>

    {{-- CONTENT --}}
    <main class="flex-1 p-6">
        @yield('content')
    </main>

</div>
</body>
</html>
