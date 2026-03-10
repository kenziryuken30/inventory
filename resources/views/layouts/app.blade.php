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

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: #e9edf2;
        }

        /* Memastikan gambar sidebar memenuhi seluruh tinggi layar */
        .sidebar-bg {
            background: url("{{ asset('images/siedbar.png') }}") no-repeat;
            background-position: center bottom;
            background-size: cover !important;
            background-color: #f0f7ff;
        }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .menu-item { transition: all 0.2s ease; }
        .menu-item:hover {
            background: rgba(255, 255, 255, 0.7);
            transform: translateX(4px);
        }

        .divider { border-top: 1px solid rgba(15, 23, 42, 0.08); }
    </style>
</head>

<body x-data="{ 
    sidebarOpen: true,
    isMobile: window.innerWidth < 1024 
}" @resize.window="isMobile = window.innerWidth < 1024" class="h-full font-sans overflow-x-hidden">

    <div class="flex min-h-screen w-full relative">

        <aside 
            :class="sidebarOpen ? 'w-64 translate-x-0' : (isMobile ? '-translate-x-full' : 'w-20 translate-x-0')"
            class="fixed left-0 top-0 h-screen z-50 flex flex-col shadow-2xl transition-all duration-300 sidebar-bg transform">
            
            <div class="flex-none px-4 py-5 bg-[#D5EEFF]/90 backdrop-blur-sm">
                <div class="flex items-center gap-3 ml-2">
                    <img src="{{ asset('images/tecno.png') }}" class="w-10">
                    <div x-show="sidebarOpen" x-transition class="whitespace-nowrap">
                        <h1 class="font-bold text-gray-800 leading-tight">Inventory</h1>
                        <p class="text-xs text-gray-600 leading-tight">Management System</p>
                    </div>
                </div>
            </div>

            <div class="divider flex-none"></div>

            <div class="flex-1 overflow-y-auto no-scrollbar py-6">
                <ul class="space-y-2 text-sm px-4">
                    <li>
                        <a href="{{ route('dashboard') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-xl">
                            <i class="ph ph-house text-2xl text-cyan-600"></i>
                            <span x-show="sidebarOpen" class="font-medium text-gray-700">Dashboard</span>
                        </a>
                    </li>

                    <li x-data="{ open: false }">
                        <button @click="open = !open" class="menu-item w-full flex justify-between items-center px-3 py-2 rounded-xl text-left">
                            <div class="flex items-center gap-3">
                                <i class="ph ph-archive-box text-2xl text-cyan-600"></i>
                                <span x-show="sidebarOpen" class="font-medium text-gray-700">Data Barang</span>
                            </div>
                            <i x-show="sidebarOpen" class="ph ph-caret-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <ul x-show="open && sidebarOpen" x-cloak x-transition class="ml-10 mt-1 space-y-1 text-gray-600">
                            <li><a href="{{ route('tools.index') }}" class="block py-1 hover:text-cyan-600">Data Tools</a></li>
                            <li><a href="{{ route('consumable.index') }}" class="block py-1 hover:text-cyan-600">Data Consumable</a></li>
                        </ul>
                    </li>

                    <li x-data="{ open: false }">
                        <button @click="open = !open" class="menu-item w-full flex justify-between items-center px-3 py-2 rounded-xl text-left">
                            <div class="flex items-center gap-3">
                                <i class="ph ph-arrows-left-right text-2xl text-cyan-600"></i>
                                <span x-show="sidebarOpen" class="font-medium text-gray-700">Transaksi</span>
                            </div>
                            <i x-show="sidebarOpen" class="ph ph-caret-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <ul x-show="open && sidebarOpen" x-cloak x-transition class="ml-10 mt-1 space-y-1 text-gray-600">
                            <li><a href="{{ route('peminjaman.index') }}" class="block py-1 hover:text-cyan-600">Peminjaman Tools</a></li>
                            <li><a href="{{ route('transaksi.index') }}" class="block py-1 hover:text-cyan-600">Permintaan Consumable</a></li>
                        </ul>
                    </li>

                    <li x-data="{ open: false }">
                        <button @click="open = !open" class="menu-item w-full flex justify-between items-center px-3 py-2 rounded-xl text-left">
                            <div class="flex items-center gap-3">
                                <i class="ph ph-file-text text-2xl text-cyan-600"></i>
                                <span x-show="sidebarOpen" class="font-medium text-gray-700">Laporan</span>
                            </div>
                            <i x-show="sidebarOpen" class="ph ph-caret-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <ul x-show="open && sidebarOpen" x-cloak x-transition class="ml-10 mt-1 space-y-1 text-gray-600">
                            <li><a href="{{ route('laporan.tools.transaksi') }}" class="block py-1 hover:text-cyan-600">Laporan Tools</a></li>
                            <li><a href="{{ route('laporan.consumable.transaksi') }}" class="block py-1 hover:text-cyan-600">Laporan Consumable</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="divider flex-none"></div>

            <div class="flex-none px-4 py-4 bg-[#D5EEFF]/90 backdrop-blur-sm">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="flex items-center gap-3 px-3 py-2 w-full rounded-xl text-red-600 hover:bg-red-50 transition">
                        <i class="ph ph-sign-out text-2xl"></i>
                        <span x-show="sidebarOpen" class="font-bold whitespace-nowrap">Logout</span>
                    </button>
                </form>
            </div>

            <button
                @click="sidebarOpen = !sidebarOpen"
                class="absolute top-6 -right-5 bg-cyan-500 text-white w-10 h-10 rounded-full shadow-lg z-50 flex items-center justify-center border-4 border-white transition-transform active:scale-90">
                <i class="ph text-xl" :class="sidebarOpen ? 'ph-caret-left' : 'ph-list'"></i>
            </button>
        </aside>

        <div 
            :class="sidebarOpen ? 'lg:ml-64' : (isMobile ? 'ml-0' : 'lg:ml-20')" 
            class="flex-1 transition-all duration-300 min-h-screen relative flex flex-col bg-[#e9edf2]">
            
            <img src="{{ asset('images/kiri.png') }}" class="hidden xl:block fixed bottom-0 left-64 w-[300px] pointer-events-none opacity-40 -z-10 transition-all duration-300" :style="!sidebarOpen && 'left: 80px'">
            <img src="{{ asset('images/kana1.png') }}" class="hidden xl:block fixed right-0 bottom-0 w-[300px] pointer-events-none opacity-40 -z-10">

            <main class="p-6 md:p-8 lg:p-10 w-full overflow-x-auto">
                <div x-show="isMobile" class="flex items-center justify-between mb-6 lg:hidden bg-white p-4 rounded-2xl shadow-sm">
                    <span class="font-bold text-cyan-600">Inventory System</span>
                    <button @click="sidebarOpen = true" class="p-2 bg-gray-100 rounded-lg">
                        <i class="ph ph-list text-2xl text-cyan-600"></i>
                    </button>
                </div>

                <div class="w-full">
                    @yield('content')
                </div>
            </main>
        </div>

        <div x-show="isMobile && sidebarOpen" 
             @click="sidebarOpen = false" 
             class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 lg:hidden"
             x-transition.opacity>
        </div>

    </div>

</body>
</html>