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
            background:#e9edf2;
            overflow-x:hidden;
        }

        .sidebar-bg{
            background:url("{{ asset('images/siedbar.png') }}") no-repeat center;
            background-size:cover;
        }

        .menu-item{
            transition:.2s;
            font-weight:500;
            color:#0f172a;
        }

        .menu-item i{
            font-size:20px;
        }

        .menu-item:hover{
            background:rgba(255,255,255,.65);
            transform:translateX(4px);
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
    class="relative z-10 flex flex-col bg-[#f4f6f9] shadow-2xl transition-all duration-300">

        <!-- HEADER (WARNA BARU) -->
        <div class="px-4 py-4 bg-[#D5EEFF]">
            <div class="flex items-center gap-3 ml-2">
                <img src="{{ asset('images/tecno.png') }}" class="w-10">
                <div x-show="sidebarOpen">
                    <h1 class="font-bold text-gray-800 leading-tight">Inventory</h1>
                    <p class="text-xs text-gray-600 leading-tight">Management System</p>
                </div>
            </div>
        </div>

        <div class="divider"></div>

        <!-- MENU -->
        <div class="flex-1 sidebar-bg py-6">
            <ul class="space-y-2 text-sm px-4">

                <li>
                    <a href="{{ route('dashboard') }}"
                       class="menu-item flex items-center gap-3 px-3 py-2 rounded-lg">
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

        <div class="divider"></div>

        <!-- LOGOUT (WARNA BARU) -->
        <div class="px-4 py-3 bg-[#D5EEFF]">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    class="flex items-center gap-3 px-3 py-2 w-full rounded-lg
                           text-red-600 hover:bg-red-100 transition text-sm">
                    <i class="ph ph-sign-out text-lg"></i>
                    <span x-show="sidebarOpen" class="font-medium">Logout</span>
                </button>
            </form>
        </div>

    </aside>

    <!-- TOGGLE -->
   <button
@click="sidebarOpen=!sidebarOpen"
:class="sidebarOpen ? 'left-64':'left-20'"
class="fixed top-6 bg-cyan-500 text-white w-10 h-10 rounded-full shadow-lg z-50 transition-all flex items-center justify-center">
<i class="ph ph-list"></i>
</button>

    <main class="flex-1 w-full p-4 md:p-6 lg:p-8 relative z-10 ml-6">
        @yield('content')
    </main>

</div>
</body>
</html>