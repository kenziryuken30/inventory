@extends('layouts.app')

@section('content')

<div class="flex justify-between items-center mb-6">

<div>    
    <h2 class="text-2xl font-bold text-[#268397]">
        Peminjaman Tools
    </h2>
    <p class="text-sm text-gray-500">
        Proses peminjaman tools dan kelola daftar tools yang dipinjam
    </p>
</div>

    <a href="{{ route('peminjaman.index') }}"
       class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow">
        ← Kembali
    </a>
</div>


    <form method="POST" action="{{ route('peminjaman.store') }}" class="space-y-6">
    @csrf

<div class="bg-gray-100 rounded-2xl shadow-xl p-6 space-y-8">

{{-- ================= HEADER ================= --}}
<div>

    <div class="grid grid-cols-2 gap-6 mb-4">

        <div>
            <label class="block text-sm font-medium mb-1">
                Nama Peminjam
            </label>
            <input type="text"
                   name="borrower_name"
                   class="w-full px-4 py-2 rounded-lg border shadow-sm focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">
                Tanggal
            </label>
            <input type="date"
                   name="date"
                   value="{{ date('Y-m-d') }}"
                   class="w-full px-4 py-2 rounded-lg border shadow-sm">
        </div>

    </div>

    <div class="grid grid-cols-3 gap-6">

    <div>
        <label class="block text-sm font-medium mb-1">
            Client
        </label>
        <input type="text"
               name="client_name"
               class="w-full px-4 py-2 rounded-lg border shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Proyek
        </label>
        <input type="text"
               name="project"
               class="w-full px-4 py-2 rounded-lg border shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Keperluan
        </label>
        <input type="text"
               name="purpose"
               class="w-full px-4 py-2 rounded-lg border shadow-sm">
    </div>

</div>

</div>




{{-- ================= DAFTAR ALAT ================= --}}
    <div class="bg-gray-100 rounded-2xl shadow-md p-6 mb-6">

        <div class="flex justify-between items-center mb-4">

            <h3 class="font-semibold">
                Daftar Tools
            </h3>

            <button type="button"
                    id="openToolsBtn"
                    class="bg-gray-100 text-gray-700
           px-6 py-2.5 rounded-xl
           shadow-[0_4px_12px_rgba(0,0,0,0.15)]
           hover:bg-gray-200
           hover:shadow-[0_6px_18px_rgba(0,0,0,0.2)]
           hover:-translate-y-0.5
           transition duration-200">
                + Pilih Tools
            </button>

        </div>

        <div class="rounded-xl overflow-hidden border border-gray-300">

    <table class="w-full text-sm">

        {{-- HEADER --}}
        <thead>
            <tr class="text-white bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)]">
                <th class="py-3 text-center">NO</th>
                <th class="py-3 text-center">Image</th>
                <th class="py-3 text-center">Nama Tools</th>
                <th class="py-3 text-center">No Seri</th>
                <th class="py-3 text-center">Aksi</th>
            </tr>
        </thead>

        {{-- BODY --}}
        <tbody id="tableSelectedTools">
            @forelse ($selectedTools ?? [] as $index => $tool)
            <tr class="border-b hover:bg-gray-50">

                <td class="text-center py-4">
                    {{ $index + 1 }}
                </td>

                <td class="text-center">
                    @if($tool->image)
                        <img src="{{ asset('storage/'.$tool->image) }}"
                             class="w-12 h-12 object-contain mx-auto rounded preview-image cursor-pointer">
                    @endif
                </td>

                <td class="text-center">
                    {{ $tool->toolkit_name }}
                </td>

                <td class="text-center">
                    {{ $tool->serial_number }}
                </td>

                <td class="text-center">
                    <button type="button"
                            class="bg-red-100 text-red-600 px-3 py-1 rounded-lg text-sm hover:bg-red-200">
                        Hapus
                    </button>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-gray-400 py-6">
                    Belum ada tools
                </td>
            </tr>
            @endforelse
        </tbody>

    </table>

</div>

    </div>

    <div class="flex justify-end">
        <button type="submit"
            class="bg-gray-100 text-gray-700
           px-6 py-2.5 rounded-xl
           shadow-[0_4px_12px_rgba(0,0,0,0.15)]
           hover:bg-gray-200
           hover:shadow-[0_6px_18px_rgba(0,0,0,0.2)]
           hover:-translate-y-0.5
           transition duration-200">
            Save Transaksi
        </button>
    </div>

</form>

</div>


