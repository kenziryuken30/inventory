<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: #e9edf2;
            overflow-x: hidden;
            overflow-y: auto;
        }

        /* TAMBAHAN: Matikan semua transition saat halaman pertama kali load */
        body.page-loading * {
            transition: none !important;
        }

        .sidebar-bg {
            background: url("{{ asset('images/siedbar.png') }}") no-repeat left center;
            background-size: 256px 100% !important;
            background-color: #f0f7ff;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

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

        .sidebar-toggle-btn {
            z-index: 10000 !important;
        }

        @media (max-width: 1023px) {
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
                font-size: 13px;
            }

            thead th,
            tbody td {
                padding: 6px 10px !important;
            }
        }

        @media (max-width: 640px) {
            table {
                font-size: 12px;
            }

            thead th,
            tbody td {
                padding: 5px 8px !important;
            }
        }

        @media (max-width: 640px) {
            .p-4,
            .p-5,
            .p-6 {
                padding: 0.75rem !important;
            }

            .p-3 {
                padding: 0.6rem !important;
            }
        }

        @media (max-width: 640px) {
            h1 { font-size: 1.25rem !important; }
            h2 { font-size: 1.1rem !important; }
            h3 { font-size: 1rem !important; }
            p, span, label, td, th, a, button { font-size: 13px !important; }
            .text-xs { font-size: 11px !important; }
            .text-sm { font-size: 12px !important; }
            .text-lg { font-size: 1rem !important; }
            .text-xl { font-size: 1.15rem !important; }
            .text-2xl { font-size: 1.3rem !important; }
            .text-3xl { font-size: 1.5rem !important; }
        }

        @media (max-width: 640px) {
            .btn,
            button:not(.menu-item):not(.ph):not(.sidebar-toggle-btn):not([class*="fixed"]) {
                padding: 6px 12px !important;
                font-size: 12px !important;
            }

            .gap-2 { gap: 0.35rem !important; }
            .gap-3 { gap: 0.5rem !important; }
            .gap-4 { gap: 0.65rem !important; }
        }

        @media (max-width: 640px) {
            input, select, textarea {
                font-size: 14px !important;
                padding: 6px 10px !important;
            }
        }

        @media (max-width: 640px) {
            .badge,
            [class*="badge"],
            [class*="px-3"][class*="py-1"] {
                padding: 2px 8px !important;
                font-size: 11px !important;
            }
        }
    </style>
</head>

