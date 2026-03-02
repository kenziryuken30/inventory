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
            <option value="maintenance" {{ request('condition') === 'butuh_perbaikan' ? 'selected' : '' }}>
                Maintenance
            </option>
        </select>
    </form>

    <button type="button"
        id="openTambahBarang"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
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

            {{-- -EDIT --}}
            <button type="button"
                class="editBtn bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600"
                data-id="{{ $tool->id }}"
                data-name="{{ $tool->toolkit->toolkit_name }}"
                data-category="{{ $tool->toolkit->category->category_name ?? '' }}"
                data-serial="{{ $tool->serial_number }}">
                Edit
            </button>

            {{-- -DELETE --}}
            @if ($tool->status === 'DIPINJAM')
                <button type="button"
                    onclick="alert('Barang sedang dipinjam, tidak bisa dihapus')"
                    class="text-gray-400 cursor-not-allowed">
                    delete
                </button>
            @else
                <form action="{{ route('tools.destroy', $tool->id) }}"
                    method="POST"
                    class="inline"
                    onsubmit="return confirm('Yakin ingin menghapus barang ini ?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600 hover:text-red-800">
                        delete
                    </button>
                </form>
            @endif

           @php
                $condition = strtoupper($tool->latestCondition->condition ?? 'BAIK');
            @endphp

            @if($tool->status === 'TIDAK_TERSEDIA' && $condition === 'MAINTENANCE')
                <form action="{{ route('tools.finishMaintenance', $tool->id) }}"
                    method="POST"
                    class="inline">
                    @csrf
                    <button class="text-green-600 hover:text-green-800">
                        Selesai Maintenance
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

{{-- ================= MODAL TAMBAH BARANG ================= --}}
<div id="tambahBarangModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white w-11/12 max-w-xl rounded-xl shadow-xl p-6 relative">

        <form action="{{ route('tools.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Tambah Barang</h2>
                <button type="button"
                        id="closeTambahBarang"
                        class="text-gray-500 hover:text-gray-700 text-xl">
                    ✕
                </button>
            </div>

            <!-- Form Content -->
            <div class="space-y-4">

                <input type="text"
                       name="toolkit_name"
                       placeholder="Nama Barang"
                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">

                <select name="category"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    <option value="">Pilih Kategori</option>
                    <option value="Alat Listrik">Alat Listrik</option>
                    <option value="Alat Manual">Perkakas</option>
                </select>

                <input type="text"
                       name="serial_number"
                       placeholder="No Seri"
                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">

                <input type="file"
                       name="image"
                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">

            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 mt-6">
                <button type="button"
                        id="cancelTambahBarang"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>

                <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                    Simpan
                </button>
            </div>

        </form>

    </div>
</div>

{{-- ================= MODAL EDIT BARANG ================= --}}
<div id="editBarangModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white w-11/12 max-w-xl rounded-xl shadow-xl p-6 relative">

        <form id="editBarangForm" method="POST">
            @csrf
            @method('PUT')

            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Edit Barang</h2>
                <button type="button"
                        id="closeEditModal"
                        class="text-gray-500 hover:text-gray-700 text-xl">
                    ✕
                </button>
            </div>

            <!-- Form -->
            <div class="space-y-4">

                <input type="text"
                       name="toolkit_name"
                       id="editName"
                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">

                <select name="category"
                        id="editCategory"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    <option value="Alat Listrik">Alat Listrik</option>
                    <option value="Perkakas">Perkakas</option>
                </select>

                <input type="text"
                       name="serial_number"
                       id="editSerial"
                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">

            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 mt-6">
                <button type="button"
                        id="cancelEditModal"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Batal
                </button>

                <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Update
                </button>
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
document.addEventListener('DOMContentLoaded', function () {

    /* =========================
       TAMBAH BARANG MODAL
    ========================== */

    const tambahModal = document.getElementById('tambahBarangModal');
    const openTambahBtn = document.getElementById('openTambahBarang');
    const closeTambahBtn = document.getElementById('closeTambahBarang');
    const cancelTambahBtn = document.getElementById('cancelTambahBarang');

    if (openTambahBtn) {
        openTambahBtn.addEventListener('click', function () {
            tambahModal.classList.remove('hidden');
            tambahModal.classList.add('flex');
        });
    }

    function closeTambah() {
        tambahModal.classList.add('hidden');
        tambahModal.classList.remove('flex');
    }

    closeTambahBtn?.addEventListener('click', closeTambah);
    cancelTambahBtn?.addEventListener('click', closeTambah);

    tambahModal?.addEventListener('click', function (e) {
        if (e.target === tambahModal) {
            closeTambah();
        }
    });


    /* =========================
       EDIT BARANG MODAL
    ========================== */

    const editModal = document.getElementById('editBarangModal');
    const editForm = document.getElementById('editBarangForm');
    const editName = document.getElementById('editName');
    const editCategory = document.getElementById('editCategory');
    const editSerial = document.getElementById('editSerial');
    const closeEditBtn = document.getElementById('closeEditModal');
    const cancelEditBtn = document.getElementById('cancelEditModal');

    document.querySelectorAll('.editBtn').forEach(button => {

        button.addEventListener('click', function () {

            editName.value = this.dataset.name;
            editCategory.value = this.dataset.category;
            editSerial.value = this.dataset.serial;

            editForm.action = '/data-tools/' + this.dataset.id;

            editModal.classList.remove('hidden');
            editModal.classList.add('flex');
        });

    });

    function closeEdit() {
        editModal.classList.add('hidden');
        editModal.classList.remove('flex');
    }

    closeEditBtn?.addEventListener('click', closeEdit);
    cancelEditBtn?.addEventListener('click', closeEdit);

    editModal?.addEventListener('click', function (e) {
        if (e.target === editModal) {
            closeEdit();
        }
    });


    /* =========================
       ESC CLOSE (GLOBAL)
    ========================== */

    document.addEventListener('keydown', function (e) {
        if (e.key === "Escape") {
            closeTambah();
            closeEdit();
        }
    });

});
</script>

@endsection
