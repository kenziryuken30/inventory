@extends('layouts.app')

@section('content')

    <div class="px-8 pt-6 pb-10">


        {{-- ================= HEADER ================= --}}
        <div class="mb-6">
            <div>
                <h1 class="text-3xl font-bold text-[#1CA7B6] tracking-wide">
                    Data Consumable
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola data Consumable
                </p>
            </div>
        </div>

        {{-- ================= NOTIF TOAST ================= --}}
        <div id="notifWrap" class="hidden mb-5">
            <div id="notifBox"
                class="relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border">
                <div id="notifIcon" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"></div>
                <p id="notifText" class="text-sm font-medium"></p>
                <button id="notifClose" class="ml-auto flex-shrink-0 opacity-50 hover:opacity-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div id="notifBar" class="absolute bottom-0 left-0 h-1 rounded-b-2xl" style="width:100%"></div>
            </div>
        </div>

        {{-- ================= SEARCH + TAMBAH ================= --}}
        <div class="mb-5">
            <div class="bg-gradient-to-b from-[#7ED6DF] to-[#1CA7B6] p-4 rounded-2xl shadow-lg">

                <div class="flex gap-3 items-center">

                    {{-- SEARCH INPUT --}}
                    <div class="relative flex-1">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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

                    {{-- BUTTON TAMBAH --}}
                    <button type="button" id="openTambahConsumable"
                        class="px-5 py-2.5 text-sm bg-white text-[#1CA7B6] font-semibold rounded-xl shadow hover:bg-gray-100 transition whitespace-nowrap">
                        + Tambah Consumable
                    </button>

                </div>
            </div>
        </div>

        {{-- ================= TABEL ================= --}}
        <div class="rounded-2xl shadow-lg overflow-hidden bg-white border border-gray-100">

            <table class="w-full text-sm">

                <thead>
                    <tr class="text-white text-xs uppercase tracking-wider"
                        style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                        <th class="py-4 px-4 text-center w-28 align-middle">Foto</th>
                        <th class="py-4 px-4 text-left align-middle">Nama Barang</th>
                        <th class="py-4 px-4 text-center align-middle">Kategori</th>
                        <th class="py-4 px-4 text-center align-middle">Stok Tersedia</th>
                        <th class="py-4 px-4 text-center w-28 align-middle">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700 text-sm">

                    @forelse($consumables as $c)

                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">

                            {{-- FOTO --}}
                            <td class="py-4 px-4 text-center align-middle">
                                <div class="flex justify-center">
                                    <img src="{{ $c->image ? asset('storage/' . $c->image) : asset('images/no-image.png') }}"
                                        class="consumableImg w-12 h-12 object-contain rounded-lg border border-gray-100 bg-white p-1 cursor-pointer hover:scale-105 transition shadow-sm"
                                        onerror="this.src='{{ asset('images/no-image.png') }}'">
                                </div>
                            </td>

                            {{-- NAMA --}}
                            <td class="py-4 px-4 font-medium text-gray-800 align-middle">
                                {{ $c->name }}
                            </td>

                            {{-- KATEGORI --}}
                            <td class="py-4 px-4 text-center align-middle">
                                <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-600 border border-gray-200">
                                    {{ optional($c->category)->category_name ?? '-' }}
                                </span>
                            </td>

                            {{-- STOK --}}
                            <td class="py-4 px-4 text-center align-middle">
                                <div
                                    class="font-semibold {{ $c->stock < $c->minimum_stock ? 'text-red-600' : 'text-gray-800' }}">
                                    {{ $c->stock }}
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $c->unit }}
                                </div>
                            </td>

                            {{-- AKSI --}}
                            <td class="py-4 px-4 text-center align-middle">
                                <div class="flex justify-center gap-3">

                                    {{-- EDIT --}}
                                    <button type="button"
                                        class="editConsumableBtn p-2 rounded-lg text-gray-400 hover:text-blue-500 hover:bg-blue-50 transition"
                                        data-id="{{ $c->id }}" data-name="{{ $c->name }}" data-stock="{{ $c->stock }}"
                                        data-minimum_stock="{{ $c->minimum_stock }}" data-unit="{{ $c->unit }}"
                                        data-category_id="{{ $c->category_id }}" title="Edit">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.8"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                        </svg>
                                    </button>

                                    {{-- HAPUS --}}
                                    <button type="button"
                                        class="deleteConsumableBtn p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition"
                                        data-id="{{ $c->id }}" data-name="{{ $c->name }}" title="Hapus">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.8"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>

                                    {{-- RESTOCK --}}
                                    <button type="button"
                                        class="restockConsumableBtn p-2 rounded-lg text-gray-400 hover:text-green-500 hover:bg-green-50 transition"
                                        data-id="{{ $c->id }}" data-name="{{ $c->name }}" title="Restock">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.8"
                                            viewBox="0 0 24 24">
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
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                        </path>
                                    </svg>
                                    <span>Data belum tersedia</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>


        {{-- ================= MODAL TAMBAH ================= --}}
        <div id="tambahConsumableModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[1000]">
            <div
                class="consumable-modal-box w-11/12 max-w-md bg-[#efefef] rounded-2xl shadow-[0_15px_40px_rgba(0,0,0,0.25)] p-6 sm:p-8">

                <form method="POST" action="/consumable" enctype="multipart/form-data">
                    @csrf

                    <div class="flex justify-between items-center mb-4">
                        <h2 class="consumable-modal-title text-lg font-semibold">Tambah Consumable</h2>
                        <button type="button" id="closeTambahConsumable"
                            class="text-gray-500 hover:text-gray-700 text-xl transition hover:scale-110">✕</button>
                    </div>

                    <input name="name" placeholder="Nama Barang" class="consumable-input" required>
                    <input name="stock" type="number" placeholder="Stok" class="consumable-input" required>
                    <input name="minimum_stock" type="number" placeholder="Minimum Stok" class="consumable-input">

                    <select name="unit" class="consumable-input" required>
                        <option value="">-- Pilih Unit --</option>
                        <option value="pcs">Pcs</option>
                        <option value="box">Box</option>
                        <option value="pack">Pack</option>
                        <option value="meter">Meter</option>
                        <option value="liter">Liter</option>
                        <option value="botol">Botol</option>
                    </select>

                    <select name="category_id" class="consumable-input">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                        @endforeach
                    </select>

                    <input type="file" name="image" class="consumable-input">

                    <div class="flex justify-end gap-3 mt-5">
                        <button type="button" id="cancelTambahConsumable" class="consumable-btn-cancel">Batal</button>
                        <button type="submit" class="consumable-btn-submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL EDIT ================= --}}
        <div id="editConsumableModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[1000]">
            <div
                class="consumable-modal-box w-11/12 max-w-md bg-[#efefef] rounded-2xl shadow-[0_15px_40px_rgba(0,0,0,0.25)] p-6 sm:p-8">

                <form id="editConsumableForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="flex justify-between items-center mb-4">
                        <h2 class="consumable-modal-title text-lg font-semibold">Edit Consumable</h2>
                        <button type="button" id="closeEditConsumable"
                            class="text-gray-500 hover:text-gray-700 text-xl transition hover:scale-110">✕</button>
                    </div>

                    <input name="name" id="editName" class="consumable-input" required>
                    <input name="stock" type="number" id="editStock" class="consumable-input" required>
                    <input name="minimum_stock" type="number" id="editMinimumStock" class="consumable-input">

                    <select name="unit" id="editUnit" class="consumable-input">
                        <option value="pcs">Pcs</option>
                        <option value="box">Box</option>
                        <option value="pack">Pack</option>
                        <option value="meter">Meter</option>
                        <option value="liter">Liter</option>
                        <option value="botol">Botol</option>
                    </select>

                    <select name="category_id" id="editCategoryId" class="consumable-input">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                        @endforeach
                    </select>

                    <input type="file" name="image" class="consumable-input">

                    <div class="flex justify-end gap-3 mt-5">
                        <button type="button" id="cancelEditConsumable" class="consumable-btn-cancel">Batal</button>
                        <button type="submit" class="consumable-btn-submit">Update</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL RESTOCK ================= --}}
        <div id="restockConsumableModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[1000]">
            <div
                class="consumable-modal-box w-11/12 max-w-sm bg-[#efefef] rounded-2xl shadow-[0_15px_40px_rgba(0,0,0,0.25)] p-6 sm:p-8">

                <form id="restockConsumableForm" method="POST">
                    @csrf

                    <div class="flex justify-between items-center mb-4">
                        <h2 class="consumable-modal-title text-lg font-semibold">Tambah Stok</h2>
                        <button type="button" id="closeRestockConsumable"
                            class="text-gray-500 hover:text-gray-700 text-xl transition hover:scale-110">✕</button>
                    </div>

                    <div class="mb-3 text-sm text-gray-600">
                        Barang: <span id="restockItemName" class="font-semibold text-gray-800"></span>
                    </div>

                    <input type="number" name="qty" id="restockQty" placeholder="Jumlah tambah stok"
                        class="consumable-input" required>

                    <div class="flex justify-end gap-3 mt-5">
                        <button type="button" id="cancelRestockConsumable" class="consumable-btn-cancel">Batal</button>
                        <button type="submit" class="consumable-btn-submit">Tambah</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL KONFIRMASI HAPUS ================= --}}
        <div id="deleteConsumableModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[1001]">
            <div
                class="consumable-modal-box w-11/12 max-w-sm bg-white rounded-2xl shadow-[0_15px_40px_rgba(0,0,0,0.25)] p-6 text-center">

                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </div>
                </div>

                <h3 class="text-lg font-bold text-gray-800 mb-2">Hapus Barang?</h3>
                <p class="text-sm text-gray-500 mb-1">Anda yakin ingin menghapus</p>
                <p id="deleteItemName" class="text-sm font-semibold text-gray-800 mb-6"></p>

                <div class="flex gap-3">
                    <button id="cancelDeleteConsumable"
                        class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">
                        Batal
                    </button>
                    <form id="deleteConsumableForm" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full px-4 py-2.5 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 transition">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================= MODAL PREVIEW FOTO ================= --}}
        <div id="imagePreviewConsumableModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-[999]">
            <div class="relative">
                <button id="closePreviewConsumable"
                    class="absolute -top-10 right-0 text-white text-3xl hover:scale-110 transition">✕</button>
                <img id="previewConsumableImg"
                    class="max-w-[500px] max-h-[400px] object-contain rounded-xl shadow-2xl bg-white p-6">
            </div>
        </div>

    </div>

    {{-- ================= STYLE ================= --}}
    <style>
        /* NOTIF */
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

        /* MODAL */
        .consumable-modal-box {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(180deg, #f7f7f7 0%, #eeeeee 100%) !important;
            border-radius: 22px !important;
            animation: consModalIn 0.22s ease-out;
        }

        @keyframes consModalIn {
            from {
                opacity: 0;
                transform: translateY(-14px) scale(0.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .consumable-modal-title {
            font-weight: 700;
            color: #1CA7B6;
        }

        .consumable-input {
            width: 100%;
            margin-bottom: .75rem;
            padding: .65rem .9rem;
            border: 1px solid #ccc;
            border-radius: .7rem;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f5f5f5;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .consumable-input:focus {
            outline: none;
            border-color: #3fb2c8;
            box-shadow: 0 0 0 3px rgba(63, 178, 200, 0.15);
        }

        .consumable-input::placeholder {
            color: #9ca3af;
        }

        .consumable-btn-cancel {
            padding: .6rem 1.2rem;
            background: #dcdcdc;
            color: #374151;
            border-radius: .75rem;
            font-weight: 600;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .consumable-btn-cancel:hover {
            background: #c5c5c5;
        }

        .consumable-btn-submit {
            padding: .6rem 1.2rem;
            background: linear-gradient(180deg, #5FD0DF, #1CA7B6);
            color: white;
            border-radius: .75rem;
            font-weight: 600;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(28, 167, 182, 0.3);
        }

        .consumable-btn-submit:hover {
            opacity: .9;
            transform: translateY(-1px);
        }
    </style>

    {{-- ================= SCRIPT ================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ========== NOTIF SYSTEM ==========
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
                    notifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-emerald-50 border-emerald-200 text-emerald-800';
                    notifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-emerald-100';
                    notifIcon.innerHTML = '<svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>';
                    notifBar.style.background = '#34d399';
                } else {
                    notifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-red-50 border-red-200 text-red-800';
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


                // ========== HELPER MODAL ==========
                function openModal(modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            function closeModal(modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }


            // ========== MODAL TAMBAH ==========
            const tambahModal = document.getElementById('tambahConsumableModal');
            document.getElementById('openTambahConsumable')?.addEventListener('click', () => openModal(tambahModal));
            document.getElementById('closeTambahConsumable')?.addEventListener('click', () => closeModal(tambahModal));
            document.getElementById('cancelTambahConsumable')?.addEventListener('click', () => closeModal(tambahModal));
            tambahModal?.addEventListener('click', e => { if (e.target === tambahModal) closeModal(tambahModal); });


            // ========== MODAL EDIT ==========
            const editModal = document.getElementById('editConsumableModal');
            const editForm = document.getElementById('editConsumableForm');

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.editConsumableBtn');
                if (!btn) return;

                document.getElementById('editName').value = btn.dataset.name;
                document.getElementById('editStock').value = btn.dataset.stock;
                document.getElementById('editMinimumStock').value = btn.dataset.minimum_stock;
                document.getElementById('editUnit').value = btn.dataset.unit;
                document.getElementById('editCategoryId').value = btn.dataset.category_id;
                editForm.action = '/consumable/' + btn.dataset.id;

                openModal(editModal);
            });

            document.getElementById('closeEditConsumable')?.addEventListener('click', () => closeModal(editModal));
            document.getElementById('cancelEditConsumable')?.addEventListener('click', () => closeModal(editModal));
            editModal?.addEventListener('click', e => { if (e.target === editModal) closeModal(editModal); });


            // ========== MODAL RESTOCK ==========
            const restockModal = document.getElementById('restockConsumableModal');
            const restockForm = document.getElementById('restockConsumableForm');

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.restockConsumableBtn');
                if (!btn) return;

                document.getElementById('restockItemName').textContent = btn.dataset.name;
                document.getElementById('restockQty').value = '';
                restockForm.action = '/consumable/' + btn.dataset.id + '/restock';

                openModal(restockModal);
            });

            document.getElementById('closeRestockConsumable')?.addEventListener('click', () => closeModal(restockModal));
            document.getElementById('cancelRestockConsumable')?.addEventListener('click', () => closeModal(restockModal));
            restockModal?.addEventListener('click', e => { if (e.target === restockModal) closeModal(restockModal); });


            // ========== MODAL HAPUS ==========
            const deleteModal = document.getElementById('deleteConsumableModal');
            const deleteForm = document.getElementById('deleteConsumableForm');

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.deleteConsumableBtn');
                if (!btn) return;

                document.getElementById('deleteItemName').textContent = btn.dataset.name;
                deleteForm.action = '/consumable/' + btn.dataset.id;

                openModal(deleteModal);
            });

            document.getElementById('cancelDeleteConsumable')?.addEventListener('click', () => closeModal(deleteModal));
            deleteModal?.addEventListener('click', e => { if (e.target === deleteModal) closeModal(deleteModal); });


            // ========== MODAL PREVIEW ==========
            const previewModal = document.getElementById('imagePreviewConsumableModal');
            const previewImg = document.getElementById('previewConsumableImg');

            document.querySelectorAll('.consumableImg').forEach(img => {
                img.addEventListener('click', function () {
                    previewImg.src = this.src;
                    openModal(previewModal);
                });
            });

            document.getElementById('closePreviewConsumable')?.addEventListener('click', () => closeModal(previewModal));
            previewModal?.addEventListener('click', e => { if (e.target === previewModal) closeModal(previewModal); });


            // ========== ESC KEY ==========
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeModal(tambahModal);
                    closeModal(editModal);
                    closeModal(restockModal);
                    closeModal(deleteModal);
                    closeModal(previewModal);
                }
            });

        });
    </script>

@endsection