<!-- TAMBAHAN: class page-loading di body saat pertama kali load -->
<body class="page-loading"
      x-data="{ 
        sidebarOpen: localStorage.getItem('sidebarOpen') !== null 
            ? localStorage.getItem('sidebarOpen') === 'true' 
            : window.innerWidth >= 1024 
      }"
      x-init="
        $watch('sidebarOpen', (val) => {
            localStorage.setItem('sidebarOpen', val);
        });
        $store.modal = {open: false};
        
        // HAPUS class page-loading setelah Alpine selesai render posisi awal
        $nextTick(() => {
            setTimeout(() => {
                document.body.classList.remove('page-loading');
            }, 50);
        });
      ">

    <div class="relative flex min-h-screen w-full">

        <!-- Overlay HP -->
        <div
            x-show="sidebarOpen"
            x-transition.opacity
            class="fixed inset-0 bg-black/40 z-40 lg:hidden"
            @click="sidebarOpen = false">
        </div>

        <!-- Sidebar -->
        <aside
            :class="sidebarOpen
                ? 'translate-x-0 w-64'
                : 'w-20'"
            class="fixed left-0 top-0 h-screen z-50 flex flex-col shadow-2xl transition-all duration-300 sidebar-bg overflow-hidden">

            <div class="flex-none px-4 py-4 bg-[#D5EEFF]">
                <div class="flex items-center gap-3 ml-2">
                    <img src="{{ asset('images/tecno.png') }}" class="w-10 flex-shrink-0">
                    <div x-show="sidebarOpen" x-transition:enter.duration.300ms class="whitespace-nowrap">
                        <h1 class="font-bold text-gray-800 leading-tight">Inventory</h1>
                        <p class="text-xs text-gray-600 leading-tight">Management System</p>
                    </div>
                </div>
            </div>

            <div class="divider flex-none"></div>

            <div class="flex-1 pt-2 pb-6">
                <ul class="space-y-3 text-[15px] font-medium px-3">

                    <li>
                        <a href="{{ route('dashboard') }}" class="menu-item flex items-center gap-4 px-4 py-3 rounded-lg whitespace-nowrap">
                            <i class="ph ph-house text-2xl flex-shrink-0"></i>
                            <span x-show="sidebarOpen">Dashboard</span>
                        </a>
                    </li>

                    <li x-data="{ open: false }">
                        <button @click="open = !open" class="menu-item w-full flex justify-between items-center px-4 py-3 rounded-lg">
                            <div class="flex items-center gap-4">
                                <i class="ph ph-archive-box text-2xl flex-shrink-0"></i>
                                <span x-show="sidebarOpen">Data Barang</span>
                            </div>
                            <i x-show="sidebarOpen" class="ph ph-caret-down text-sm transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <ul x-show="open && sidebarOpen" x-transition x-cloak class="ml-11 mt-1 space-y-1">
                            <li><a href="{{ route('tools.index') }}" class="block px-4 py-2 rounded hover:bg-white/60 text-sm font-normal">Data Tools</a></li>
                            <li><a href="{{ route('consumable.index') }}" class="block px-4 py-2 rounded hover:bg-white/60 text-sm font-normal">Data Consumable</a></li>
                        </ul>
                    </li>

                    <li x-data="{ open: false }">
                        <button @click="open = !open" class="menu-item w-full flex justify-between items-center px-4 py-3 rounded-lg">
                            <div class="flex items-center gap-4">
                                <i class="ph ph-shopping-cart text-2xl flex-shrink-0"></i>
                                <span x-show="sidebarOpen">Transaksi</span>
                            </div>
                            <i x-show="sidebarOpen" class="ph ph-caret-down text-sm transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <ul x-show="open && sidebarOpen" x-transition x-cloak class="ml-11 mt-1 space-y-1">
                            <li><a href="{{ route('peminjaman.index') }}" class="block px-4 py-2 rounded hover:bg-white/60 text-sm font-normal">Peminjaman Tools</a></li>
                            <li><a href="{{ route('transaksi.index') }}" class="block px-4 py-2 rounded hover:bg-white/60 text-sm font-normal">Permintaan Consumable</a></li>
                        </ul>
                    </li>

                    <li x-data="{ open: false }">
                        <button @click="open = !open" class="menu-item w-full flex justify-between items-center px-4 py-3 rounded-lg">
                            <div class="flex items-center gap-4">
                                <i class="ph ph-file-text text-2xl flex-shrink-0"></i>
                                <span x-show="sidebarOpen">Laporan</span>
                            </div>
                            <i x-show="sidebarOpen" class="ph ph-caret-down text-sm transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <ul x-show="open && sidebarOpen" x-transition x-cloak class="ml-11 mt-1 space-y-1">
                            <li><a href="{{ route('laporan.tools.transaksi') }}" class="block px-4 py-2 rounded hover:bg-white/60 text-sm font-normal">Laporan Tools</a></li>
                            <li><a href="{{ route('laporan.consumable.transaksi') }}" class="block px-4 py-2 rounded hover:bg-white/60 text-sm font-normal">Laporan Consumable</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{ route('categories.index') }}" class="menu-item flex items-center gap-4 px-4 py-3 rounded-lg whitespace-nowrap">
                            <i class="ph ph-squares-four text-2xl flex-shrink-0"></i>
                            <span x-show="sidebarOpen">Kategory</span>
                        </a>
                    </li>

                </ul>
            </div>

            <div class="divider flex-none"></div>

            <div class="flex-none px-4 py-4 bg-[#D5EEFF]">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="flex items-center gap-4 px-4 py-3 w-full rounded-lg text-red-600 hover:bg-red-100 transition text-[15px] font-medium">
                        <i class="ph ph-sign-out text-2xl flex-shrink-0"></i>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Logout</span>
                    </button>
                </form>
            </div>

        </aside>

        <!-- Toggle Button -->
        <button
            @click="sidebarOpen = !sidebarOpen"
            :class="sidebarOpen ? 'left-[232px]' : 'left-[60px]'"
            class="sidebar-toggle-btn fixed top-[50px] bg-cyan-500 text-white w-8 h-8 rounded-full shadow-lg flex items-center justify-center border-2 border-white transition-all duration-300">
            <i class="ph ph-list text-sm"></i>
        </button>

        <!-- Main Content -->
        <div class="flex-1 transition-all duration-300 min-h-screen overflow-y-auto relative z-10"
             :style="{ paddingLeft: sidebarOpen ? '256px' : '80px' }">

            <img src="{{ asset('images/kiri.png') }}"
                :style="{ left: sidebarOpen ? '256px' : '80px', transition: 'left 0.3s ease' }"
                class="hidden lg:block fixed bottom-0 w-[380px] pointer-events-none -z-10 opacity-60">

            <img src="{{ asset('images/kana1.png') }}"
                class="hidden lg:block fixed right-0 bottom-0 w-[380px] pointer-events-none -z-10 opacity-60">

            <main class="px-3 py-2 sm:px-5 sm:py-3 lg:px-8 lg:pt-3 lg:pb-8">
                @yield('content')
            </main>

            <div
                x-show="$store.modal.open"
                x-transition.opacity
                class="fixed inset-0 bg-black/30 backdrop-blur-md z-[999]">
            </div>

        </div>

    </div>

</body>

</html>