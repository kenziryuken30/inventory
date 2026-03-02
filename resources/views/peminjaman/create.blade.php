 @extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6"
     x-data="{ openTools: false }">

    <h2 class="text-xl font-semibold mb-4">Peminjaman Tools</h2>

    <form action="{{ route('peminjaman.store') }}" method="POST">
        @csrf

        {{-- ================= HEADER FORM ================= --}}
        <div class="bg-white shadow rounded-xl p-6 mb-6">

            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Peminjam</label>
                    <input type="text"
                           name="borrower_name"
                           required
                           class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                           placeholder="Masukkan nama peminjam">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal</label>
                    <input type="date"
                           name="date"
                           value="{{ date('Y-m-d') }}"
                           required
                           class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <input type="text" name="client_name"
                       class="border rounded-lg px-3 py-2"
                       placeholder="Nama Client">

                <input type="text" name="project"
                       class="border rounded-lg px-3 py-2"
                       placeholder="Proyek">

                <input type="text" name="purpose"
                       class="border rounded-lg px-3 py-2"
                       placeholder="Keperluan">
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                    Save
                </button>

                <a href="{{ route('peminjaman.index') }}"
                    class="bg-gray-500 text-white px-5 py-2 rounded-lg shadow hover:bg-gray-600 transition">
                    Kembali
                </a>
            </div>

        </div>

        {{-- ================= DAFTAR ALAT ================= --}}
        <div class="bg-white shadow rounded-xl p-6">

            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold">Daftar Alat yang Dipinjam</h3>

                <button type="button"
                        @click="openTools = true"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    + Pilih Tools
                </button>
            </div>

            <table class="w-full text-sm border rounded-lg overflow-hidden"
                   id="tableSelectedTools">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2">No</th>
                        <th class="p-2">Image</th>
                        <th class="p-2">Nama Tool</th>
                        <th class="p-2">No Seri</th>
                        <th class="p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </form>

    {{-- ================= MODAL TOOLS ================= --}}
<div x-show="openTools"
     x-transition
     class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

    <div class="bg-white w-11/12 max-w-6xl rounded-2xl shadow-2xl">

        {{-- HEADER --}}
        <div class="flex justify-between items-center px-8 py-5 border-b">
            <h3 class="text-lg font-semibold text-gray-800">
                Pilih Tools Tersedia
            </h3>

            <button @click="openTools = false"
                    class="text-gray-400 hover:text-gray-700 text-xl">
                ✕
            </button>
        </div>

        {{-- BODY --}}
        <div class="p-8">

            <input type="text"
                   id="searchTools"
                   class="w-full border border-gray-300 rounded-xl px-4 py-3 mb-6 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                   placeholder="Cari nama tools atau no seri...">

            <div class="overflow-y-auto max-h-[400px] border rounded-xl">

                <table class="w-full text-sm">
                    <thead class="bg-gray-100 text-gray-600 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3 w-10"></th>
                            <th class="px-6 py-3 text-left">Image</th>
                            <th class="px-6 py-3 text-left">Nama</th>
                            <th class="px-6 py-3 text-left">No Seri</th>
                        </tr>
                    </thead>

                    <tbody id="toolsTable" class="divide-y divide-gray-200">
                        @foreach ($serials as $serial)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <input type="checkbox"
                                       class="tool-checkbox w-4 h-4 text-blue-600 rounded"
                                       value="{{ $serial->id }}"
                                       data-id="{{ $serial->id }}"
                                       data-name="{{ $serial->toolkit->toolkit_name }}"
                                       data-serial="{{ $serial->serial_number }}"
                                       data-image="{{ $serial->toolkit->image }}">
                            </td>

                            <td class="px-6 py-4">
                                <img src="{{ $serial->toolkit->image 
                                    ? asset('storage/'.$serial->toolkit->image)
                                    : asset('images/no-image.png') }}"
                                     class="w-14 h-14 object-contain rounded-lg border cursor-pointer preview-image">
                            </td>

                            <td class="px-6 py-4">
                                {{ $serial->toolkit->toolkit_name }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $serial->serial_number }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

        </div>

        {{-- FOOTER --}}
        <div class="flex justify-end gap-4 px-8 py-5 border-t bg-gray-50 rounded-b-2xl">

            <button @click="openTools = false"
                    class="px-5 py-2 bg-gray-200 rounded-xl hover:bg-gray-300 transition">
                Batal
            </button>

            <button type="button"
                    id="btnAddTool"
                    class="px-6 py-2 bg-blue-600 text-white rounded-xl shadow hover:bg-blue-700 transition">
                + Tambahkan
            </button>

        </div>

    </div>
</div>

    {{-- ================= IMAGE PREVIEW ================= --}}
    <div id="imagePreviewModal"
         class="fixed inset-0 bg-black/70 hidden z-50 flex items-center justify-center">

        <div class="relative">
            <button id="closePreview"
                    class="absolute -top-8 right-0 text-white text-2xl">
                ✕
            </button>

            <img id="previewImage"
                 src=""
                 class="max-h-[90vh] max-w-[90vw] rounded shadow-lg">
        </div>
    </div>

</div>
@endsection


{{-- SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    let counter = 1;

    document.getElementById('btnAddTool').addEventListener('click', function () {

        const selected = document.querySelectorAll('.tool-checkbox:checked');
        if (selected.length === 0) return alert('Pilih minimal satu tool');

        selected.forEach(function (checkbox) {

            const id = checkbox.dataset.id;
            const name = checkbox.dataset.name;
            const serial = checkbox.dataset.serial;
            const image = checkbox.dataset.image;

            if (document.getElementById('row-' + id)) return;

            const imagePath = image ? `/storage/${image}` : `/images/no-image.png`;

            const row = `
                <tr id="row-${id}" class="border-b">
                    <td class="p-2">${counter++}</td>
                    <td class="p-2">
                        <img src="${imagePath}" 
                             class="w-12 cursor-pointer preview-image">
                    </td>
                    <td class="p-2">${name}</td>
                    <td class="p-2">${serial}</td>
                    <td class="p-2">
                        <button type="button"
                                class="text-red-600"
                                onclick="document.getElementById('row-${id}').remove()">
                            Hapus
                        </button>
                        <input type="hidden" name="serial_ids[]" value="${id}">
                    </td>
                </tr>
            `;

            document.querySelector('#tableSelectedTools tbody')
                .insertAdjacentHTML('beforeend', row);

            checkbox.checked = false;
        });
    });

    // SEARCH
    document.getElementById('searchTools').addEventListener('keyup', function() {
        let keyword = this.value.toLowerCase();
        document.querySelectorAll('#toolsTable tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(keyword) ? '' : 'none';
        });
    });

    // IMAGE PREVIEW
    const modal = document.getElementById('imagePreviewModal');
    const previewImage = document.getElementById('previewImage');

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('preview-image')) {
            previewImage.src = e.target.src;
            modal.classList.remove('hidden');
        }
    });

    document.getElementById('closePreview').onclick = () => {
        modal.classList.add('hidden');
        previewImage.src = "";
    };

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
            previewImage.src = "";
        }
    });
});
</script>