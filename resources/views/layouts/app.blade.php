<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Inventory</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/@phosphor-icons/web@2.1.1"></script>
    <script src="https://unpkg.com/phosphor-icons"></script>
    <style>
        [x-cloak]{display:none!important}

body{
    font-size: 16px;
    background:#e9edf2;
    overflow-x:hidden;
    font-size:16px;
}

html,body{
    height: 100%;
    margin: 0;
    padding: 0;
}

.sidebar-bg {
    background: url("{{ asset('images/siedbar.png') }}") no-repeat;
    background-position: center bottom;
    background-size: 100% 100%; 
    background-color: #f0f7ff; 
}

.no-scrollbar::-webkit-scrollbar{
    display:none;
}

.no-scrollbar{
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.menu-item i{
    font-size:15px;
    width:22px;
    text-align:center;
}

        .menu-item i{
            font-size:20px;
        }

       .menu-item:hover{
    background:rgba(255,255,255,.75);
    transform:translateX(4px);
    box-shadow:0 4px 10px rgba(0,0,0,0.08);
}

        .divider{
            border-top:1px solid rgba(15,23,42,.2);
        }
    </style>
</head>

<body x-data="{ sidebarOpen: true }">

<div class="flex min-h-screen w-full relative">

    <img src="{{ asset('images/kiri.png') }}"
        class="hidden lg:block absolute bottom-0 left-64 w-[340px] pointer-events-none -z-10">

    <img src="{{ asset('images/kana1.png') }}"
        class="hidden lg:block absolute right-0 bottom-0 w-[340px] pointer-events-none -z-10">

    <!-- SIDEBAR -->
<aside 
:class="sidebarOpen ? 'w-64' : 'w-20'"
class="fixed left-0 top-0 h-screen z-40 shadow-2xl transition-all duration-300 sidebar-bg flex flex-col overflow-visible">

        <div class="flex flex-col h-full w-full overflow-hidden">

        <!-- HEADER (WARNA BARU) -->
        <div class="flex-none px-4 py-3 bg-[#D5EEFF] border-b border-gray-200/50">
            <div class="flex items-center gap-3 ml-2">
                <img src="{{ asset('images/tecno.png') }}" class="w-10">
                <div x-show="sidebarOpen" x-transition>
                    <h1 class="font-bold text-gray-800 leading-tight">Inventory</h1>
                    <p class="text-xs text-gray-600 leading-tight">Management System</p>
                </div>
            </div>
        </div>

            <div class="divider flex-none"></div>

        <!-- MENU -->
         <div class="flex-1 min-h-0 overflow-y-auto no-scrollbar py-6 ">
            <ul class="space-y-2 text-sm px-5">

                <li>
                    <a href="{{ route('dashboard') }}"
                      class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg whitespace-nowrap">
                        <i class="ph ph-house"></i>
                        <span x-show="sidebarOpen">Dashboard</span>
                    </a>
                </li>

                <!-- DATA BARANG -->
                <li x-data="{ open:false }">
                    <button @click="open=!open"
                        class="menu-item w-full flex justify-between items-center px-3 py-2 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="ph ph-archive-box"></i>
                            <span x-show="sidebarOpen">Data Barang</span>
                        </div>
                        <span x-show="sidebarOpen" x-text="open ? '▾':'▸'"></span>
                    </button>

                    <ul x-show="open && sidebarOpen" x-transition x-cloak
                        class="ml-9 mt-1 space-y-1">
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
                <li x-data="{ open:false }">
                    <button @click="open=!open"
                        class="menu-item w-full flex justify-between items-center px-3 py-2 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="ph ph-arrows-left-right"></i>
                            <span x-show="sidebarOpen">Transaksi</span>
                        </div>
                        <span x-show="sidebarOpen" x-text="open ? '▾':'▸'"></span>
                    </button>

                    <ul x-show="open && sidebarOpen" x-transition x-cloak
                        class="ml-9 mt-1 space-y-1">
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
                <li x-data="{ open:false }">
                    <button @click="open=!open"
                        class="menu-item w-full flex justify-between items-center px-3 py-2 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="ph ph-file-text"></i>
                            <span x-show="sidebarOpen">Laporan</span>
                        </div>
                        <span x-show="sidebarOpen" x-text="open ? '▾':'▸'"></span>
                    </button>

                    <ul x-show="open && sidebarOpen" x-transition x-cloak
                        class="ml-9 mt-1 space-y-1">
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
        </div>

        <div class="divider flex-none"></div>

        <!-- LOGOUT (WARNA BARU) -->
        <div class="flex-none px-4 py-3 bg-[#D5EEFF] border-t border-gray-200/50">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    class="flex items-center gap-3 px-3 py-2 w-full rounded-lg text-red-600 hover:bg-red-100 transition text-sm">
                    <i class="ph ph-sign-out text-lg"></i>
                    <span x-show="sidebarOpen" class="font-medium">Logout</span>
                </button>
            </form>
        </div>
</div>

      <button
    @click="sidebarOpen = !sidebarOpen"
    class="absolute top-6 -right-5 bg-cyan-500 text-white w-10 h-10
    rounded-full shadow-lg z-50 transition-all duration-300
    flex items-center justify-center border-2 border-white">
    <i class="ph ph-list text-xl"></i>
    </button>

</aside>

<main 
:class="sidebarOpen ? 'ml-64' : 'ml-20'"
class="p-6 md:p-8 lg:p-10 w-full min-h-screen transition-all duration-300">
    @yield('content')
</main>

</div>
</body>
</html>