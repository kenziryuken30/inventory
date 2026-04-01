@extends('layouts.app')

@section('content')
<div class="p-6" x-data="{ openCreate:false, openEdit:false, editData:{} }">

    {{-- HEADER --}}
    <div class="flex justify-between items-start mb-4">
        <div>
            <h1 class="text-2xl font-bold text-teal-600">Kategori</h1>
            <p class="text-gray-500 text-sm">Daftar dan Input Kategori</p>
        </div>

        <button @click="openCreate = true"
            class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-lg shadow">
            + Tambah Kategori
        </button>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border">
        <table class="w-full text-sm text-center">
            <thead class="bg-gradient-to-r from-teal-500 to-cyan-500 text-white">
                <tr>
                    <th class="py-3">NO</th>
                    <th>KATEGORI</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $cat)
                <tr class="border-b">
                    <td class="py-3">{{ $loop->iteration }}</td>
                    <td>{{ $cat->category_name }}</td>
                    <td class="flex justify-center gap-3 py-3">

                        {{-- EDIT --}}
                        <button 
                            @click="openEdit=true; editData={id:'{{ $cat->id }}', name:'{{ $cat->category_name }}'}"
                            class="text-blue-500">
                            ✏️
                        </button>

                        {{-- DELETE --}}
                        <form action="{{ route('categories.destroy', $cat->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Yakin hapus?')" class="text-red-500">
                                🗑️
                            </button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ================= MODAL CREATE ================= --}}
    <div x-show="openCreate" class="fixed inset-0 bg-black/50 flex items-center justify-center">

        <div class="bg-white rounded-xl p-6 w-96 shadow-xl">
            <h2 class="text-lg font-bold mb-4">Tambah Kategori</h2>

            <form action="{{ route('categories.store') }}" method="POST">
                @csrf

                <input type="text" name="category_name"
                       class="w-full border rounded-lg px-3 py-2 mb-3"
                       placeholder="Nama kategori">

                <div class="flex justify-end gap-2">
                    <button type="button" @click="openCreate=false"
                        class="px-3 py-1 bg-gray-300 rounded">
                        Batal
                    </button>

                    <button class="px-3 py-1 bg-teal-500 text-white rounded">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL EDIT ================= --}}
    <div x-show="openEdit" class="fixed inset-0 bg-black/50 flex items-center justify-center">

        <div class="bg-white rounded-xl p-6 w-96 shadow-xl">
            <h2 class="text-lg font-bold mb-4">Edit Kategori</h2>

            <form :action="'/categories/' + editData.id" method="POST">
                @csrf
                @method('PUT')

                <input type="text" name="category_name"
                       x-model="editData.name"
                       class="w-full border rounded-lg px-3 py-2 mb-3">

                <div class="flex justify-end gap-2">
                    <button type="button" @click="openEdit=false"
                        class="px-3 py-1 bg-gray-300 rounded">
                        Batal
                    </button>

                    <button class="px-3 py-1 bg-teal-500 text-white rounded">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection