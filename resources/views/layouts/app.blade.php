<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1"></script>
    
    <style>
        [x-cloak] { display: none !important; }

        /* Pastikan html dan body mengambil 100% tinggi layar */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: #e9edf2;
            overflow-x: hidden;
        }

        /* Perbaikan Background Sidebar */
        .sidebar-bg {
            background: url("{{ asset('images/siedbar.png') }}") no-repeat;
            background-position: center bottom;
            background-size: 100% 100% !important; /* Memaksa gambar mengisi seluruh area sidebar */
            background-color: #f0f7ff;
        }

        /* Menghilangkan scrollbar pada menu sidebar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .menu-item {
            transition: all 0.3s ease;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.75);
            transform: translateX(4px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .divider {
            border-top: 1px solid rgba(15, 23, 42, 0.1);
        }
    </style>
</head>

<body x-data="{ sidebarOpen: true }" class="h-full">

    <div class="relative flex min-h-screen w-full">

        <aside 
            :class="sidebarOpen ? 'w-64' : 'w-20'"
            class="fixed left-0 top-0 h-screen z-50 flex flex-col shadow-2xl transition-all duration-300 sidebar-bg overflow-visible">
            
            <div class="flex-none px-4 py-4 bg-[#D5EEFF]">
                <div class="flex items-center gap-3 ml-2">
                    <img src="{{ asset('images/tecno.png') }}" class="w-10">
                    <div x-show="sidebarOpen" x-transition:enter.duration.300ms>
                        <h1 class="font-bold text-gray-800 leading-tight">Inventory</h1>
                        <p class="text-xs text-gray-600 leading-tight">Management System</p>
                    </div>
                </div>
            </div>

            <div class="divider flex-none"></div>

            <div class="flex-1 overflow-y-auto no-scrollbar py-6">
                <ul class="space-y-2 text-sm px-4">
                    
                    <li>
                        <a href="{{ route('dashboard') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg whitespace-nowrap">
                            <i class="ph ph-house text-xl"></i>
                            <span x-show="sidebarOpen">Dashboard</span>
                        </a>
                    </li>

                    <li x-data="{ open: false }">
                        <button @click="open = !open" class="menu-item w-full flex justify-between items-center px-3 py-2 rounded-lg">
                            <div class="flex items-center gap-3">
                                <i class="ph ph-archive-box text-xl"></i>
                                <span x-show="sidebarOpen">Data Barang</span>
                            </div>
                            <i x-show="sidebarOpen" class="ph ph-caret-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <ul x-show="open && sidebarOpen" x-transition x-cloak class="ml-9 mt-1 space-y-1">
                            <li><a href="{{ route('tools.index') }}" class="block px-3 py-1 rounded hover:bg-white/60">Data Tools</a></li>
                            <li><a href="{{ route('consumable.index') }}" class="block px-3 py-1 rounded hover:bg-white/60">Data Consumable</a></li>
                        </ul>
                    </li>

                    <li x-data="{ open: false }">
                        <button @click="open = !open" class="menu-item w-full flex justify-between items-center px-3 py-2 rounded-lg">
                            <div class="flex items-center gap-3">
                                <i class="ph ph-arrows-left-right text-xl"></i>
                                <span x-show="sidebarOpen">Transaksi</span>
                            </div>
                            <i x-show="sidebarOpen" class="ph ph-caret-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <ul x-show="open && sidebarOpen" x-transition x-cloak class="ml-9 mt-1 space-y-1">
                            <li><a href="{{ route('peminjaman.index') }}" class="block px-3 py-1 rounded hover:bg-white/60">Peminjaman Tools</a></li>
                            <li><a href="{{ route('transaksi.index') }}" class="block px-3 py-1 rounded hover:bg-white/60">Permintaan Consumable</a></li>
                        </ul>
                    </li>

                    <li x-data="{ open: false }">
                        <button @click="open = !open" class="menu-item w-full flex justify-between items-center px-3 py-2 rounded-lg">
                            <div class="flex items-center gap-3">
                                <i class="ph ph-file-text text-xl"></i>
                                <span x-show="sidebarOpen">Laporan</span>
                            </div>
                            <i x-show="sidebarOpen" class="ph ph-caret-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <ul x-show="open && sidebarOpen" x-transition x-cloak class="ml-9 mt-1 space-y-1">
                            <li><a href="{{ route('laporan.tools.transaksi') }}" class="block px-3 py-1 rounded hover:bg-white/60">Laporan Tools</a></li>
                            <li><a href="{{ route('laporan.consumable.transaksi') }}" class="block px-3 py-1 rounded hover:bg-white/60">Laporan Consumable</a></li>
                        </ul>
                    </li>

                </ul>
            </div>

            <div class="divider flex-none"></div>

            <div class="flex-none px-4 py-4 bg-[#D5EEFF]">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="flex items-center gap-3 px-3 py-2 w-full rounded-lg text-red-600 hover:bg-red-100 transition text-sm">
                        <i class="ph ph-sign-out text-xl"></i>
                        <span x-show="sidebarOpen" class="font-medium whitespace-nowrap">Logout</span>
                    </button>
                </form>
            </div>

            <button
                @click="sidebarOpen = !sidebarOpen"
                class="absolute top-6 -right-5 bg-cyan-500 text-white w-10 h-10 rounded-full shadow-lg z-50 flex items-center justify-center border-2 border-white transition-transform active:scale-90">
                <i class="ph ph-list text-xl"></i>
            </button>
        </aside>

        <div :class="sidebarOpen ? 'pl-64' : 'pl-20'" class="flex-1 transition-all duration-300 min-h-screen relative">
            
            <img src="{{ asset('images/kiri.png') }}" class="hidden lg:block fixed bottom-0 left-64 w-[340px] pointer-events-none -z-10 opacity-70">
            <img src="{{ asset('images/kana1.png') }}" class="hidden lg:block fixed right-0 bottom-0 w-[340px] pointer-events-none -z-10 opacity-70">

            <main class="p-6 md:p-8 lg:p-10">
                @yield('content')
            </main>
        </div>

    </div>

</body>
</html>