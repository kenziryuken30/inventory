@extends('layouts.app')

@section('content')

    <div x-data="{ 
        add:false, 
        edit:false, 
        preview:false, 
        previewImage:'', 
        item:{},
        restock:false,
        restockItem:{},
        qty:0
    }" class="px-8 pt-6 pb-10">

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


        {{-- ================= SEARCH + TAMBAH ================= --}}
        <div class="mb-5">
            <div class="bg-gradient-to-b from-[#7ED6DF] to-[#1CA7B6] p-4 rounded-2xl shadow-lg">

                <div class="flex gap-3 items-center">

                    {{-- SEARCH INPUT --}}
                    <div class="relative flex-1">

                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8"/>
                                <path stroke-linecap="round" d="m21 21-4.35-4.35"/>
                            </svg>
                        </div>

                        <form method="GET" action="/consumable" class="flex items-center">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang..."
                                class="w-full bg-white rounded-xl shadow-inner pl-10 pr-10 py-2.5 text-sm outline-none">

                            @if(request('search'))
                                <a href="/consumable"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            @endif
                        </form>

                    </div>

                    {{-- BUTTON TAMBAH --}}
                    <button @click="add = true"
                        class="px-5 py-2.5 text-sm bg-white text-[#1CA7B6] font-semibold rounded-xl shadow hover:bg-gray-100 transition whitespace-nowrap">
                        + Tambah Consumable
                    </button>

                </div>

            </div>
        </div>

        <div class="rounded-2xl shadow-lg overflow-hidden bg-white border border-gray-100">

            <table class="w-full text-sm">

                {{-- HEADER --}}
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
                                    <button type="button" @click="edit = true; item = @js($c)"
                                        class="p-2 rounded-lg text-gray-400 hover:text-blue-500 hover:bg-blue-50 transition">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                                        </svg>
                                    </button>

                                    {{-- HAPUS --}}
                                    <form method="POST" action="/consumable/{{ $c->id }}"
                                        onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                            </svg>
                                        </button>
                                    </form>

                                    {{-- RESTOCK --}}
                                    <button type="button" @click="restock=true; restockItem = @js($c); qty=0"
                                        class="p-2 rounded-lg text-gray-400 hover:text-green-500 hover:bg-green-50 transition">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 4.5v15m7.5-7.5h-15"/>
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

        {{-- ================= MODAL TAMBAH STOK ================= --}}
        <div x-show="restock" x-cloak class="modal">
            <div class="modal-box">
                <h3 class="modal-title">Tambah Stok</h3>

                <form method="POST" :action="`/consumable/${restockItem.id}/restock`">
                    @csrf

                    <div class="mb-2 text-sm text-gray-600">
                        Barang:
                        <span class="font-semibold" x-text="restockItem.name"></span>
                    </div>

                    <input type="number" name="qty" x-model="qty" placeholder="Jumlah tambah stok" class="input" required>

                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" @click="restock=false" class="px-4 py-2 bg-gray-200 rounded-lg">
                            Batal
                        </button>

                        <button type="submit" class="btn-primary">
                            Tambah
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