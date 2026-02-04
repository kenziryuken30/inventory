@extends('layouts.app')

@section('content')
<h2 class="text-xl font-semibold mb-4">Data Tools</h2>

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

<div class="bg-white rounded shadow p-4">

<div class="flex justify-between items-center mb-4">

    {{-- SEARCH & FILTER --}}
    <form method="GET"
          action="{{ route('tools.index') }}"
          class="flex gap-2 items-center">

        <input type="text"
               name="search"
               value="{{ request('search') }}"
               placeholder="Cari barang..."
               class="border rounded px-3 py-2 w-64">

        <select name="condition"
                onchange="this.form.submit()"
                class="border rounded px-3 py-2">
            <option value="">Semua Kondisi</option>
            <option value="baik" {{ request('condition') === 'baik' ? 'selected' : '' }}>
                Baik
            </option>
            <option value="rusak" {{ request('condition') === 'rusak' ? 'selected' : '' }}>
                Rusak
            </option>
            <option value="maintenance" {{ request('condition') === 'maintenance' ? 'selected' : '' }}>
                Maintenance
            </option>
        </select>
    </form>

    <button onclick="openAddModal()"
            class="px-4 py-2 bg-blue-600 text-white rounded">
        + Tambah Barang
    </button>
</div>

    <table class="w-full border-collapse">
        <thead>
            <tr class="border-b text-sm text-gray-600">
                <th>Foto</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>No Seri</th>
                <th>Status</th>
                <th>Kondisi</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
@foreach ($tools as $tool)
    @php
        $condition = $tool->latestCondition->condition ?? 'baik';
    @endphp

    <tr class="border-b text-sm align-middle">

    <td class="py-2">
        <img
            src="{{ $tool->toolkit->image
                ? asset('storage/'.$tool->toolkit->image)
                : asset('images/no-image.png') }}"
            style="width: 40px; height: 40px; object-fit: cover; border-radius: 0.25rem;">
    </td>

    <td>
        {{ $tool->toolkit->toolkit_name }}
    </td>

    <td>
        {{ $tool->toolkit->category->category_name ?? '-' }}
    </td>

    <td>
        {{ $tool->serial_number }}
    </td>

    <td>
        {{ strtoupper($tool->status) }}
    </td>

    <td>
        {{ strtoupper($condition) }}
    </td>

    {{-- AKSI --}}
    <td class="text-right w-28">
        <div class="inline-flex gap-2 justify-end">

                <button
                    onclick="openEditModal(
                        '{{ $tool->id }}',
                        '{{ $tool->toolkit->toolkit_name }}',
                        '{{ $tool->toolkit->category_id }}',
                        '{{ $tool->serial_number }}'
                    )"
                    class="text-blue-600"
                >
                    ✏️
                </button>

            @if ($tool->status === 'dipinjam')
                <button
                    type="button"
                    onclick="alert('Barang sedang dipinjam, tidak bisa dihapus')"
                    class="text-gray-400 cursor-not-allowed">
                    🗑️
                </button>
            @else
                <form
                    action="{{ route('tools.destroy', $tool->id) }}"
                    method="POST"
                    class="inline"
                    onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                    @csrf
                    @method('DELETE')

                    <button class="text-red-600 hover:text-red-800">
                        🗑️
                    </button>
                </form>
            @endif

        </div>
    </td>
</tr>
@endforeach
</tbody>


    </table>
</div>

{{-- ================= MODAL TAMBAH ================= --}}
<div id="addModal" class="modal hidden">
    <div class="modal-box">
        <h3 class="modal-title">Tambah Barang</h3>

        <form method="POST" action="{{ route('tools.store') }}" enctype="multipart/form-data">
            @csrf

            <input name="toolkit_name" placeholder="Nama Barang" class="input" required>
            <select name="category_id" class="input">
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                @endforeach
            </select>
            <input name="serial_number" placeholder="No Seri" class="input" required>
            <input type="file" name="image" class="input">

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeAddModal()">Batal</button>
                <button class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= MODAL EDIT ================= --}}
<div id="editModal" class="modal hidden">
    <div class="modal-box">
        <h3 class="modal-title">Edit Barang</h3>

        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <input id="edit_name" name="toolkit_name" class="input" required>
            <select id="edit_category" name="category_id" class="input">
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                @endforeach
            </select>
            <input id="edit_serial" name="serial_number" class="input" required>
            <input type="file" name="image" class="input">

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()">Batal</button>
                <button class="btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- ================= STYLE & SCRIPT ================= --}}

<style>

.modal-box {
    background: #fff;
    padding: 1.5rem;
    width: 100%; max-width: 500px;
    border-radius: .5rem;
}
.modal-title { font-weight: 600; margin-bottom: 1rem; }
.input { width: 100%; margin-bottom: .75rem; padding: .5rem; border: 1px solid #ccc; border-radius: .25rem; }
.hidden { display: none; }
</style>


<script>
function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
}
function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}

function openEditModal(id, name, category, serial) {
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_category').value = category;
    document.getElementById('edit_serial').value = serial;

    document.getElementById('editForm').action = '/data-tools/' + id;

    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>

@endsection
