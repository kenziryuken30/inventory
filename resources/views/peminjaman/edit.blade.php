@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen flex flex-col">

    {{-- ================= TITLE ================= --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-3xl font-bold text-[#1CA7B6] tracking-tight">Edit Peminjaman Tools</h2>
            <p class="text-sm text-gray-500 mt-1">Edit Proses Peminjaman dan Daftar Tools yang dipinjam</p>
        </div>

        <a href="{{ route('peminjaman.index') }}"
           class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition">
            ← Kembali
        </a>
    </div>

    {{-- ================= ALERT ================= --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl shadow-sm">
            {{ session('error') }}
        </div>
    @endif


    {{-- ================= PANEL BESAR ================= --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 space-y-8">

        {{-- ================= FORM UPDATE (GRID 3 KOLOM + WARNA BARU) ================= --}}
        <form id="updateForm"
              action="{{ route('peminjaman.update', $transaction->id) }}"
              method="POST">
            @csrf
            @method('PUT')

            {{-- Grid Utama: 3 Kolom --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- === BARIS 1 === --}}
                
                {{-- Kolom 1: Nama Peminjam --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Peminjam</label>
                    <input type="text"
                           name="borrower_name"
                           value="{{ $transaction->borrower_name }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                </div>

                {{-- Kolom 2: Tanggal --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal</label>
                    <input type="date"
                           name="date"
                           value="{{ $transaction->date->format('Y-m-d') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                </div>

                {{-- Kolom 3: KOSONG (Untuk mendorong Keperluan ke bawah) --}}
                <div class="hidden md:block">
                    <label class="block text-sm font-bold text-gray-700 mb-2">&nbsp;</label>
                    <div class="w-full px-4 py-2.5">&nbsp;</div>
                </div>

                {{-- === BARIS 2 === --}}

                {{-- Kolom 1: Nama Client --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Client</label>
                    <input type="text"
                           name="client_name"
                           value="{{ $transaction->client_name }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                </div>

                {{-- Kolom 2: Proyek --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Proyek</label>
                    <input type="text"
                           name="project"
                           value="{{ $transaction->project }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                </div>

                {{-- Kolom 3: Keperluan (Sejajar Proyek) --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Keperluan</label>
                    <input type="text"
                           name="purpose"
                           value="{{ $transaction->purpose }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white shadow-md focus:ring-2 focus:ring-[#1CA7B6] focus:border-transparent transition duration-200 text-sm">
                </div>

            </div>
        </form>
        {{-- END FORM UPDATE --}}


        {{-- ================= DAFTAR TOOLS ================= --}}
        <div class="space-y-4">
            
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-gray-800 text-lg">Daftar Alat yang Di Edit</h3>

                <button type="button"
                        id="openToolsBtn"
                        class="text-white px-5 py-2 rounded-xl font-bold shadow-md hover:opacity-90 transition-all duration-200 text-sm tracking-wide"
                        style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                    + Pilih Tools
                </button>
            </div>

            <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-white text-xs uppercase tracking-wider"
                            style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                            <th class="py-3 px-4 font-semibold text-center w-10">NO</th>
                            <th class="py-3 px-4 font-semibold text-center w-20">Image</th>
                            <th class="py-3 px-4 font-semibold text-center">Nama Tools</th>
                            <th class="py-3 px-4 font-semibold text-center">No Seri</th>
                            <th class="py-3 px-4 font-semibold text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($transaction->items as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="text-center py-4 px-4 text-gray-600">
                                {{ $loop->iteration }}
                            </td>

                            <td class="text-center py-2 px-4">
                                @if($item->toolkit && $item->toolkit->image)
                                    <img src="{{ asset('storage/'.$item->toolkit->image) }}"
                                         class="w-12 h-12 object-contain mx-auto rounded-md shadow-sm border">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-md mx-auto flex items-center justify-center text-gray-400 text-xs">
                                        No Img
                                    </div>
                                @endif
                            </td>

                            <td class="text-center py-4 px-4 font-medium text-gray-800">
                                {{ $item->toolkit->toolkit_name ?? '-' }}
                            </td>

                            <td class="text-center py-4 px-4 text-gray-600 font-mono">
                                {{ $item->serial->serial_number ?? '-' }}
                            </td>

                            <td class="text-center py-4 px-4">
                                <form action="{{ route('peminjaman.item.destroy', $item->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus item ini?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg font-semibold text-xs transition shadow-sm">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-400 py-10 bg-white">
                                Belum ada tools yang dipinjam.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>


        {{-- ================= SAVE BUTTON ================= --}}
        <div class="flex justify-end pt-4 border-t border-gray-100">
            <button type="submit"
                    form="updateForm"
                    class="text-white px-8 py-3 rounded-xl font-bold shadow-md hover:opacity-90 transition-all duration-200 tracking-wide"
                    style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                Save Transaksi
            </button>
        </div>

    </div>


    {{-- ================= MODAL TOOLS ================= --}}
    <div id="toolsModal"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">

        <div class="bg-white w-11/12 max-w-3xl rounded-2xl shadow-2xl relative overflow-hidden flex flex-col">

            <div class="px-6 py-4 flex justify-between items-center text-white"
                 style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                <div>
                    <h3 class="text-lg font-bold">Pilih Tools Tersedia</h3>
                </div>
                <button type="button"
                        id="closeToolsBtn"
                        class="text-2xl text-white/80 hover:text-white transition">
                    ✕
                </button>
            </div>

            <div class="p-6 overflow-auto flex-1 bg-gray-50">
                <div class="mb-6">
                    <input type="text"
                           id="searchToolsModal"
                           placeholder="Cari nama tools atau no seri..."
                           class="w-full bg-white border-0 rounded-xl px-4 py-3 shadow-inner focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none text-sm">
                </div>

                <form action="{{ route('peminjaman.item.add', $transaction->id) }}"
                      method="POST">
                    @csrf

                    <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm max-h-[350px] overflow-y-auto">

                        <table class="w-full text-sm">

                            <thead class="sticky top-0 text-white text-xs uppercase tracking-wider"
                                   style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                                <tr>
                                    <th class="py-3 px-4 w-10"></th>
                                    <th class="py-3 px-4 text-left font-semibold">Nama Tools</th>
                                    <th class="py-3 px-4 text-center font-semibold">No Seri</th>
                                    <th class="py-3 px-4 text-center font-semibold">Image</th>
                                </tr>
                            </thead>

                            <tbody id="toolsTableBody" class="bg-white divide-y divide-gray-100">

                                @forelse ($serials as $serial)
                                <tr class="hover:bg-gray-50 transition cursor-pointer">

                                    <td class="py-3 px-4 text-center">
                                        <input type="checkbox"
                                               name="serial_ids[]"
                                               value="{{ $serial->id }}"
                                               class="w-5 h-5 rounded border-gray-300 text-[#1CA7B6] focus:ring-[#1CA7B6]">
                                    </td>

                                    <td class="py-3 px-4 font-medium text-gray-800">
                                        {{ $serial->toolkit->toolkit_name }}
                                    </td>

                                    <td class="py-3 px-4 text-center text-gray-600 font-mono text-xs">
                                        {{ $serial->serial_number }}
                                    </td>

                                    <td class="py-3 px-4 text-center">
                                        <img src="{{ $serial->toolkit->image 
                                            ? asset('storage/'.$serial->toolkit->image)
                                            : asset('images/no-image.png') }}"
                                             class="w-10 h-10 object-contain mx-auto rounded shadow-sm border">
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4"
                                        class="text-center text-gray-400 py-10">
                                        Tidak ada tools tersedia
                                    </td>
                                </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    <div class="flex justify-end gap-3 pt-6 mt-4 bg-transparent">
                        <button type="button"
                                onclick="document.getElementById('toolsModal').classList.add('hidden'); document.getElementById('toolsModal').classList.remove('flex');"
                                class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                            Batal
                        </button>

                        <button type="submit"
                                class="text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md hover:opacity-90 transition"
                                style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                            + Tambahkan
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>


    {{-- ================= SCRIPT ================= --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const modal = document.getElementById('toolsModal');
        const openBtn = document.getElementById('openToolsBtn');
        const closeBtns = document.querySelectorAll('#closeToolsBtn');
        const searchInput = document.getElementById('searchToolsModal');

        // ================= OPEN MODAL =================
        if (openBtn && modal) {
            openBtn.addEventListener('click', function () {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            });
        }

        // ================= CLOSE MODAL =================
        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            // reset search
            if (searchInput) {
                searchInput.value = '';
                document.querySelectorAll('#toolsTableBody tr').forEach(row => {
                    row.style.display = '';
                });
            }
        }

        closeBtns.forEach(btn => {
            btn.addEventListener('click', closeModal);
        });
        
        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if(e.target === modal) {
                closeModal();
            }
        });

        // ================= LIVE SEARCH =================
        if (searchInput) {
            searchInput.addEventListener('keyup', function () {

                let keyword = this.value.toLowerCase();

                document.querySelectorAll('#toolsTableBody tr').forEach(function (row) {

                    let text = row.innerText.toLowerCase();

                    row.style.display = text.includes(keyword) ? '' : 'none';

                });

            });
        }

    });
    </script>
    @endsection