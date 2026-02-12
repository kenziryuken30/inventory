@extends('layouts.app')

@section('content')
<h2 class="text-xl font-semibold mb-4">Data Consumable</h2>

{{-- ALERT --}}
@if (session('success'))
    <div class="mb-4 px-4 py-3 rounded bg-green-100 text-green-700 border">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-4 px-4 py-3 rounded bg-red-100 text-red-700 border">
        {{ session('error') }}
    </div>
@endif

<div
x-data="{ add:false, edit:false, item:{} }"
class="bg-white rounded shadow p-4">

    {{-- SEARCH & TAMBAH --}}
    <div class="flex justify-between items-center mb-4">
        <form method="GET" action="/consumable">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari consumable..."
                class="border rounded px-3 py-2 w-64">
        </form>

        <button
            @click="add=true"
            class="px-4 py-2 bg-blue-600 text-white rounded">
            + Tambah Consumable
        </button>
    </div>

    {{-- TABLE --}}
    <table class="w-full border-collapse">
        <thead>
            <tr class="border-b text-sm text-gray-600">
                <th>Foto</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th class="text-right">Aksi</th>
            </tr>
        </thead>

        <tbody>
        @forelse($consumables as $c)
            <tr class="border-b text-sm align-middle">
    
                {{-- NAMA --}}
                <td>{{ $c->name }}</td>

                {{-- KATEGORI --}}
                <td>{{ optional($c->category)->category_name ?? '-' }}</td>

                {{-- STOK --}}
                <td>
                    {{ $c->stock }} {{ $c->unit }}
                    @if($c->stock < $c->minimum_stock)
                        <div class="text-xs text-red-500">
                            Min: {{ $c->minimum_stock }}
                        </div>
                    @endif
                </td>

                {{-- FOTO --}}
                <td class="py-2">
                    <img
                        src="{{ $c->image
                            ? asset('storage/'.$c->image)
                            : asset('images/no-image.png') }}"
                        class="w-10 h-10 object-cover rounded">
                </td>

                {{-- AKSI --}}
                <td class="text-right w-28">
                    <div class="inline-flex gap-2 justify-end">
                        <button
                            @click="edit=true; item=@js($c)"
                            class="text-blue-600">
                            ✏️
                        </button>

                        <form
                            method="POST"
                            action="/consumable/{{ $c->id }}"
                            onsubmit="return confirm('Yakin hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:text-red-800">
                                🗑️
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-6 text-gray-500">
                    Data kosong
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

{{-- ================= MODAL TAMBAH ================= --}}
<div x-show="add" class="modal">
    <div class="modal-box">
        <h3 class="modal-title">Tambah Consumable</h3>

        <form method="POST" action="/consumable" enctype="multipart/form-data">
            @csrf

            {{-- NAMA --}}
            <input name="name" placeholder="Nama" class="input" required>

            {{-- STOK --}}
            <input name="stock" type="number" placeholder="Stok" class="input" required>

            {{-- MINIMUM STOK --}}
            <input name="minimum_stock" type="number" placeholder="Minimum Stok" class="input">

            {{-- UNIT --}}
            <select name="unit" class="input" required>
                <option value="">-- Pilih Unit --</option>
                <option value="pcs">Pcs</option>
                <option value="box">Box</option>
                <option value="pack">Pack</option>
                <option value="meter">Meter</option>
                <option value="liter">Liter</option>
                <option value="botol">Botol</option>
            </select>

            {{-- KATEGORI --}}
            <select name="category_id" class="input">
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                @endforeach
            </select>

            {{-- FOTO (PALING BAWAH) --}}
            <input type="file" name="image" class="input">

            <div class="flex justify-end gap-2">
                <button type="button" @click="add=false">Batal</button>
                <button class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>


    {{-- ================= MODAL EDIT ================= --}}
<div x-show="edit" class="modal">
    <div class="modal-box">
        <h3 class="modal-title">Edit Consumable</h3>

        <form
            method="POST"
            :action="`/consumable/${item.id}`"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- NAMA --}}
            <input name="name" x-model="item.name" class="input" required>

            {{-- STOK --}}
            <input name="stock" type="number" x-model="item.stock" class="input">

            {{-- MINIMUM STOK --}}
            <input name="minimum_stock" type="number" x-model="item.minimum_stock" class="input">

            {{-- UNIT --}}
            <select name="unit" x-model="item.unit" class="input">
                <option value="pcs">Pcs</option>
                <option value="box">Box</option>
                <option value="pack">Pack</option>
                <option value="meter">Meter</option>
                <option value="liter">Liter</option>
                <option value="botol">Botol</option>
            </select>

            {{-- KATEGORI --}}
            <select name="category_id" x-model="item.category_id" class="input">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">
                        {{ $cat->category_name }}
                    </option>
                @endforeach
            </select>

            {{-- PREVIEW FOTO LAMA --}}
            <template x-if="item.image">
                <img
                    :src="`/storage/${item.image}`"
                    class="w-24 h-24 object-cover rounded mx-auto mb-2">
            </template>

            {{-- GANTI FOTO (PALING BAWAH) --}}
            <input type="file" name="image" class="input">

            <div class="flex justify-end gap-2">
                <button type="button" @click="edit=false">Batal</button>
                <button class="btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>


{{-- STYLE SAMA KAYAK TOOLS --}}
<style>
.modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.4);
    display: flex;
    align-items: center;
    justify-content: center;
}
.modal-box {
    background: #fff;
    padding: 1.5rem;
    width: 100%;
    max-width: 500px;
    border-radius: .5rem;
}
.modal-title {
    font-weight: 600;
    margin-bottom: 1rem;
}
.input {
    width: 100%;
    margin-bottom: .75rem;
    padding: .5rem;
    border: 1px solid #ccc;
    border-radius: .25rem;
}
.btn-primary {
    background: #2563eb;
    color: white;
    padding: .5rem 1rem;
    border-radius: .25rem;
}
</style>
@endsection
