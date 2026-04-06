@extends('layouts.app')

@section('content')

    <div class="pt-4 sm:pt-6 pb-8 sm:pb-10">

        {{-- ================= HEADER ================= --}}
        <div class="mb-4 sm:mb-6">
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-[#0e7490] tracking-wide leading-tight">
                Data Consumable
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola data Consumable</p>
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

        {{-- ================= SEARCH + TAMBAH ================= --}}
        <div class="mb-4 sm:mb-5">
            <div class="bg-gradient-to-b from-[#7FC4FF] to-[#5EA6FF] p-3 sm:p-4 rounded-2xl shadow-lg">
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 items-stretch sm:items-center">
                    <div class="relative flex-1 w-full sm:w-auto">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" />
                                <path stroke-linecap="round" d="m21 21-4.35-4.35" />
                            </svg>
                        </div>
                        <form method="GET" action="/consumable" class="flex items-center">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang..."
                                class="w-full bg-white rounded-xl shadow-inner pl-10 pr-10 py-2.5 text-sm outline-none">
                            @if(request('search'))
                                <a href="/consumable"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            @endif
                        </form>
                    </div>
                    <button type="button" id="openTambahConsumable"
                        class="w-full sm:w-auto px-5 py-2.5 text-sm bg-white text-gray-700 font-semibold rounded-xl shadow hover:bg-gray-50 transition whitespace-nowrap">
                        + Tambah Consumable
                    </button>
                </div>
            </div>
        </div>

        {{-- ================= TABEL ================= --}}
        <div class="rounded-2xl shadow-lg overflow-x-auto bg-white border border-gray-100">
            <table class="w-full text-sm min-w-[620px]">
                <thead>
                    <tr class="bg-gradient-to-r from-[#7FC4FF] to-[#5EA6FF]">
                        <th class="py-3 sm:py-4 px-3 sm:px-4 text-center text-[10px] sm:text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">Foto</th>
                        <th class="py-3 sm:py-4 px-3 sm:px-4 text-left text-[10px] sm:text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap pl-4 sm:pl-6 align-middle">Nama Barang</th>
                        <th class="py-3 sm:py-4 px-3 sm:px-4 text-center text-[10px] sm:text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap hidden md:table-cell align-middle">Kategori</th>
                        <th class="py-3 sm:py-4 px-3 sm:px-4 text-center text-[10px] sm:text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">Stok</th>
                        <th class="py-3 sm:py-4 px-3 sm:px-4 text-center text-[10px] sm:text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap align-middle">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($consumables as $c)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="py-3 sm:py-4 px-3 sm:px-4 text-center align-middle">
                                <div class="flex justify-center">
                                    <img src="{{ $c->image ? asset('storage/' . $c->image) : asset('images/no-image.png') }}"
                                        class="consumableImg w-10 h-10 sm:w-12 sm:h-12 object-contain rounded-lg border border-gray-100 bg-white p-1 cursor-pointer hover:scale-105 transition shadow-sm"
                                        data-fallback="{{ asset('images/no-image.png') }}">
                                </div>
                            </td>
                            <td class="py-3 sm:py-4 px-3 sm:px-4 font-medium text-gray-800 align-middle whitespace-nowrap">{{ $c->name }}</td>
                            <td class="py-3 sm:py-4 px-3 sm:px-4 text-center align-middle">
                                <span class="inline-block px-2.5 sm:px-3 py-1 text-[11px] sm:text-xs rounded-full bg-gray-100 text-gray-600 border border-gray-200">
                                    {{ optional($c->category)->category_name ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 sm:py-4 px-3 sm:px-4 text-center align-middle whitespace-nowrap">
                                <div class="font-semibold {{ $c->stock < $c->minimum_stock ? 'text-red-600' : 'text-gray-800' }}">{{ $c->stock }}</div>
                                <div class="text-[11px] sm:text-xs text-gray-400 mt-0.5">{{ $c->unit }}</div>
                            </td>
                            <td class="py-3 sm:py-4 px-3 sm:px-4 text-center align-middle">
                                <div class="flex justify-center gap-1.5 sm:gap-3">
                                    <button type="button" class="editConsumableBtn p-1.5 sm:p-2 rounded-lg text-gray-400 hover:text-blue-500 hover:bg-blue-50 transition"
                                        data-id="{{ $c->id }}" data-name="{{ $c->name }}" data-stock="{{ $c->stock }}"
                                        data-minimum_stock="{{ $c->minimum_stock }}" data-unit="{{ $c->unit }}"
                                        data-category_id="{{ $c->category_id }}" title="Edit">
                                        <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                        </svg>
                                    </button>
                                    <button type="button" class="deleteConsumableBtn p-1.5 sm:p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition"
                                        data-id="{{ $c->id }}" data-name="{{ $c->name }}" title="Hapus">
                                        <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                    <button type="button" class="restockConsumableBtn p-1.5 sm:p-2 rounded-lg text-gray-400 hover:text-green-500 hover:bg-green-50 transition"
                                        data-id="{{ $c->id }}" data-name="{{ $c->name }}" title="Restock">
                                        <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-gray-400 bg-white">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <span class="text-xs sm:text-sm">Data belum tersedia</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ================= MODAL TAMBAH ================= --}}
        <div id="tambahConsumableModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[10001] p-3 sm:p-4">
            <div class="w-[calc(100%-1.5rem)] sm:w-11/12 max-w-md bg-gradient-to-b from-[#f7f7f7] to-[#eee] rounded-2xl shadow-2xl max-h-[92vh] flex flex-col">
                <form id="tambahConsumableForm" method="POST" action="/consumable" enctype="multipart/form-data" class="flex flex-col min-h-0" novalidate>
                    @csrf
                    <div class="flex justify-between items-center px-5 sm:px-8 pt-5 sm:pt-6 pb-3 border-b border-gray-200 flex-shrink-0">
                        <h2 class="text-base sm:text-lg font-bold text-[#0e7490]">Tambah Consumable</h2>
                        <button type="button" id="closeTambahConsumable" class="text-gray-500 hover:text-gray-700 text-xl transition hover:scale-110">✕</button>
                    </div>
                    <div class="flex-1 overflow-y-auto min-h-0 px-5 sm:px-8 py-4 space-y-3">

                        {{-- NAMA --}}
                        <div class="relative">
                            <input name="name" id="tambahName" placeholder="Nama Barang"
                                class="w-full px-4 py-3 bg-[#f5f5f5] border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#3fb2c8] focus:ring-2 focus:ring-[#3fb2c8]/15 transition">
                            <div id="tambahNameEmpty" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Nama barang wajib diisi</span>
                            </div>
                            <div id="tambahNameSpace" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Nama tidak boleh hanya spasi</span>
                            </div>
                            <div id="tambahNameError" class="hidden mt-1.5 flex items-center gap-1.5 text-xs text-red-500">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Nama barang sudah ada!</span>
                            </div>
                            <div id="tambahNameOk" class="hidden mt-1.5 flex items-center gap-1.5 text-xs text-emerald-500">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                <span>Nama tersedia</span>
                            </div>
                        </div>

                        {{-- STOK --}}
                        <div class="relative">
                            <input name="stock" type="number" id="tambahStock" placeholder="Stok" min="0"
                                class="w-full px-4 py-3 bg-[#f5f5f5] border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#3fb2c8] focus:ring-2 focus:ring-[#3fb2c8]/15 transition">
                            <div id="tambahStockEmpty" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Stok wajib diisi</span>
                            </div>
                            <div id="tambahStockNegatif" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Stok tidak boleh negatif</span>
                            </div>
                        </div>

                        {{-- MINIMUM STOK --}}
                        <div class="relative">
                            <input name="minimum_stock" type="number" id="tambahMinStock" placeholder="Minimum Stok (opsional)" min="0"
                                class="w-full px-4 py-3 bg-[#f5f5f5] border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#3fb2c8] focus:ring-2 focus:ring-[#3fb2c8]/15 transition">
                            <div id="tambahMinStockNegatif" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Minimum stok tidak boleh negatif</span>
                            </div>
                        </div>

                        {{-- UNIT --}}
                        <div class="relative">
                            <select name="unit" id="tambahUnit"
                                class="w-full px-4 py-3 bg-[#f5f5f5] border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#3fb2c8] focus:ring-2 focus:ring-[#3fb2c8]/15 transition">
                                <option value="">-- Pilih Unit --</option>
                                <option value="pcs">Pcs</option>
                                <option value="box">Box</option>
                                <option value="pack">Pack</option>
                                <option value="meter">Meter</option>
                                <option value="liter">Liter</option>
                                <option value="botol">Botol</option>
                            </select>
                            <div id="tambahUnitEmpty" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Unit wajib dipilih</span>
                            </div>
                        </div>

                        {{-- KATEGORI --}}
                        <div class="relative">
                            <select name="category_id" id="tambahCategory"
                                class="w-full px-4 py-3 bg-[#f5f5f5] border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#3fb2c8] focus:ring-2 focus:ring-[#3fb2c8]/15 transition">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                            <div id="tambahCategoryEmpty" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Kategori wajib dipilih</span>
                            </div>
                        </div>

                        {{-- IMAGE --}}
                        <div class="relative">
                            <input type="file" name="image" id="tambahImage" accept="image/jpeg,image/png,image/webp"
                                class="w-full px-4 py-2 bg-[#e6e6e6] border border-gray-300 rounded-lg text-xs cursor-pointer">
                            <div id="tambahImageError" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Format: JPG, PNG, WebP. Maks 2MB.</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 px-5 sm:px-8 py-4 border-t border-gray-200 flex-shrink-0">
                        <button type="button" id="cancelTambahConsumable" class="px-5 py-2.5 bg-[#dcdcdc] text-gray-700 rounded-xl text-sm font-semibold hover:bg-[#c5c5c5] transition">Batal</button>
                        <button type="submit" id="tambahSubmitBtn" class="px-5 py-2.5 bg-gradient-to-b from-[#7FC4FF] to-[#5EA6FF] text-white rounded-xl text-sm font-semibold shadow-lg hover:opacity-90 transition">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL EDIT ================= --}}
        <div id="editConsumableModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[10001] p-3 sm:p-4">
            <div class="w-[calc(100%-1.5rem)] sm:w-11/12 max-w-md bg-gradient-to-b from-[#f7f7f7] to-[#eee] rounded-2xl shadow-2xl max-h-[92vh] flex flex-col">
                <form id="editConsumableForm" method="POST" enctype="multipart/form-data" class="flex flex-col min-h-0" novalidate>
                    @csrf
                    @method('PUT')
                    <div class="flex justify-between items-center px-5 sm:px-8 pt-5 sm:pt-6 pb-3 border-b border-gray-200 flex-shrink-0">
                        <h2 class="text-base sm:text-lg font-bold text-[#0e7490]">Edit Consumable</h2>
                        <button type="button" id="closeEditConsumable" class="text-gray-500 hover:text-gray-700 text-xl transition hover:scale-110">✕</button>
                    </div>
                    <div class="flex-1 overflow-y-auto min-h-0 px-5 sm:px-8 py-4 space-y-3">

                        {{-- NAMA --}}
                        <div class="relative">
                            <input name="name" id="editName"
                                class="w-full px-4 py-3 bg-[#f5f5f5] border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#3fb2c8] focus:ring-2 focus:ring-[#3fb2c8]/15 transition">
                            <div id="editNameEmpty" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Nama barang wajib diisi</span>
                            </div>
                            <div id="editNameSpace" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Nama tidak boleh hanya spasi</span>
                            </div>
                            <div id="editNameError" class="hidden mt-1.5 flex items-center gap-1.5 text-xs text-red-500">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Nama barang sudah ada!</span>
                            </div>
                            <div id="editNameOk" class="hidden mt-1.5 flex items-center gap-1.5 text-xs text-emerald-500">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                <span>Nama tersedia</span>
                            </div>
                        </div>

                        {{-- STOK --}}
                        <div class="relative">
                            <input name="stock" type="number" id="editStock" min="0"
                                class="w-full px-4 py-3 bg-[#f5f5f5] border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#3fb2c8] focus:ring-2 focus:ring-[#3fb2c8]/15 transition">
                            <div id="editStockEmpty" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Stok wajib diisi</span>
                            </div>
                            <div id="editStockNegatif" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Stok tidak boleh negatif</span>
                            </div>
                        </div>

                        {{-- MINIMUM STOK --}}
                        <div class="relative">
                            <input name="minimum_stock" type="number" id="editMinStock" min="0"
                                class="w-full px-4 py-3 bg-[#f5f5f5] border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#3fb2c8] focus:ring-2 focus:ring-[#3fb2c8]/15 transition">
                            <div id="editMinStockNegatif" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Minimum stok tidak boleh negatif</span>
                            </div>
                        </div>

                        {{-- UNIT --}}
                        <select name="unit" id="editUnit" class="w-full px-4 py-3 bg-[#f5f5f5] border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#3fb2c8] focus:ring-2 focus:ring-[#3fb2c8]/15 transition">
                            <option value="pcs">Pcs</option>
                            <option value="box">Box</option>
                            <option value="pack">Pack</option>
                            <option value="meter">Meter</option>
                            <option value="liter">Liter</option>
                            <option value="botol">Botol</option>
                        </select>

                        {{-- KATEGORI --}}
                        <select name="category_id" id="editCategoryId" class="w-full px-4 py-3 bg-[#f5f5f5] border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#3fb2c8] focus:ring-2 focus:ring-[#3fb2c8]/15 transition">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                            @endforeach
                        </select>

                        {{-- IMAGE --}}
                        <div class="relative">
                            <input type="file" name="image" id="editImage" accept="image/jpeg,image/png,image/webp"
                                class="w-full px-4 py-2 bg-[#e6e6e6] border border-gray-300 rounded-lg text-xs cursor-pointer">
                            <div id="editImageError" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Format: JPG, PNG, WebP. Maks 2MB.</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 px-5 sm:px-8 py-4 border-t border-gray-200 flex-shrink-0">
                        <button type="button" id="cancelEditConsumable" class="px-5 py-2.5 bg-[#dcdcdc] text-gray-700 rounded-xl text-sm font-semibold hover:bg-[#c5c5c5] transition">Batal</button>
                        <button type="submit" id="editSubmitBtn" class="px-5 py-2.5 bg-gradient-to-b from-[#7FC4FF] to-[#5EA6FF] text-white rounded-xl text-sm font-semibold shadow-lg hover:opacity-90 transition">Update</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL RESTOCK ================ --}}
        <div id="restockConsumableModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[10001] p-3 sm:p-4">
            <div class="w-[calc(100%-1.5rem)] sm:w-11/12 max-w-sm bg-gradient-to-b from-[#f7f7f7] to-[#eee] rounded-2xl shadow-2xl">
                <form id="restockConsumableForm" method="POST" novalidate>
                    @csrf
                    <div class="flex justify-between items-center px-5 sm:px-8 pt-5 sm:pt-6 pb-3 border-b border-gray-200">
                        <h2 class="text-base sm:text-lg font-bold text-[#0e7490]">Tambah Stok</h2>
                        <button type="button" id="closeRestockConsumable" class="text-gray-500 hover:text-gray-700 text-xl transition hover:scale-110">✕</button>
                    </div>
                    <div class="px-5 sm:px-8 py-4 space-y-3">
                        <div class="text-xs sm:text-sm text-gray-600">
                            Barang: <span id="restockItemName" class="font-semibold text-gray-800"></span>
                        </div>
                        <div class="relative">
                            <input type="number" name="qty" id="restockQty" placeholder="Jumlah tambah stok" min="1"
                                class="w-full px-4 py-3 bg-[#f5f5f5] border border-gray-300 rounded-xl text-sm focus:outline-none focus:border-[#3fb2c8] focus:ring-2 focus:ring-[#3fb2c8]/15 transition">
                            <div id="restockQtyEmpty" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Jumlah wajib diisi</span>
                            </div>
                            <div id="restockQtyMin" class="hidden mt-1.5 text-xs text-red-500 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Minimal tambah 1</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 px-5 sm:px-8 py-4 border-t border-gray-200">
                        <button type="button" id="cancelRestockConsumable" class="px-5 py-2.5 bg-[#dcdcdc] text-gray-700 rounded-xl text-sm font-semibold hover:bg-[#c5c5c5] transition">Batal</button>
                        <button type="submit" id="restockSubmitBtn" class="px-5 py-2.5 bg-gradient-to-b from-[#7FC4FF] to-[#5EA6FF] text-white rounded-xl text-sm font-semibold shadow-lg hover:opacity-90 transition">Tambah</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL HAPUS ================= --}}
        <div id="deleteConsumableModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[10002] p-3 sm:p-4">
            <div class="w-[calc(100%-1.5rem)] sm:w-11/12 max-w-sm bg-white rounded-2xl shadow-2xl p-5 sm:p-6 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-2">Hapus Barang?</h3>
                <p class="text-xs sm:text-sm text-gray-500 mb-1">Anda yakin ingin menghapus</p>
                <p id="deleteItemName" class="text-xs sm:text-sm font-semibold text-gray-800 mb-5"></p>
                <div class="flex gap-3">
                    <button id="cancelDeleteConsumable" class="flex-1 px-5 py-2.5 bg-[#dcdcdc] text-gray-700 rounded-xl text-xs sm:text-sm font-semibold hover:bg-[#c5c5c5] transition">Batal</button>
                    <form id="deleteConsumableForm" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" id="deleteSubmitBtn" class="w-full px-5 py-2.5 bg-red-500 text-white rounded-xl text-xs sm:text-sm font-semibold hover:bg-red-600 transition">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================= MODAL PREVIEW FOTO ================= --}}
        <div id="imagePreviewConsumableModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-[10001] p-3 sm:p-4">
            <div class="relative">
                <button id="closePreviewConsumable" class="absolute -top-10 right-0 text-white text-3xl hover:scale-110 transition">✕</button>
                <img id="previewConsumableImg" class="max-w-[calc(100vw-1.5rem)] sm:max-w-[500px] max-h-[75vh] object-contain rounded-xl shadow-2xl bg-white p-3 sm:p-6">
            </div>
        </div>

    </div>

    <style>
        #notifWrap { animation: notifSlideIn 0.3s ease-out; }
        @keyframes notifSlideIn { from { opacity: 0; transform: translateY(-12px); } to { opacity: 1; transform: translateY(0); } }
        #notifWrap.hiding { animation: notifSlideOut 0.25s ease-in forwards; }
        @keyframes notifSlideOut { from { opacity: 1; transform: translateY(0); } to { opacity: 0; transform: translateY(-12px); } }
        #notifBar { transition: width 3.5s linear; }

        /* Input error state */
        .input-error { border-color: #f87171 !important; background-color: #fef2f2 !important; }
        .input-ok { border-color: #34d399 !important; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            //  FIX IMAGE ERROR 
            document.querySelectorAll('.consumableImg').forEach(img => {
                img.addEventListener('error', function () {
                    if (this.dataset.errored) return;
                    this.dataset.errored = '1';
                    this.src = this.dataset.fallback || '{{ asset("images/no-image.png") }}';
                });
            });

            //  NOTIF SYSTEM 
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
                    notifBox.className = 'relative overflow-hidden flex items-center gap-3 px-4 sm:px-5 py-3 sm:py-3.5 rounded-2xl shadow-lg border bg-emerald-50 border-emerald-200 text-emerald-800';
                    notifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-emerald-100';
                    notifIcon.innerHTML = '<svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>';
                    notifBar.style.background = '#34d399';
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
                setTimeout(() => { notifWrap.classList.add('hidden'); notifWrap.classList.remove('hiding'); }, 250);
            }

            notifClose.addEventListener('click', () => { if (notifTimer) clearTimeout(notifTimer); hideNotif(); });

            @if(session('success'))
                showNotif('{{ session("success") }}', 'success');
            @endif
            @if(session('error'))
                showNotif('{{ session("error") }}', 'error');
            @endif

            //  HELPER MODAL 
            function openModal(m) { m.classList.remove('hidden'); m.classList.add('flex'); }
            function closeModal(m) { m.classList.add('hidden'); m.classList.remove('flex'); }

            //  HELPER VALIDASI 
            function showErr(divId, inputId) {
                document.getElementById(divId)?.classList.remove('hidden');
                if (inputId) document.getElementById(inputId)?.classList.add('input-error');
            }
            function hideErr(divId, inputId) {
                document.getElementById(divId)?.classList.add('hidden');
                if (inputId) document.getElementById(inputId)?.classList.remove('input-error');
            }
            function resetInput(inputId, ...errIds) {
                const el = document.getElementById(inputId);
                el?.classList.remove('input-error', 'input-ok');
                errIds.forEach(id => document.getElementById(id)?.classList.add('hidden'));
            }
            function validateImage(fileInputId, errorDivId) {
                const file = document.getElementById(fileInputId).files[0];
                if (!file) return true;
                const allowed = ['image/jpeg', 'image/png', 'image/webp'];
                if (!allowed.includes(file.type)) { showErr(errorDivId, fileInputId); return false; }
                if (file.size > 2 * 1024 * 1024) { showErr(errorDivId, fileInputId); return false; }
                hideErr(errorDivId, fileInputId);
                return true;
            }

            //  VALIDASI NAMA DUPLIKAT 
            let debounceTimer = null;

            async function checkNameDuplicate(inputId, errId, okId, excludeId = null) {
                const input = document.getElementById(inputId);
                const name = input.value.trim();

                hideErr(errId, inputId);
                document.getElementById(okId)?.classList.add('hidden');
                input.classList.remove('input-ok');

                if (name.length === 0) return true;

                if (debounceTimer) clearTimeout(debounceTimer);
                return new Promise(resolve => {
                    debounceTimer = setTimeout(async () => {
                        try {
                            let url = `/consumable/check-name?name=${encodeURIComponent(name)}`;
                            if (excludeId) url += `&id=${excludeId}`;
                            const res = await fetch(url);
                            const data = await res.json();
                            if (data.exists) {
                                showErr(errId, inputId);
                                input.classList.remove('input-ok');
                                resolve(false);
                            } else {
                                hideErr(errId, inputId);
                                input.classList.add('input-ok');
                                document.getElementById(okId)?.classList.remove('hidden');
                                resolve(true);
                            }
                        } catch (e) { resolve(true); }
                    }, 400);
                });
            }

            //  DEKLARASI ELEMEN 
            const tambahModal = document.getElementById('tambahConsumableModal');
            const tambahForm = document.getElementById('tambahConsumableForm');
            const tambahSubmitBtn = document.getElementById('tambahSubmitBtn');

            const editModal = document.getElementById('editConsumableModal');
            const editForm = document.getElementById('editConsumableForm');
            const editSubmitBtn = document.getElementById('editSubmitBtn');
            let currentEditId = null;
            let editNameValid = true;

            const restockModal = document.getElementById('restockConsumableModal');
            const restockForm = document.getElementById('restockConsumableForm');
            const restockSubmitBtn = document.getElementById('restockSubmitBtn');

            const deleteModal = document.getElementById('deleteConsumableModal');
            const deleteForm = document.getElementById('deleteConsumableForm');
            const deleteSubmitBtn = document.getElementById('deleteSubmitBtn');

            const previewModal = document.getElementById('imagePreviewConsumableModal');
            const previewImg = document.getElementById('previewConsumableImg');

            //  MODAL TAMBAH
            document.getElementById('openTambahConsumable')?.addEventListener('click', () => {
                openModal(tambahModal);
                tambahForm.reset();
                // Reset semua error & ok
                ['tambahName', 'tambahStock', 'tambahMinStock', 'tambahUnit', 'tambahCategory', 'tambahImage'].forEach(id => {
                    const el = document.getElementById(id);
                    el?.classList.remove('input-error', 'input-ok');
                });
                ['tambahNameEmpty','tambahNameSpace','tambahNameError','tambahNameOk','tambahStockEmpty','tambahStockNegatif','tambahMinStockNegatif','tambahUnitEmpty','tambahCategoryEmpty','tambahImageError'].forEach(id => {
                    document.getElementById(id)?.classList.add('hidden');
                });
                tambahSubmitBtn.disabled = false;
                tambahSubmitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            });

            document.getElementById('closeTambahConsumable')?.addEventListener('click', () => closeModal(tambahModal));
            document.getElementById('cancelTambahConsumable')?.addEventListener('click', () => closeModal(tambahModal));
            tambahModal?.addEventListener('click', e => { if (e.target === tambahModal) closeModal(tambahModal); });

            // Real-time validasi nama tambah
            document.getElementById('tambahName')?.addEventListener('input', function () {
                const v = this.value;
                resetInput('tambahName', 'tambahNameEmpty', 'tambahNameSpace', 'tambahNameError', 'tambahNameOk');
                if (v.length === 0) return;
                if (v.trim().length === 0) { showErr('tambahNameSpace', 'tambahName'); return; }
                checkNameDuplicate('tambahName', 'tambahNameError', 'tambahNameOk');
            });

            // Real-time validasi stok tambah
            document.getElementById('tambahStock')?.addEventListener('input', function () {
                resetInput('tambahStock', 'tambahStockEmpty', 'tambahStockNegatif');
                if (this.value === '') return;
                if (parseInt(this.value) < 0) showErr('tambahStockNegatif', 'tambahStock');
            });

            // Real-time validasi min stok tambah
            document.getElementById('tambahMinStock')?.addEventListener('input', function () {
                resetInput('tambahMinStock', 'tambahMinStockNegatif');
                if (this.value === '') return;
                if (parseInt(this.value) < 0) showErr('tambahMinStockNegatif', 'tambahMinStock');
            });

            // Real-time validasi image tambah
            document.getElementById('tambahImage')?.addEventListener('change', () => {
                hideErr('tambahImage', 'tambahImage');
                if (document.getElementById('tambahImage').files[0]) validateImage('tambahImage', 'tambahImageError');
            });

            // Submit tambah
            tambahForm?.addEventListener('submit', function (e) {
                let hasError = false;
                const name = document.getElementById('tambahName').value;
                const stock = document.getElementById('tambahStock').value;
                const minStock = document.getElementById('tambahMinStock').value;
                const unit = document.getElementById('tambahUnit').value;
                const category = document.getElementById('tambahCategory').value;

                // Reset dulu
                ['tambahName','tambahStock','tambahMinStock','tambahUnit','tambahCategory','tambahImage'].forEach(id => {
                    document.getElementById(id)?.classList.remove('input-error');
                });
                ['tambahNameEmpty','tambahNameSpace','tambahStockEmpty','tambahStockNegatif','tambahMinStockNegatif','tambahUnitEmpty','tambahCategoryEmpty','tambahImageError'].forEach(id => {
                    document.getElementById(id)?.classList.add('hidden');
                });

                // Validasi nama
                if (name.trim().length === 0 && name.length > 0) {
                    showErr('tambahNameSpace', 'tambahName'); hasError = true;
                } else if (name.trim().length === 0) {
                    showErr('tambahNameEmpty', 'tambahName'); hasError = true;
                } else if (!document.getElementById('tambahNameError').classList.contains('hidden')) {
                    hasError = true;
                }

                // Validasi stok
                if (stock === '') { showErr('tambahStockEmpty', 'tambahStock'); hasError = true; }
                else if (parseInt(stock) < 0) { showErr('tambahStockNegatif', 'tambahStock'); hasError = true; }

                // Validasi min stok
                if (minStock !== '' && parseInt(minStock) < 0) { showErr('tambahMinStockNegatif', 'tambahMinStock'); hasError = true; }

                // Validasi unit
                if (!unit) { showErr('tambahUnitEmpty', 'tambahUnit'); hasError = true; }

                // Validasi kategori
                if (!category) { showErr('tambahCategoryEmpty', 'tambahCategory'); hasError = true; }

                // Validasi image
                if (document.getElementById('tambahImage').files[0]) {
                    if (!validateImage('tambahImage', 'tambahImageError')) hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                    showNotif('Lengkapi form dengan benar', 'error');
                    return;
                }

                tambahSubmitBtn.disabled = true;
                tambahSubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            });

            //  MODAL EDIT 
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.editConsumableBtn');
                if (!btn) return;
                currentEditId = btn.dataset.id;
                editNameValid = true;

                document.getElementById('editName').value = btn.dataset.name;
                document.getElementById('editStock').value = btn.dataset.stock;
                document.getElementById('editMinStock').value = btn.dataset.minimum_stock;
                document.getElementById('editUnit').value = btn.dataset.unit;
                document.getElementById('editCategoryId').value = btn.dataset.category_id;
                editForm.action = '/consumable/' + btn.dataset.id;

                // Reset semua error
                ['editName','editStock','editMinStock','editImage'].forEach(id => {
                    document.getElementById(id)?.classList.remove('input-error', 'input-ok');
                });
                ['editNameEmpty','editNameSpace','editNameError','editNameOk','editStockEmpty','editStockNegatif','editMinStockNegatif','editImageError'].forEach(id => {
                    document.getElementById(id)?.classList.add('hidden');
                });
                editSubmitBtn.disabled = false;
                editSubmitBtn.classList.remove('opacity-50', 'cursor-not-allowed');

                openModal(editModal);
            });

            document.getElementById('closeEditConsumable')?.addEventListener('click', () => closeModal(editModal));
            document.getElementById('cancelEditConsumable')?.addEventListener('click', () => closeModal(editModal));
            editModal?.addEventListener('click', e => { if (e.target === editModal) closeModal(editModal); });

            // Real-time validasi nama edit
            document.getElementById('editName')?.addEventListener('input', async function () {
                const v = this.value;
                resetInput('editName', 'editNameEmpty', 'editNameSpace', 'editNameError', 'editNameOk');
                if (v.length === 0) return;
                if (v.trim().length === 0) { showErr('editNameSpace', 'editName'); editNameValid = false; return; }
                editNameValid = await checkNameDuplicate('editName', 'editNameError', 'editNameOk', currentEditId);
            });

            // Real-time validasi stok edit
            document.getElementById('editStock')?.addEventListener('input', function () {
                resetInput('editStock', 'editStockEmpty', 'editStockNegatif');
                if (this.value === '') return;
                if (parseInt(this.value) < 0) showErr('editStockNegatif', 'editStock');
            });

            // Real-time validasi min stok edit
            document.getElementById('editMinStock')?.addEventListener('input', function () {
                resetInput('editMinStock', 'editMinStockNegatif');
                if (this.value === '') return;
                if (parseInt(this.value) < 0) showErr('editMinStockNegatif', 'editMinStock');
            });

            // Real-time validasi image edit
            document.getElementById('editImage')?.addEventListener('change', () => {
                hideErr('editImage', 'editImage');
                if (document.getElementById('editImage').files[0]) validateImage('editImage', 'editImageError');
            });

            // Submit edit
            editForm?.addEventListener('submit', function (e) {
                let hasError = false;
                const name = document.getElementById('editName').value;
                const stock = document.getElementById('editStock').value;
                const minStock = document.getElementById('editMinStock').value;

                ['editName','editStock','editMinStock','editImage'].forEach(id => {
                    document.getElementById(id)?.classList.remove('input-error');
                });
                ['editNameEmpty','editNameSpace','editStockEmpty','editStockNegatif','editMinStockNegatif','editImageError'].forEach(id => {
                    document.getElementById(id)?.classList.add('hidden');
                });

                if (name.trim().length === 0 && name.length > 0) {
                    showErr('editNameSpace', 'editName'); hasError = true;
                } else if (name.trim().length === 0) {
                    showErr('editNameEmpty', 'editName'); hasError = true;
                } else if (!editNameValid) {
                    hasError = true;
                }

                if (stock === '') { showErr('editStockEmpty', 'editStock'); hasError = true; }
                else if (parseInt(stock) < 0) { showErr('editStockNegatif', 'editStock'); hasError = true; }

                if (minStock !== '' && parseInt(minStock) < 0) { showErr('editMinStockNegatif', 'editMinStock'); hasError = true; }

                if (document.getElementById('editImage').files[0]) {
                    if (!validateImage('editImage', 'editImageError')) hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                    showNotif('Lengkapi form dengan benar', 'error');
                    return;
                }

                editSubmitBtn.disabled = true;
                editSubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            });

            //  MODAL RESTOCK 
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.restockConsumableBtn');
                if (!btn) return;
                document.getElementById('restockItemName').textContent = btn.dataset.name;
                document.getElementById('restockQty').value = '';
                restockForm.action = '/consumable/' + btn.dataset.id + '/restock';
                resetInput('restockQty', 'restockQtyEmpty', 'restockQtyMin');
                restockSubmitBtn.disabled = false;
                restockSubmitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                openModal(restockModal);
            });

            document.getElementById('closeRestockConsumable')?.addEventListener('click', () => closeModal(restockModal));
            document.getElementById('cancelRestockConsumable')?.addEventListener('click', () => closeModal(restockModal));
            restockModal?.addEventListener('click', e => { if (e.target === restockModal) closeModal(restockModal); });

            document.getElementById('restockQty')?.addEventListener('input', function () {
                resetInput('restockQty', 'restockQtyEmpty', 'restockQtyMin');
                if (this.value === '') return;
                if (parseInt(this.value) < 1) showErr('restockQtyMin', 'restockQty');
            });

            restockForm?.addEventListener('submit', function (e) {
                let hasError = false;
                const qty = document.getElementById('restockQty').value;

                resetInput('restockQty', 'restockQtyEmpty', 'restockQtyMin');

                if (qty === '') { showErr('restockQtyEmpty', 'restockQty'); hasError = true; }
                else if (parseInt(qty) < 1) { showErr('restockQtyMin', 'restockQty'); hasError = true; }

                if (hasError) {
                    e.preventDefault();
                    showNotif('Masukkan jumlah yang valid', 'error');
                    return;
                }

                restockSubmitBtn.disabled = true;
                restockSubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            });

            //  MODAL HAPUS 
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.deleteConsumableBtn');
                if (!btn) return;
                document.getElementById('deleteItemName').textContent = btn.dataset.name;
                deleteForm.action = '/consumable/' + btn.dataset.id;
                deleteSubmitBtn.disabled = false;
                deleteSubmitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                openModal(deleteModal);
            });

            document.getElementById('cancelDeleteConsumable')?.addEventListener('click', () => closeModal(deleteModal));
            deleteModal?.addEventListener('click', e => { if (e.target === deleteModal) closeModal(deleteModal); });

            deleteForm?.addEventListener('submit', function () {
                deleteSubmitBtn.disabled = true;
                deleteSubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            });

            //  MODAL PREVIEW 
            document.querySelectorAll('.consumableImg').forEach(img => {
                img.addEventListener('click', function () { previewImg.src = this.src; openModal(previewModal); });
            });
            document.getElementById('closePreviewConsumable')?.addEventListener('click', () => closeModal(previewModal));
            previewModal?.addEventListener('click', e => { if (e.target === previewModal) closeModal(previewModal); });

            //  ESC KEY 
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeModal(tambahModal); closeModal(editModal); closeModal(restockModal);
                    closeModal(deleteModal); closeModal(previewModal);
                }
            });
        });
    </script>

@endsection