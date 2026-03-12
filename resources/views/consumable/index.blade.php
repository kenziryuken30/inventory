@extends('layouts.app')

@section('content')

<div x-data="{ 
                    add:false, 
                    edit:false, 
                    preview:false, 
                    previewImage:'', 
                    item:{} 
                }" class="px-8 pt-6 pb-10">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-start mb-6">

        <div>
            <h1 class="text-3xl font-bold text-[#1CA7B6] tracking-wide">
                Data Consumable
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Kelola data Consumable
            </p>
        </div>

        <div class="flex items-center gap-3">

            <button @click="add = true"
                class="px-4 py-2 text-sm text-white rounded-lg shadow-md hover:opacity-90 transition"
                style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                + Tambah Consumable
            </button>

        </div>
    </div>


    {{-- ================= SEARCH ================= --}}
    <div class="mb-5">
        <div class="bg-gradient-to-b from-[#7ED6DF] to-[#1CA7B6] p-4 rounded-2xl shadow-lg">

            <form method="GET" action="/consumable" class="flex gap-3 items-center">

                <div class="relative flex-1">

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari barang..."
                        class="w-full bg-white rounded-xl shadow-inner px-4 py-2 pr-10 text-sm outline-none">

                    @if(request('search'))
                    <a href="/consumable"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 text-lg transition">
                        ×
                    </a>
                    @endif

                </div>

                <button type="submit"
                    class="px-4 py-2 text-sm bg-white text-[#1CA7B6] font-semibold rounded-lg shadow hover:bg-gray-100 transition">
                    Cari
                </button>

            </form>

        </div>
    </div>


    {{-- ================= TABLE (SUDAH DIRAPIKAN) ================= --}}
    <div class="rounded-2xl shadow-lg overflow-hidden bg-white border border-gray-100">

        <table class="w-full text-sm">

            {{-- HEADER --}}
            <thead>
                <tr class="text-white text-xs uppercase tracking-wider"
                    style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">

                    {{-- Tambah padding dan align middle --}}
                    <th class="py-4 px-4 text-center w-28 align-middle">Foto</th>
                    <th class="py-4 px-4 text-left align-middle">Nama Barang</th>
                    <th class="py-4 px-4 text-center align-middle">Kategori</th>
                    <th class="py-4 px-4 text-center align-middle">Stok Tersedia</th>
                    <th class="py-4 px-4 text-center w-28 align-middle">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-gray-700 text-sm">

                @forelse($consumables as $c)
                {{-- GARIS DIPERHALUS: border-b border-gray-100 (warna lebih terang) --}}
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition">

                    {{-- FOTO --}}
                    <td class="py-4 px-4 text-center align-middle">
                        <div class="flex justify-center">
                            <img src="{{ $c->image ? asset('storage/' . $c->image) : asset('images/no-image.png') }}"
                                @click="preview=true; previewImage='{{ $c->image ? asset('storage/' . $c->image) : asset('images/no-image.png') }}'"
                                class="w-12 h-12 object-contain rounded-lg border border-gray-100 bg-white p-1 cursor-pointer hover:scale-105 transition shadow-sm">
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
                        <div class="font-semibold {{ $c->stock < $c->minimum_stock ? 'text-red-600' : 'text-gray-800' }}">
                            {{ $c->stock }}
                        </div>
                        <div class="text-xs text-gray-400 mt-0.5">
                            {{ $c->unit }}
                        </div>
                    </td>

                    {{-- AKSI --}}
                    <td class="py-4 px-4 text-center align-middle">
                        <div class="flex justify-center gap-4 text-lg">

                            <button type="button" @click="edit = true; item = @js($c)"
                                class="text-gray-500 hover:text-blue-600 transition transform hover:scale-110">
                                ✏
                            </button>

                            <form method="POST" action="/consumable/{{ $c->id }}"
                                onsubmit="return confirm('Yakin hapus data ini?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="text-gray-500 hover:text-red-600 transition transform hover:scale-110">
                                    🗑
                                </button>
                            </form>

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
                            <span>Data belum tersedia</span>
                        </div>
                    </td>
                </tr>
                @endforelse

            </tbody>

        </table>
    </div>

    {{-- ... MODAL TAMBAH, PREVIEW, EDIT BIARKAN APA ADANYA ... --}}

    {{-- ================= MODAL TAMBAH ================= --}}
    <div x-show="add" x-cloak class="modal">
        <div class="modal-box">
            <h3 class="modal-title">Tambah Consumable</h3>

            <form method="POST" action="/consumable" enctype="multipart/form-data">
                @csrf

                <input name="name" placeholder="Nama Barang" class="input" required>
                <input name="stock" type="number" placeholder="Stok" class="input" required>
                <input name="minimum_stock" type="number" placeholder="Minimum Stok" class="input">

                <select name="unit" class="input" required>
                    <option value="">-- Pilih Unit --</option>
                    <option value="pcs">Pcs</option>
                    <option value="box">Box</option>
                    <option value="pack">Pack</option>
                    <option value="meter">Meter</option>
                    <option value="liter">Liter</option>
                    <option value="botol">Botol</option>
                </select>

                <select name="category_id" class="input">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">
                        {{ $cat->category_name }}
                    </option>
                    @endforeach
                </select>

                <input type="file" name="image" class="input">

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" @click="add=false" class="px-4 py-2 bg-gray-200 rounded-lg">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL PREVIEW FOTO ================= --}}
    <div x-show="preview" x-cloak class="modal" @click.self="preview=false">
        <div class="modal-box bg-transparent shadow-none max-w-3xl">

            <div class="flex justify-end mb-2">
                <button @click="preview=false" class="text-white text-2xl">✕</button>
            </div>

            <img :src="previewImage" class="w-full max-h-[80vh] object-contain rounded-xl bg-white p-4 shadow-xl">
        </div>
    </div>

    {{-- ================= MODAL EDIT ================= --}}
    <div x-show="edit" x-cloak class="modal">
        <div class="modal-box">
            <h3 class="modal-title">Edit Consumable</h3>

            <form method="POST" :action="`/consumable/${item.id}`" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input name="name" x-model="item.name" class="input" required>
                <input name="stock" type="number" x-model="item.stock" class="input">
                <input name="minimum_stock" type="number" x-model="item.minimum_stock" class="input">

                <select name="unit" x-model="item.unit" class="input">
                    <option value="pcs">Pcs</option>
                    <option value="box">Box</option>
                    <option value="pack">Pack</option>
                    <option value="meter">Meter</option>
                    <option value="liter">Liter</option>
                    <option value="botol">Botol</option>
                </select>

                <select name="category_id" x-model="item.category_id" class="input">
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">
                        {{ $cat->category_name }}
                    </option>
                    @endforeach
                </select>

                <input type="file" name="image" class="input">

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" @click="edit=false" class="px-4 py-2 bg-gray-200 rounded-lg">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<style>
    .modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .7);
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }

    .modal-box {
        background: white;
        padding: 1.5rem;
        width: 100%;
        max-width: 500px;
        border-radius: 1rem;
    }

    .modal-title {
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .input {
        width: 100%;
        margin-bottom: .75rem;
        padding: .6rem;
        border: 1px solid #ccc;
        border-radius: .5rem;
    }

    .btn-primary {
        background: linear-gradient(180deg, #5FD0DF, #1CA7B6);
        color: white;
        padding: .6rem 1.2rem;
        border-radius: .5rem;
    }
</style>

@endsection