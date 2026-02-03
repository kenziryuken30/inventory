@extends('layouts.app')

@section('content')
<div
x-data="{
    add:false,
    edit:false,
    item:{}
}"
class="p-6 space-y-4">

    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold">Data Consumable</h1>

        <button
            @click="add=true"
            class="px-4 py-2 border rounded bg-blue-600 text-white">
            + Tambah
        </button>
    </div>

    <!-- SEARCH -->
    <form method="GET" action="/consumable">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari consumable..."
            class="border rounded px-3 py-2 w-64">
    </form>

    <!-- TABLE -->
    <div class="overflow-x-auto">
    <table class="w-full border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-2">Foto</th>
                <th class="border p-2">Nama</th>
                <th class="border p-2">Kategori</th>
                <th class="border p-2">Stok</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($consumables as $c)
            <tr>
                <td class="border p-2 text-center">
                    @if($c->image)
                        <img src="{{ asset('storage/'.$c->image) }}" class="h-12 mx-auto">
                    @else
                        <span class="text-xs text-gray-400">No Image</span>
                    @endif
                </td>

                <td class="border p-2">{{ $c->name }}</td>

                <td class="border p-2">
                    {{ optional($c->category)->category_name ?? '-' }}
                </td>

                <td class="border p-2">
                    {{ $c->stock }} {{ $c->unit }}
                    @if($c->stock < $c->minimum_stock)
                        <div class="text-xs text-red-500">
                            Min: {{ $c->minimum_stock }}
                        </div>
                    @endif
                </td>

                <td class="border p-2 text-center space-x-2">
                    <button
                        type="button"
                        @click="edit=true; item=@js($c)"
                        class="px-2 border rounded">
                        Edit
                    </button>

                    <form method="POST" action="/consumable/{{ $c->id }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button class="px-2 border rounded">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center p-4">
                    Data kosong
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <!-- MODAL TAMBAH -->
    <div x-show="add" class="fixed inset-0 bg-black/30 flex items-center justify-center">
        <form
            method="POST"
            action="/consumable"
            enctype="multipart/form-data"
            class="bg-white p-4 rounded w-80 space-y-2">
            @csrf

            <h2 class="font-bold">Tambah Consumable</h2>

            <input type="file" name="image" class="border p-2 w-full">
            <input name="name" placeholder="Nama" class="border p-2 w-full">
            <input name="stock" type="number" placeholder="Stok" class="border p-2 w-full">
            <input name="minimum_stock" type="number" placeholder="Minimum Stok" class="border p-2 w-full">
            <select name="unit" class="border p-2 w-full" required>
                <option value="">-- Pilih Unit --</option>
                <option value="pcs">Pcs</option>
                <option value="box">Box</option>
                <option value="pack">Pack</option>
                <option value="meter">Meter</option>
                <option value="liter">Liter</option>
                <option value="botol">Botol</option>
            </select>


            <select name="category_id" class="border p-2 w-full">
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                @endforeach
            </select>

            <div class="flex justify-end space-x-2">
                <button type="button" @click="add=false" class="border px-3 py-1">
                    Batal
                </button>
                <button class="border px-3 py-1 bg-blue-600 text-white">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <!-- MODAL EDIT -->
    <div x-show="edit" class="fixed inset-0 bg-black/30 flex items-center justify-center">
        <form 
            method="POST"
            :action="`/consumable/${item.id}`"
            enctype="multipart/form-data"
            class="bg-white p-4 rounded w-80 space-y-2">
            @csrf
            @method('PUT')

            <h2 class="font-bold">Edit Consumable</h2>

            <template x-if="item.image">
                <img :src="`/storage/${item.image}`" class="h-20 mx-auto">
            </template>

            <input type="file" name="image" class="border p-2 w-full">
            <input name="name" :value="item.name" class="border p-2 w-full">
            <select name="category_id" class="border p-2 w-full">
            @foreach($categories as $cat)
                <option 
                    value="{{ $cat->id }}"
                    :selected="item.category_id == '{{ $cat->id }}'">
                    {{ $cat->category_name }}
                </option>
            @endforeach
            </select>
            <input name="stock" type="number" :value="item.stock" class="border p-2 w-full">
            <input name="minimum_stock" type="number" :value="item.minimum_stock" class="border p-2 w-full">
           <select name="unit" class="border p-2 w-full">
                <option value="pcs" :selected="item.unit=='pcs'">Pcs</option>
                <option value="box" :selected="item.unit=='box'">Box</option>
                <option value="pack" :selected="item.unit=='pack'">Pack</option>
                <option value="meter" :selected="item.unit=='meter'">Meter</option>
                <option value="liter" :selected="item.unit=='liter'">Liter</option>
                <option value="botol" :selected="item.unit=='botol'">Botol</option>
            </select>


            <div class="flex justify-end space-x-2">
                <button type="button" @click="edit=false" class="border px-3 py-1">
                    Batal
                </button>
                <button class="border px-3 py-1 bg-blue-600 text-white">
                    Update
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
