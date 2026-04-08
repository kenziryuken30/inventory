@extends('layouts.app')

@section('content')

    <div class="max-w-7xl mx-auto w-full pt-4 sm:pt-6 pb-8 sm:pb-10">

        {{-- ================= HEADER ================= --}}
        <div class="mb-4 sm:mb-5">
            {{-- WARNA JUDUL DIUBAH JADI BIRU TEMA --}}
            <h1 class="text-2xl sm:text-[26px] font-extrabold text-[#5EA6FF] tracking-tight leading-tight">
                Data Tools
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1 leading-snug">
                Kelola semua data peralatan, inventaris, dan kondisi barang di satu tempat.
            </p>
        </div>

        {{-- ================= NOTIF TOAST ================= --}}
        <div id="notifWrap" class="hidden mb-5">
            <div id="notifBox"
                class="relative overflow-hidden flex items-center gap-3 px-4 sm:px-5 py-3 sm:py-3.5 rounded-2xl shadow-lg border">
                <div id="notifIcon" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"></div>
                <p id="notifText" class="text-xs sm:text-sm font-medium flex-1 min-w-0"></p>
                <button id="notifClose" class="ml-auto flex-shrink-0 opacity-50 hover:opacity-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div id="notifBar" class="absolute bottom-0 left-0 h-1 rounded-b-2xl" style="width:100%"></div>
            </div>
        </div>

        {{-- ================= SEARCH + FILTER + TAMBAH ================= --}}
        <div class="bg-gradient-to-b from-[#7FC4FF] to-[#5EA6FF] p-3 sm:p-5 rounded-2xl shadow-md mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row gap-2.5 sm:gap-3 items-stretch sm:items-center">

                <form method="GET" action="{{ route('tools.index') }}"
                    class="flex flex-col sm:flex-row gap-2.5 sm:gap-3 flex-1 w-full sm:w-auto">

                    {{-- SEARCH --}}
                    <div class="relative flex-1">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                            placeholder="Cari barang..."
                            class="w-full h-10 sm:h-[42px] bg-white rounded-xl pl-10 pr-10 text-sm font-medium text-gray-700 shadow-sm outline-none focus:ring-2 focus:ring-[#5EA6FF]/20 focus:border-[#5EA6FF] transition">
                        @if(request('search'))
                            <button type="button" id="clearSearch"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 text-sm font-bold">
                                ✕
                            </button>
                        @endif
                    </div>

                    {{-- FILTER --}}
                    <div class="relative">
                        <select name="condition" onchange="this.form.submit()"
                            class="w-full sm:w-[180px] h-10 sm:h-[42px] bg-white rounded-xl pl-3.5 pr-9 text-sm font-medium text-gray-700 shadow-sm outline-none appearance-none cursor-pointer transition focus:ring-2 focus:ring-[#5EA6FF]/20 focus:border-[#5EA6FF]">
                            <option value="">Semua Kondisi</option>
                            <option value="baik" {{ request('condition') === 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak" {{ request('condition') === 'rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="maintenance" {{ request('condition') === 'maintenance' ? 'selected' : '' }}>
                                Maintenance</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                </form>

                {{-- TOMBOL TAMBAH (GAYA BARU SESUAI CONTOH KATEGORI) --}}
                <button type="button" id="openTambahBarang"
                    class="group inline-flex items-center justify-center px-5 h-10 sm:h-[42px] rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all duration-200 tracking-wide border-2 border-[#5EA6FF] bg-white text-sm text-[#5EA6FF] hover:bg-[#5EA6FF] hover:text-white hover:shadow-blue-500/40 hover:-translate-y-0.5 whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2 transition-transform duration-300 group-hover:rotate-90" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Barang
                </button>

            </div>
        </div>

        {{-- ================= TABEL ================= --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden min-h-[400px]">
            <div class="overflow-x-auto">

                <table class="w-full text-sm min-w-[700px]">

                    <thead>
                        <tr class="bg-gradient-to-r from-[#7FC4FF] to-[#5EA6FF]">
                            <th
                                class="py-3 sm:py-4 px-3 sm:px-4 text-center text-[10px] sm:text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap w-[70px] sm:w-[80px]">
                                Foto</th>
                            <th
                                class="py-3 sm:py-4 px-3 sm:px-4 text-left text-[10px] sm:text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap pl-4 sm:pl-6">
                                Nama</th>
                            <th
                                class="py-3 sm:py-4 px-3 sm:px-4 text-center text-[10px] sm:text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap hidden md:table-cell">
                                Kategori</th>
                            <th
                                class="py-3 sm:py-4 px-3 sm:px-4 text-center text-[10px] sm:text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap hidden md:table-cell">
                                No Seri</th>
                            <th
                                class="py-3 sm:py-4 px-3 sm:px-4 text-center text-[10px] sm:text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">
                                Status</th>
                            <th
                                class="py-3 sm:py-4 px-3 sm:px-4 text-center text-[10px] sm:text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap hidden md:table-cell">
                                Kondisi</th>
                            <th
                                class="py-3 sm:py-4 px-3 sm:px-4 text-center text-[10px] sm:text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap w-[90px] sm:w-[110px]">
                                Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse ($tools as $tool)

                            @php $condition = $tool->latestCondition->condition ?? 'baik'; @endphp

                            <tr class="hover:bg-cyan-50 transition h-[60px] sm:h-[70px]">

                                {{-- FOTO --}}
                                <td class="py-2.5 sm:py-3 px-2 sm:px-3 text-center align-middle">
                                    @php
                                        $image = optional($tool->toolkit)->image;
                                    @endphp

                                    <img 
                                        src="{{ $image ? asset('storage/' . $image) : asset('images/no-image.png') }}"
                                        class="tools-img w-10 h-10 object-contain bg-white rounded-lg shadow p-1"
                                        
                                        {{-- fallback hanya untuk ERROR --}}
                                        onerror="this.src='{{ asset('images/error-image.png') }}'">
                                </td>

                                {{-- NAMA --}}
                                <td
                                    class="py-2.5 sm:py-3 px-3 sm:px-6 font-semibold text-gray-800 text-xs sm:text-sm whitespace-nowrap align-middle">
                                    {{ optional($tool->toolkit)->toolkit_name }}
                                </td>

                                {{-- KATEGORI --}}
                                <td class="py-2.5 sm:py-3 px-2 sm:px-3 text-center align-middle hidden md:table-cell">
                                    <span
                                        class="inline-block px-2.5 sm:px-3 py-1 text-[10px] sm:text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                        {{ optional(optional($tool->toolkit)->category)->category_name ?? '-' }}
                                    </span>
                                </td>

                                {{-- NO SERI --}}
                                <td
                                    class="py-2.5 sm:py-3 px-2 sm:px-3 text-center font-medium text-gray-700 text-xs sm:text-sm whitespace-nowrap align-middle hidden md:table-cell">
                                    {{ $tool->serial_number }}
                                </td>

                                {{-- STATUS --}}
                                <td class="py-2.5 sm:py-3 px-2 sm:px-3 text-center align-middle">
                                   @php
                                        $condition = $tool->latestCondition->condition ?? 'baik';
                                    @endphp

                                    @if ($tool->isPending)
                                        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs">
                                            Pending
                                        </span>

                                    @elseif ($tool->isDipinjam)
                                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">
                                            Dipinjam
                                        </span>

                                    @elseif ($condition == 'rusak')
                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">
                                            Tidak tersedia
                                        </span>

                                    @else
                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs">
                                            Tersedia
                                        </span>
                                    @endif
                                </td>

                                {{-- KONDISI --}}
                                <td class="py-2.5 sm:py-3 px-2 sm:px-3 text-center align-middle hidden md:table-cell">
                                    @if($condition == 'baik')
                                        <span
                                            class="inline-block px-2.5 sm:px-3 py-1 text-[10px] sm:text-xs font-semibold rounded-full border border-green-400 text-green-700 bg-green-50 whitespace-nowrap">Baik</span>
                                    @elseif($condition == 'rusak')
                                        <span
                                            class="inline-block px-2.5 sm:px-3 py-1 text-[10px] sm:text-xs font-semibold rounded-full border border-red-400 text-red-700 bg-red-50 whitespace-nowrap">Rusak</span>
                                    @else
                                        <span
                                            class="inline-block px-2.5 sm:px-3 py-1 text-[10px] sm:text-xs font-semibold rounded-full border border-yellow-400 text-yellow-600 bg-yellow-100 whitespace-nowrap">Maintenance</span>
                                    @endif
                                </td>

                                {{-- AKSI --}}
                                <td class="py-2.5 sm:py-3 px-2 sm:px-3 text-center align-middle">
                                    <div class="flex justify-center items-center gap-1.5 sm:gap-2.5">

                                        @if(($tool->latestCondition->condition ?? '') === 'maintenance')
                                            <form action="{{ route('tools.finishMaintenance', $tool->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" title="Selesai Maintenance"
                                                    class="w-8 h-8 sm:w-[34px] sm:h-[34px] rounded-lg bg-green-50 text-green-600 hover:bg-green-100 hover:scale-110 transition flex items-center justify-center">
                                                    <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px]" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                        <button type="button"
                                            class="editBtn w-8 h-8 sm:w-[34px] sm:h-[34px] rounded-lg bg-gray-50 text-gray-500 hover:bg-blue-50 hover:text-blue-600 hover:scale-110 transition flex items-center justify-center"
                                            data-id="{{ $tool->id }}" data-name="{{ $tool->toolkit->toolkit_name }}"
                                            data-category="{{ $tool->toolkit->category_id }}"
                                            data-serial="{{ $tool->serial_number }}" data-image="{{ $tool->toolkit->image }}"
                                            title="Edit Barang">
                                            <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px]" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                            </svg>
                                        </button>

                                        @if (strtolower($tool->status) == 'dipinjam')
                                            <button type="button" onclick="alert('Barang sedang dipinjam, tidak bisa dihapus!')"
                                                class="w-8 h-8 sm:w-[34px] sm:h-[34px] rounded-lg bg-gray-50 text-gray-300 opacity-40 cursor-not-allowed flex items-center justify-center"
                                                title="Barang sedang dipinjam">
                                                <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px]" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 7h12M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-7 0h8l-1 13a2 2 0 01-2 2H11a2 2 0 01-2-2L8 7z" />
                                                </svg>
                                            </button>
                                        @else
                                            <form action="{{ route('tools.destroy', $tool->id) }}" method="POST" class="deleteForm">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus Barang"
                                                    class="w-8 h-8 sm:w-[34px] sm:h-[34px] rounded-lg bg-gray-50 text-gray-500 hover:bg-red-50 hover:text-red-600 hover:scale-110 transition flex items-center justify-center">
                                                    <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px]" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="1.8">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 
                                                                    .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-gray-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="text-4xl">📦</div>
                                        <p class="text-sm text-gray-500">Tidak ada data tool</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>

            </div>
        </div>

        {{-- ================= PAGINATION ================= --}}
        <div class="mt-5 sm:mt-6 flex justify-center">
            {{ $tools->links() }}
        </div>

        {{-- ================= MODAL TAMBAH ================= --}}
        <div id="tambahBarangModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[10001] p-3 sm:p-4">
            <div
                class="w-[calc(100%-1.5rem)] sm:w-11/12 max-w-xl bg-gradient-to-b from-[#f7f7f7] to-[#eee] rounded-2xl shadow-2xl max-h-[92vh] flex flex-col">

                <form action="{{ route('tools.store') }}" method="POST" enctype="multipart/form-data"
                    class="flex flex-col min-h-0">
                    @csrf

                    <div
                        class="flex justify-between items-center px-5 sm:px-8 pt-5 sm:pt-6 pb-3 border-b border-gray-200 flex-shrink-0">
                        {{-- JUDUL MODAL DIUBAH JADI BIRU --}}
                        <h2 class="text-base sm:text-lg font-bold text-[#5EA6FF]">Tambah Barang</h2>
                        <button type="button" id="closeTambahBarang"
                            class="text-gray-500 hover:text-gray-700 text-xl transition hover:scale-110">✕</button>
                    </div>

                    <div class="flex-1 overflow-y-auto min-h-0 px-5 sm:px-8 py-4 space-y-3">
                        <input type="text" name="toolkit_name" placeholder="Nama Barang"
                            class="w-full px-4 py-3 bg-[#f5f5f5] border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#5EA6FF] focus:ring-2 focus:ring-[#5EA6FF]/20 transition">

                        <select name="category_id" required
                            class="w-full px-4 py-3 bg-[#f5f5f5] border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#5EA6FF] focus:ring-2 focus:ring-[#5EA6FF]/20 transition">
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>

                        <input type="text" name="serial_number" placeholder="No Seri"
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-xl text-sm">
                        @error('serial_number')
                            <p class="text-red-500 text-xs mt-1 mb-2">
                                {{ $message }}
                            </p>
                        @enderror

                        <input type="file" name="image"
                            class="w-full px-4 py-2 bg-[#e6e6e6] border border-gray-300 rounded-lg text-xs">
                    </div>

                    <div class="flex justify-end gap-3 px-5 sm:px-8 py-4 border-t border-gray-200 flex-shrink-0">
                        <button type="button" id="cancelTambahBarang"
                            class="px-5 py-2.5 bg-[#dcdcdc] text-gray-700 rounded-xl text-sm font-semibold hover:bg-[#c5c5c5] transition">
                            Batal
                        </button>
                        {{-- TOMBOL SUBMIT DIUBAH JADI GRADASI BIRU --}}
                        <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-b from-[#7FC4FF] to-[#5EA6FF] text-white rounded-xl text-sm font-semibold shadow-lg shadow-blue-500/30 hover:opacity-90 hover:-translate-y-0.5 transition">
                            Tambah
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL EDIT ================= --}}
        <div id="editBarangModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[10001] p-3 sm:p-4">
            <div
                class="w-[calc(100%-1.5rem)] sm:w-11/12 max-w-xl bg-gradient-to-b from-[#f7f7f7] to-[#eee] rounded-2xl shadow-2xl max-h-[92vh] flex flex-col">

                <form id="editBarangForm" method="POST" enctype="multipart/form-data" class="flex flex-col min-h-0">
                    @csrf
                    @method('PUT')

                    <div
                        class="flex justify-between items-center px-5 sm:px-8 pt-5 sm:pt-6 pb-3 border-b border-gray-200 flex-shrink-0">
                        <h2 class="text-base sm:text-lg font-bold text-[#5EA6FF]">Edit Barang</h2>
                        <button type="button" id="closeEditModal"
                            class="text-gray-500 hover:text-gray-700 text-xl transition hover:scale-110">✕</button>
                    </div>

                    <div class="flex-1 overflow-y-auto min-h-0 px-5 sm:px-8 py-4 space-y-3">
                        <input type="text" name="toolkit_name" id="editName"
                            class="w-full px-4 py-3 bg-[#f5f5f5] border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#5EA6FF] focus:ring-2 focus:ring-[#5EA6FF]/20 transition">

                        <select name="category_id" id="editCategory"
                            class="w-full px-4 py-3 bg-[#f5f5f5] border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#5EA6FF] focus:ring-2 focus:ring-[#5EA6FF]/20 transition">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>

                        <input type="text" name="serial_number" id="editSerial"
                            class="w-full px-4 py-3 bg-[#f5f5f5] border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#5EA6FF] focus:ring-2 focus:ring-[#5EA6FF]/20 transition">

                        <input type="file" name="image"
                            class="w-full px-4 py-2 bg-[#e6e6e6] border border-gray-300 rounded-lg text-xs cursor-pointer">
                    </div>

                    <div class="flex justify-end gap-3 px-5 sm:px-8 py-4 border-t border-gray-200 flex-shrink-0">
                        <button type="button" id="cancelEditModal"
                            class="px-5 py-2.5 bg-[#dcdcdc] text-gray-700 rounded-xl text-sm font-semibold hover:bg-[#c5c5c5] transition">
                            Batal
                        </button>
                        {{-- TOMBOL UPDATE DIUBAH JADI GRADASI BIRU --}}
                        <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-b from-[#7FC4FF] to-[#5EA6FF] text-white rounded-xl text-sm font-semibold shadow-lg shadow-blue-500/30 hover:opacity-90 hover:-translate-y-0.5 transition">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL PREVIEW FOTO ================= --}}
        <div id="imagePreviewModal"
            class="fixed inset-0 bg-black/70 hidden items-center justify-center z-[10001] p-3 sm:p-4">
            <div class="relative">
                <button id="closePreview"
                    class="absolute -top-10 right-0 text-white text-3xl hover:scale-110 transition">✕</button>
                <img id="previewImg"
                    class="max-w-[calc(100vw-1.5rem)] sm:max-w-[500px] max-h-[75vh] object-contain rounded-xl shadow-2xl bg-white p-3 sm:p-6">
            </div>
        </div>

        <!-- MODAL DELETE -->
        <div id="deleteModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[10001]">

            <div class="bg-white rounded-2xl shadow-xl p-6 w-[350px] text-center">

                <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    🗑️
                </div>

                <h2 class="text-lg font-bold mb-2">Hapus Barang?</h2>
                <p class="text-sm text-gray-500 mb-5">
                    Data akan dihapus permanen
                </p>

                <div class="flex gap-3">
                    <button id="cancelDelete" class="flex-1 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">
                        Batal
                    </button>

                    <button id="confirmDelete" class="flex-1 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600">
                        Ya, Hapus
                    </button>
                </div>

            </div>
        </div>

    </div>

    <style>
        /* ★ NOTIF ANIMASI — cuma ini yang perlu custom ★ */
        #notifWrap {
            animation: notifSlideIn 0.3s ease-out;
        }

        @keyframes notifSlideIn {
            from {
                opacity: 0;
                transform: translateY(-12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #notifWrap.hiding {
            animation: notifSlideOut 0.25s ease-in forwards;
        }

        @keyframes notifSlideOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-12px);
            }
        }

        #notifBar {
            transition: width 3.5s linear;
        }

        /* ★ GAMBAR — hover scale doang ★ */
        .tools-img {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .tools-img:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        }

        /* ★ SCROLLBAR TABEL ★ */
        .overflow-x-auto {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .overflow-x-auto::-webkit-scrollbar {
            height: 5px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ★ FIX IMAGE ERROR ★
            document.querySelectorAll('.tools-img[data-fallback]').forEach(img => {
                img.addEventListener('error', function () {
                    if (this.dataset.errored) return;
                    this.dataset.errored = '1';
                    this.src = this.dataset.fallback;
                });
            });

            // ★ NOTIF SYSTEM — DIUBAH WARNA SUKSES JADI BIRU ★
            const notifWrap = document.getElementById('notifWrap');
            const notifBox = document.getElementById('notifBox');
            const notifIcon = document.getElementById('notifIcon');
            const notifText = document.getElementById('notifText');
            const notifBar = document.getElementById('notifBar');
            const notifClose = document.getElementById('notifClose');
            let notifTimer = null;

            function showNotif(message, type) {
                if (notifTimer) clearTimeout(notifTimer);
                notifWrap.classList.remove('hidden', 'hiding');
                if (type === 'success') {
                    // NOTIF HIJAU FULL
                    notifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-green-50 border-green-200 text-green-800';

                    notifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-green-100';

                    notifIcon.innerHTML = '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>';

                    notifBar.style.background = '#22c55e'; // hijau tailwind
                } else {
                    notifBox.className = 'relative overflow-hidden flex items-center gap-3 px-4 sm:px-5 py-3 sm:py-3.5 rounded-2xl shadow-lg border bg-red-50 border-red-200 text-red-800';
                    notifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-red-100';
                    notifIcon.innerHTML = '<svg class="w-4.5 h-4.5 text-red-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
                    notifBar.style.background = '#f87171';
                }
                notifText.textContent = message;
                notifBar.style.transition = 'none';
                notifBar.style.width = '100%';
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        notifBar.style.transition = 'width 3.5s linear';
                        notifBar.style.width = '0%';
                    });
                });
                notifTimer = setTimeout(() => hideNotif(), 3500);
            }

            function hideNotif() {
                notifWrap.classList.add('hiding');
                setTimeout(() => {
                    notifWrap.classList.add('hidden');
                    notifWrap.classList.remove('hiding');
                }, 250);
            }

            notifClose.addEventListener('click', () => {
                if (notifTimer) clearTimeout(notifTimer);
                hideNotif();
            });

            @if(session('success'))
                showNotif('{{ session("success") }}', 'success');
            @endif
            @if(session('error'))
                showNotif('{{ session("error") }}', 'error');
            @endif

                // ★ HELPER MODAL ★
                function openModal(m) {
                    m.classList.remove('hidden');
                    m.classList.add('flex');
                }

            function closeModal(m) {
                m.classList.add('hidden');
                m.classList.remove('flex');
            }

            // ★ MODAL TAMBAH ★
            const tambahModal = document.getElementById('tambahBarangModal');
            document.getElementById('openTambahBarang')?.addEventListener('click', () => openModal(tambahModal));
            document.getElementById('closeTambahBarang')?.addEventListener('click', () => closeModal(tambahModal));
            document.getElementById('cancelTambahBarang')?.addEventListener('click', () => closeModal(tambahModal));
            tambahModal?.addEventListener('click', e => {
                if (e.target === tambahModal) closeModal(tambahModal);
            });

            // ★ MODAL EDIT ★
            const editModal = document.getElementById('editBarangModal');
            const editForm = document.getElementById('editBarangForm');
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.editBtn');
                if (!btn) return;
                document.getElementById('editName').value = btn.dataset.name;
                document.getElementById('editCategory').value = btn.dataset.category;
                document.getElementById('editSerial').value = btn.dataset.serial;
                editForm.action = '/data-tools/' + btn.dataset.id;
                openModal(editModal);
            });
            document.getElementById('closeEditModal')?.addEventListener('click', () => closeModal(editModal));
            document.getElementById('cancelEditModal')?.addEventListener('click', () => closeModal(editModal));
            editModal?.addEventListener('click', e => {
                if (e.target === editModal) closeModal(editModal);
            });

            // ★ MODAL PREVIEW ★
            const previewModal = document.getElementById("imagePreviewModal");
            const previewImg = document.getElementById("previewImg");
            document.querySelectorAll(".previewImage").forEach(img => {
                img.addEventListener("click", function () {
                    previewImg.src = this.src;
                    openModal(previewModal);
                });
            });
            document.getElementById("closePreview")?.addEventListener("click", () => closeModal(previewModal));
            previewModal?.addEventListener('click', e => {
                if (e.target === previewModal) closeModal(previewModal);
            });

            // ★ CLEAR SEARCH ★
            document.getElementById('clearSearch')?.addEventListener('click', function () {
                document.getElementById('searchInput').value = '';
                window.location.href = "{{ route('tools.index') }}";
            });

            // ★ ESC KEY ★
            document.addEventListener('keydown', function (e) {
                if (e.key === "Escape") {
                    closeModal(tambahModal);
                    closeModal(editModal);
                    closeModal(previewModal);
                }
            });

            // ★ AUTO OPEN MODAL KALAU ADA ERROR ★
            @php $hasErrors = $errors->any();
            @endphp
            @if($hasErrors)
                openModal(tambahModal);
            @endif

            // DELETE MODAL
            let deleteForm = null;

            const deleteModal = document.getElementById('deleteModal');
            const confirmDelete = document.getElementById('confirmDelete');
            const cancelDelete = document.getElementById('cancelDelete');

            document.querySelectorAll('.deleteForm').forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    deleteForm = this;

                    deleteModal.classList.remove('hidden');
                    deleteModal.classList.add('flex');
                });
            });

            confirmDelete.addEventListener('click', function () {
                if (deleteForm) deleteForm.submit();
            });

            cancelDelete.addEventListener('click', function () {
                deleteModal.classList.add('hidden');
                deleteModal.classList.remove('flex');
            });
        });
    </script>

@endsection