{{-- ================= MODAL TOOLS ================= --}}
<div id="toolsModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white w-11/12 max-w-3xl rounded-2xl shadow-2xl relative">

        <button type="button"
                id="closeToolsBtn"
                class="absolute top-4 right-5 text-gray-400 hover:text-gray-600 text-xl">
            ✕
        </button>

        <div class="px-8 pt-8 pb-4">
            <h3 class="text-lg font-semibold text-gray-700">
                Tools Tersedia
            </h3>
        </div>

        <div class="px-8 pb-6">

            {{-- SEARCH --}}
            <div class="bg-gradient-to-r from-teal-400 to-teal-500 p-3 rounded-xl shadow-md mb-6">
                <input type="text"
                       id="searchTools"
                       placeholder="Cari nama Tools"
                       class="w-full bg-white rounded-lg px-4 py-2 outline-none">
            </div>

            {{-- TABLE --}}

                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm max-h-[350px] overflow-y-auto">

                    <table class="w-full text-sm">

                        <thead>
                            <tr class="text-white bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)]">
                                <th class="py-3 px-4 w-10"></th>
                                <th class="py-3 px-4 text-left">Nama Tools</th>
                                <th class="py-3 px-4 text-center">No Seri</th>
                                <th class="py-3 px-4 text-center">Image</th>
                            </tr>
                        </thead>

                        <tbody id="toolsTable" class="bg-white divide-y divide-gray-200">

                            @foreach ($serials as $serial)
                            <tr class="hover:bg-gray-50 transition">

                                <td class="py-3 px-4 text-center">
                                    <input type="checkbox"
                                        class="tool-checkbox w-4 h-4 accent-teal-600"
                                        value="{{ $serial->id }}"
                                        data-name="{{ $serial->toolkit->toolkit_name }}"
                                        data-serial="{{ $serial->serial_number }}"
                                        data-image="{{ $serial->toolkit->image }}">
                                </td>

                                <td class="py-3 px-4 font-medium text-gray-700">
                                    {{ $serial->toolkit->toolkit_name }}
                                </td>

                                <td class="py-3 px-4 text-center text-gray-600">
                                    {{ $serial->serial_number }}
                                </td>

                                <td class="py-3 px-4 text-center">
                                    <img src="{{ $serial->toolkit->image 
                                        ? asset('storage/'.$serial->toolkit->image)
                                        : asset('images/no-image.png') }}"
                                         class="w-12 h-12 object-contain mx-auto rounded shadow-sm preview-image cursor-pointer">
                                </td>

                            </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

                {{-- FOOTER --}}
                <div class="flex justify-end gap-4 px-0 pt-6">

                    <button type="button"
                            id="cancelToolsBtn"
                            class="px-5 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                        Batal
                    </button>

                    <button type="button"
                            id="btnAddTool"
                            class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition shadow">
                        + Tambahkan
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>

    
{{-- ================= IMAGE PREVIEW ================= --}}
<div id="imagePreviewModal"
     class="fixed inset-0 bg-black/70 hidden items-center justify-center z-[9999]">

    <div class="relative">
        <button id="closePreview"
                class="absolute -top-8 right-0 text-white text-2xl">
            ✕
        </button>

        <img id="previewImage"
             class="max-h-[90vh] max-w-[90vw] rounded shadow-lg">
    </div>
</div>
@endsection


{{-- ================= SCRIPT ================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const toolsModal = document.getElementById('toolsModal');
    const openToolsBtn = document.getElementById('openToolsBtn');
    const closeToolsBtn = document.getElementById('closeToolsBtn');
    const cancelToolsBtn = document.getElementById('cancelToolsBtn');
    const btnAddTool = document.getElementById('btnAddTool');

    const searchInput = document.getElementById('searchTools');
    const modalTable = document.getElementById('toolsTable');

    const previewModal = document.getElementById('imagePreviewModal');
    const previewImage = document.getElementById('previewImage');
    const closePreview = document.getElementById('closePreview');

    // ===== MODAL =====
    function openModal() {
        toolsModal.classList.remove('hidden');
        toolsModal.classList.add('flex');
    }

    function closeModal() {
        toolsModal.classList.add('hidden');
        toolsModal.classList.remove('flex');
    }

    openToolsBtn?.addEventListener('click', openModal);
    closeToolsBtn?.addEventListener('click', closeModal);
    cancelToolsBtn?.addEventListener('click', closeModal);

    // ===== LIVE SEARCH =====
    searchInput?.addEventListener('input', function () {
        let keyword = this.value.toLowerCase();

        modalTable.querySelectorAll('tr').forEach(row => {
            row.style.display =
                row.innerText.toLowerCase().includes(keyword)
                ? ''
                : 'none';
        });
    });

    // ===== TAMBAH TOOL =====
    btnAddTool?.addEventListener('click', function () {

        const selected = document.querySelectorAll('.tool-checkbox:checked');

        if (selected.length === 0) {
            alert('Pilih minimal satu tool');
            return;
        }

        const tableBody = document.getElementById('tableSelectedTools');

        // Hapus row "Belum ada tools" jika ada
        const emptyRow = tableBody.querySelector('td[colspan="5"]');
        if (emptyRow) {
            emptyRow.closest('tr').remove();
        }

        selected.forEach(checkbox => {

            const id = checkbox.value;
            const name = checkbox.dataset.name;
            const serial = checkbox.dataset.serial;
            const image = checkbox.dataset.image;

            if (document.getElementById('row-' + id)) return;

            // Hitung nomor otomatis
            const rowCount = tableBody.querySelectorAll('tr').length + 1;

            const imagePath = image
                ? `/storage/${image}`
                : `/images/no-image.png`;

            const row = `
                <tr id="row-${id}" class="border-b">
                    <td class="text-center py-3">${rowCount}</td>
                    <td class="text-center">
                        <img src="${imagePath}"
                             class="w-12 h-12 object-contain mx-auto rounded preview-image cursor-pointer">
                    </td>
                    <td class="text-center">${name}</td>
                    <td class="text-center">${serial}</td>
                    <td class="text-center">
                        <button type="button"
                                class="text-red-600"
                                onclick="this.closest('tr').remove()">
                            Hapus
                        </button>
                        <input type="hidden"
                               name="serial_ids[]"
                               value="${id}">
                    </td>
                </tr>
            `;

            tableBody.insertAdjacentHTML('beforeend', row);

            // Sembunyikan tool di modal
            checkbox.closest('tr').style.display = 'none';

            checkbox.checked = false;
        });

        closeModal();
    });

    // ===== IMAGE PREVIEW =====
    document.addEventListener('click', function (e) {

        const img = e.target.closest('.preview-image');
        if (!img) return;

        previewImage.src = img.src;
        previewModal.classList.remove('hidden');
        previewModal.classList.add('flex');
    });

    closePreview?.addEventListener('click', function () {
        previewModal.classList.add('hidden');
        previewModal.classList.remove('flex');
    });

});
</script>