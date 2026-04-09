@extends('layouts.app')

@section('content')

    <div class="px-8 pt-6 pb-10">

        {{-- ================= HEADER ================= --}}
        <div class="mb-4 sm:mb-5">
            <h1 class="text-3xl font-bold text-[#113561] tracking-wide">Kategori</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar dan Input Kategori</p>
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

        {{-- ================= SEARCH + TOMBOL TAMBAH (UPDATED) ================= --}}
        <div class="bg-gradient-to-b from-[#7FC4FF] to-[#5EA6FF] p-3 sm:p-5 rounded-2xl shadow-md mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row gap-2.5 sm:gap-3 items-stretch sm:items-center">
                
                {{-- FORM SEARCH --}}
                <form method="GET" action="{{ route('categories.index') }}"
                    class="flex flex-col sm:flex-row gap-2.5 sm:gap-3 flex-1 w-full sm:w-auto">
                    
                    <div class="relative flex-1">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8" />
                                <path stroke-linecap="round" d="m21 21-4.35-4.35" />
                            </svg>
                        </div>
                        {{-- Input Style disesuaikan agar tingginya sama dengan tombol --}}
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori..."
                            class="w-full h-10 sm:h-[42px] bg-white rounded-xl pl-10 pr-10 text-sm font-medium text-gray-700 shadow-sm outline-none focus:ring-2 focus:ring-[#5EA6FF]/20 focus:border-[#5EA6FF] transition">
                        
                        @if(request('search'))
                            <a href="{{ route('categories.index') }}"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </div>

                </form>

                {{-- TOMBOL TAMBAH (DIPINDAHKAN KESINI) --}}
                <button type="button" id="openTambahKategori"
                    class="group inline-flex items-center justify-center px-5 h-10 sm:h-[42px] rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all duration-200 tracking-wide border-2 border-[#5EA6FF] bg-white text-sm text-[#5EA6FF] hover:bg-[#5EA6FF] hover:text-white hover:shadow-blue-500/40 hover:-translate-y-0.5 whitespace-nowrap">
                    
                    <svg class="w-4 h-4 mr-2 transition-transform duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    
                    Tambah Kategori
                </button>

            </div>
        </div>

        {{-- ================= TABEL ================= --}}
        <div class="rounded-2xl shadow-lg overflow-hidden bg-white border border-gray-100">

            <table class="w-full text-sm" style="table-layout: fixed;">

                <thead>
                    <tr class="text-white text-xs uppercase tracking-wider"
                        style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">
                        <th class="py-3 px-4 text-center" style="width: 12%;">No</th>
                        <th class="py-3 px-4 text-center" style="width: 56%;">Kategori</th>
                        <th class="py-3 px-4 text-center" style="width: 32%;">Aksi</th>
                    </tr>
                </thead>

                <tbody class="text-gray-700 text-sm">

                    @forelse($categories as $cat)

                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="py-3.5 px-4 text-center font-medium text-gray-600">{{ $categories->firstItem() + $loop->index }}</td>
                            <td class="py-3.5 px-4 font-medium text-gray-800 text-center">{{ $cat->category_name }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <button type="button"
                                        class="editKategoriBtn p-2 rounded-lg text-gray-400 hover:text-blue-500 hover:bg-blue-50 transition"
                                        data-id="{{ $cat->id }}" data-name="{{ $cat->category_name }}" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                        </svg>
                                    </button>
                                    <button type="button"
                                        class="deleteKategoriBtn p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition"
                                        data-id="{{ $cat->id }}" data-name="{{ $cat->category_name }}" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-10 text-center text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                        </path>
                                    </svg>
                                    <span>Belum ada kategori</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- ================= PAGINATION ================= --}}
        <div class="mt-5 flex justify-center">
            {{ $categories->appends(request()->query())->links() }}
        </div>


        {{-- ================= MODAL TAMBAH ================= --}}
        <div id="tambahKategoriModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[1000]">
            <div class="kategori-modal-box w-11/12 max-w-md bg-[#efefef] rounded-2xl shadow-[0_15px_40px_rgba(0,0,0,0.25)] p-6 sm:p-8">
                <form id="tambahKategoriForm" method="POST" action="{{ route('categories.store') }}">
                    @csrf
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="kategori-modal-title text-lg font-semibold">Tambah Kategori</h2>
                        <button type="button" id="closeTambahKategori"
                            class="text-gray-500 hover:text-gray-700 text-xl transition hover:scale-110">✕</button>
                    </div>
                    <input name="category_name" placeholder="Nama Kategori" class="kategori-input" required>
                    <div class="flex justify-end gap-3 mt-5">
                        <button type="button" id="cancelTambahKategori" class="kategori-btn-cancel">Batal</button>
                        <button type="submit" id="tambahSubmitBtn" class="kategori-btn-submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL EDIT ================= --}}
        <div id="editKategoriModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[1000]">
            <div class="kategori-modal-box w-11/12 max-w-md bg-[#efefef] rounded-2xl shadow-[0_15px_40px_rgba(0,0,0,0.25)] p-6 sm:p-8">
                <form id="editKategoriForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="kategori-modal-title text-lg font-semibold">Edit Kategori</h2>
                        <button type="button" id="closeEditKategori"
                            class="text-gray-500 hover:text-gray-700 text-xl transition hover:scale-110">✕</button>
                    </div>
                    <input name="category_name" id="editKategoriName" placeholder="Nama Kategori" class="kategori-input" required>
                    <div class="flex justify-end gap-3 mt-5">
                        <button type="button" id="cancelEditKategori" class="kategori-btn-cancel">Batal</button>
                        <button type="submit" id="editSubmitBtn" class="kategori-btn-submit">Update</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ================= MODAL HAPUS ================= --}}
        <div id="deleteKategoriModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[1001]">
            <div class="kategori-modal-box w-11/12 max-w-sm bg-white rounded-2xl shadow-[0_15px_40px_rgba(0,0,0,0.25)] p-6 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Hapus Kategori?</h3>
                <p class="text-sm text-gray-500 mb-1">Anda yakin ingin menghapus</p>
                <p id="deleteKategoriName" class="text-sm font-semibold text-gray-800 mb-6"></p>
                <div class="flex gap-3">
                    <button id="cancelDeleteKategori"
                        class="flex-1 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">Batal</button>
                    <form id="deleteKategoriForm" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" id="deleteSubmitBtn"
                            class="w-full px-4 py-2.5 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 transition">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    {{-- ================= STYLE ================= --}}
    <style>
        #notifWrap { animation: notifSlideIn 0.3s ease-out; }
        @keyframes notifSlideIn { from { opacity:0; transform:translateY(-12px); } to { opacity:1; transform:translateY(0); } }
        #notifWrap.hiding { animation: notifSlideOut 0.25s ease-in forwards; }
        @keyframes notifSlideOut { from { opacity:1; transform:translateY(0); } to { opacity:0; transform:translateY(-12px); } }
        #notifBar { transition: width 3.5s linear; }

        .kategori-modal-box {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(180deg, #f7f7f7 0%, #eeeeee 100%) !important;
            border-radius: 22px !important;
            animation: katModalIn 0.22s ease-out;
        }
        @keyframes katModalIn { from { opacity:0; transform:translateY(-14px) scale(0.97); } to { opacity:1; transform:translateY(0) scale(1); } }
        
        .kategori-modal-title { font-weight: 700; color: #5EA6FF; } 
        
        .kategori-input {
            width: 100%; margin-bottom: .75rem; padding: .65rem .9rem;
            border: 1px solid #ccc; border-radius: .7rem; font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif; background: #f5f5f5;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .kategori-input:focus { outline: none; border-color: #5EA6FF; box-shadow: 0 0 0 3px rgba(94,166,255,0.15); }
        .kategori-input::placeholder { color: #9ca3af; }
        
        .kategori-btn-cancel {
            padding: .6rem 1.2rem; background: #dcdcdc; color: #374151;
            border-radius: .75rem; font-weight: 600; font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif; transition: all 0.2s; border: none; cursor: pointer;
        }
        .kategori-btn-cancel:hover { background: #c5c5c5; }
        
        .kategori-btn-submit {
            padding: .6rem 1.2rem;
            background: linear-gradient(180deg, #7FC4FF, #5EA6FF);
            color: white; border-radius: .75rem; font-weight: 600; font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif; transition: all 0.2s;
            border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(94,166,255,0.3);
        }
        .kategori-btn-submit:hover { opacity: .9; transform: translateY(-1px); }
        .kategori-btn-submit:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
    </style>

    {{-- ================= SCRIPT ================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

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
                    notifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-red-50 border-red-200 text-red-800';
                    notifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-red-100';
                    notifIcon.innerHTML = '<svg class="w-4.5 h-4.5 text-red-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
                    notifBar.style.background = '#f87171';
                }
                notifText.textContent = message;
                notifBar.style.transition = 'none';
                notifBar.style.width = '100%';
                requestAnimationFrame(() => { requestAnimationFrame(() => { notifBar.style.transition = 'width 3.5s linear'; notifBar.style.width = '0%'; }); });
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

            function openModal(m) { m.classList.remove('hidden'); m.classList.add('flex'); }
            function closeModal(m) { m.classList.add('hidden'); m.classList.remove('flex'); }

            // ★ MODAL TAMBAH ★
            const tambahModal = document.getElementById('tambahKategoriModal');
            const tambahForm = document.getElementById('tambahKategoriForm');
            const tambahSubmitBtn = document.getElementById('tambahSubmitBtn');

            document.getElementById('openTambahKategori')?.addEventListener('click', () => openModal(tambahModal));
            document.getElementById('closeTambahKategori')?.addEventListener('click', () => closeModal(tambahModal));
            document.getElementById('cancelTambahKategori')?.addEventListener('click', () => closeModal(tambahModal));
            tambahModal?.addEventListener('click', e => { if (e.target === tambahModal) closeModal(tambahModal); });

            tambahForm?.addEventListener('submit', function () {
                tambahSubmitBtn.disabled = true;
            });

            // ★ MODAL EDIT ★
            const editModal = document.getElementById('editKategoriModal');
            const editForm = document.getElementById('editKategoriForm');
            const editSubmitBtn = document.getElementById('editSubmitBtn');

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.editKategoriBtn');
                if (!btn) return;
                document.getElementById('editKategoriName').value = btn.dataset.name;
                editForm.action = '/categories/' + btn.dataset.id;
                openModal(editModal);
            });
            document.getElementById('closeEditKategori')?.addEventListener('click', () => closeModal(editModal));
            document.getElementById('cancelEditKategori')?.addEventListener('click', () => closeModal(editModal));
            editModal?.addEventListener('click', e => { if (e.target === editModal) closeModal(editModal); });

            editForm?.addEventListener('submit', function () {
                editSubmitBtn.disabled = true;
            });

            // ★ MODAL HAPUS ★
            const deleteModal = document.getElementById('deleteKategoriModal');
            const deleteForm = document.getElementById('deleteKategoriForm');
            const deleteSubmitBtn = document.getElementById('deleteSubmitBtn');

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.deleteKategoriBtn');
                if (!btn) return;
                document.getElementById('deleteKategoriName').textContent = btn.dataset.name;
                deleteForm.action = '/categories/' + btn.dataset.id;
                openModal(deleteModal);
            });
            document.getElementById('cancelDeleteKategori')?.addEventListener('click', () => closeModal(deleteModal));
            deleteModal?.addEventListener('click', e => { if (e.target === deleteModal) closeModal(deleteModal); });

            deleteForm?.addEventListener('submit', function () {
                deleteSubmitBtn.disabled = true;
            });

            // ★ ESC KEY ★
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') { closeModal(tambahModal); closeModal(editModal); closeModal(deleteModal); }
            });
        });
    </script>

@endsection