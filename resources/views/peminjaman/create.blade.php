@extends('layouts.app')

@section('content')
<div class="w-full min-h-screen flex flex-col">
    {{-- Header Page --}}
    <div class="flex justify-between items-end mb-6">
        <div>
            <h2 class="text-3xl font-bold text-[#1CA7B6] tracking-tight">Peminjaman Tools</h2>
            <p class="text-sm text-gray-500 mt-1">Proses peminjaman alat dan kelola daftar tools</p>
        </div>
        <a href="{{ route('peminjaman.index') }}"
            class="bg-[#E5E7EB] hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center shadow-sm">
            <span class="mr-1">←</span> Kembali
        </a>
    </div>

    <div id="notifWrap" class="hidden mb-5">
        <div id="notifBox"
            class="relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border">
            <div id="notifIcon" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"></div>
            <p id="notifText" class="text-sm font-medium"></p>
            <button id="notifClose" class="ml-auto flex-shrink-0 opacity-50 hover:opacity-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div id="notifBar" class="absolute bottom-0 left-0 h-1 rounded-b-2xl" style="width:100%"></div>
        </div>
    </div>


    <form method="POST" action="{{ route('peminjaman.store') }}" id="formPeminjaman">
        @csrf
        {{-- Main Card --}}
        <div class="bg-[#F9FAFB] rounded-3xl shadow-xl p-8 border border-gray-100 space-y-8">

            <div>
                {{-- Dihapus border-b dan pb-3 agar garis hilang --}}
                <h3 class="text-lg font-bold text-gray-800 mb-6">Proses peminjaman Alat</h3>

                <div class="space-y-6">
                    {{-- ROW 1 --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Peminjam <span class="text-red-500">*</span>
                            </label>
                            <select name="employee_id" required
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none">

                                <option value="">-- Pilih Karyawan --</option>

                                @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->full_name }}
                                </option>
                                @endforeach

                            </select>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none">
                            </div>

                            <div></div>
                        </div>

                        {{-- ROW 2 --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama client</label>
                                <input type="text" name="client_name" placeholder="Masukan nama klien"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Proyek</label>
                                <input type="text" name="project" placeholder="Masukan Keterangan"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keperluan</label>
                                <input type="text" name="purpose" placeholder="Masukan Keperluan"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section Daftar Alat --}}
                <div class="space-y-0 mt-10">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800">Daftar Alat yang Dipinjam</h3>
                        <button type="button" id="openToolsBtn"
                            class="text-white px-5 py-2 rounded-lg text-xs font-bold shadow-md hover:opacity-90 transition-all"
                            style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                            + Pilih Tools
                        </button>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <table class="w-full">
                            <thead>
                                <tr class="text-white text-xs uppercase tracking-wider"
                                    style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                                    <th class="py-4 px-6 font-semibold text-center">No</th>
                                    <th class="py-4 px-6 font-semibold text-center">Foto</th>
                                    <th class="py-4 px-6 font-semibold text-left">Nama Tool</th>
                                    <th class="py-4 px-6 font-semibold text-center">No Seri</th>
                                    <th class="py-4 px-6 font-semibold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableSelectedTools" class="divide-y divide-gray-100 text-sm">
                                <tr id="emptyRow">
                                    <td colspan="5" class="py-12 text-center text-gray-400 italic">
                                        Belum ada tools yang dipilih
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Save Button Row --}}
                <div class="pt-8 border-t border-gray-200 flex justify-end">
                    <button type="submit"
                        class="text-white px-10 py-2.5 rounded-xl font-bold shadow-md hover:opacity-90 transition-all duration-200 tracking-wide"
                        style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                        Save Transaksi
                    </button>
                </div>
            </div>
    </form>
</div>

{{-- ================= MODAL TOOLS ================= --}}
<div id="toolsModal"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">

    <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl relative max-h-[90vh] overflow-hidden flex flex-col">

        {{-- Modal Header (Gradasi) --}}
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center text-white"
            style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
            <h3 class="text-lg font-bold">
                Pilih Tools Tersedia
            </h3>
            <button type="button" id="closeToolsBtn"
                class="text-white/80 hover:text-white text-2xl transition">
                ✕
            </button>
        </div>

        <div class="p-6 flex-1 overflow-auto">

            {{-- Search Input --}}
            <div class="mb-5">
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" id="searchTools" placeholder="Cari nama tools..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3 shadow-inner focus:ring-2 focus:ring-[#1CA7B6] focus:outline-none transition text-sm">
                </div>
            </div>

            {{-- Table Modal --}}
            <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-gray-50">
                        <tr class="text-gray-600 border-b border-gray-200">
                            <th class="py-3 px-4 text-center w-12">
                                <input type="checkbox" id="selectAllTools" class="w-4 h-4 accent-[#1CA7B6] rounded border-gray-300">
                            </th>
                            <th class="py-3 px-4 text-left font-semibold">Nama Tools</th>
                            <th class="py-3 px-4 text-center font-semibold">No Seri</th>
                            <th class="py-3 px-4 text-center w-24 font-semibold">Image</th>
                        </tr>
                    </thead>

                    <tbody id="toolsTable" class="bg-white divide-y divide-gray-100">
                        @foreach ($serials as $serial)
                        <tr class="hover:bg-teal-50/50 transition cursor-pointer tool-row"
                            data-name="{{ strtolower($serial->toolkit->toolkit_name) }}">
                            <td class="py-3 px-4 text-center">
                                <input type="checkbox"
                                    class="tool-checkbox w-4 h-4 accent-[#1CA7B6] rounded border-gray-300"
                                    value="{{ $serial->id }}"
                                    data-name="{{ $serial->toolkit->toolkit_name }}"
                                    data-serial="{{ $serial->serial_number }}"
                                    data-image="{{ $serial->toolkit->image }}">
                            </td>
                            <td class="py-3 px-4 font-medium text-gray-800">
                                {{ $serial->toolkit->toolkit_name }}
                            </td>
                            <td class="py-3 px-4 text-center text-gray-500 font-mono text-xs">
                                {{ $serial->serial_number }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <img src="{{ $serial->toolkit->image 
                                    ? asset('storage/'.$serial->toolkit->image)
                                    : asset('images/no-image.png') }}"
                                    class="w-10 h-10 object-contain mx-auto rounded-lg shadow-sm preview-image cursor-pointer hover:scale-110 transition">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50">
            <button type="button" id="cancelToolsBtn"
                class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-100 transition font-medium text-sm shadow-sm">
                Batal
            </button>

            <button type="button" id="btnAddTool"
                class="px-6 py-2.5 text-white rounded-xl hover:opacity-90 transition font-medium text-sm shadow-md flex items-center gap-2"
                style="background: linear-gradient(180deg, #5FD0DF, #1CA7B6);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambahkan
            </button>
        </div>
    </div>
</div>

{{-- ================= IMAGE PREVIEW MODAL ================= --}}
<div id="imagePreviewModal"
    class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center z-[9999] p-4">

    <div class="relative max-w-4xl w-full">
        <button id="closePreview"
            class="absolute -top-10 right-0 text-white text-3xl hover:text-[#5FD0DF] transition">
            ✕
        </button>
        <img id="previewImage"
            class="max-h-[85vh] max-w-full mx-auto rounded-xl shadow-2xl">
    </div>
</div>

@endsection

{{-- ================= CUSTOM STYLE ================= --}}
<style>
    .shadow-inner {
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05);
    }

    input::placeholder {
        color: #9CA3AF;
        font-weight: 400;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #tableSelectedTools tr[id^="row-"] {
        animation: fadeIn 0.3s ease-out;
    }
</style>

{{-- ================= SCRIPT ================= --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {

        let selectedTools = new Set();

        // Elements
        const toolsModal = document.getElementById('toolsModal');
        const openToolsBtn = document.getElementById('openToolsBtn');
        const closeToolsBtn = document.getElementById('closeToolsBtn');
        const cancelToolsBtn = document.getElementById('cancelToolsBtn');
        const btnAddTool = document.getElementById('btnAddTool');
        const selectAllCheckbox = document.getElementById('selectAllTools');

        const searchInput = document.getElementById('searchTools');
        const modalTable = document.getElementById('toolsTable');

        const previewModal = document.getElementById('imagePreviewModal');
        const previewImage = document.getElementById('previewImage');
        const closePreview = document.getElementById('closePreview');

        // ===== NOTIF SYSTEM =====
        const notifWrap = document.getElementById('notifWrap');
        const notifBox = document.getElementById('notifBox');
        const notifIcon = document.getElementById('notifIcon');
        const notifText = document.getElementById('notifText');
        const notifBar = document.getElementById('notifBar');
        const notifClose = document.getElementById('notifClose');
        let notifTimer = null;

        function showNotif(message, type) {
            if (notifTimer) clearTimeout(notifTimer);
            notifWrap.classList.remove('hidden', 'hiding');

            if (type === 'success') {
                notifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-emerald-50 border-emerald-200 text-emerald-800';
                notifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-emerald-100';
                notifIcon.innerHTML = '<svg class="w-4.5 h-4.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>';
                notifBar.style.background = '#34d399';
            } else {
                notifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-red-50 border-red-200 text-red-800';
                notifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-red-100';
                notifIcon.innerHTML = '<svg class="w-4.5 h-4.5 text-red-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
                notifBar.style.background = '#f87171';
            }

            notifText.textContent = message;
            notifBar.style.transition = 'none';
            notifBar.style.width = '100%';

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    notifBar.style.transition = 'width 3.5s linear';
                    notifBar.style.width = '0%';
                });
            });

            notifTimer = setTimeout(() => hideNotif(), 3500);
        }

        // ===== MODAL FUNCTIONS =====
        function openModal() {
            toolsModal.classList.remove('hidden');
            toolsModal.classList.add('flex');
            document.body.style.overflow = 'hidden';

            // 🔥 Hide yang sudah dipilih
            document.querySelectorAll('.tool-checkbox').forEach(cb => {
                const row = cb.closest('tr');
                if (selectedTools.has(cb.value)) {
                    row.style.display = 'none';
                } else {
                    row.style.display = '';
                }
            });
        }

        function closeModal() {
            toolsModal.classList.add('hidden');
            toolsModal.classList.remove('flex');
            document.body.style.overflow = '';
            // Reset checkbox & search
            document.querySelectorAll('.tool-checkbox').forEach(cb => cb.checked = false);
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
            if (searchInput) searchInput.value = '';
            modalTable.querySelectorAll('tr').forEach(row => {
                const checkbox = row.querySelector('.tool-checkbox');
                if (!checkbox) return;

                if (selectedTools.has(checkbox.value)) {
                    row.style.display = 'none';
                } else {
                    row.style.display = '';
                }
            });
        }

        openToolsBtn?.addEventListener('click', openModal);
        closeToolsBtn?.addEventListener('click', closeModal);
        cancelToolsBtn?.addEventListener('click', closeModal);

        // Close modal on outside click
        toolsModal?.addEventListener('click', function(e) {
            if (e.target === toolsModal) closeModal();
        });

        // ===== SELECT ALL =====
        selectAllCheckbox?.addEventListener('change', function() {
            const visibleCheckboxes = document.querySelectorAll('.tool-checkbox');
            visibleCheckboxes.forEach(cb => {
                if (cb.closest('tr').style.display !== 'none') {
                    cb.checked = this.checked;
                }
            });
        });

        // ===== LIVE SEARCH =====
        searchInput?.addEventListener('input', function() {
            const keyword = this.value.toLowerCase().trim();

            modalTable.querySelectorAll('tr').forEach(row => {
                const checkbox = row.querySelector('.tool-checkbox');
                if (!checkbox) return;

                const name = row.dataset.name || '';
                const id = checkbox.value;

                // 🔥 cek 2 kondisi: search + sudah dipilih
                if (selectedTools.has(id)) {
                    row.style.display = 'none';
                } else if (name.includes(keyword)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // ===== ROW CLICK TOGGLE =====
        modalTable?.addEventListener('click', function(e) {
            const row = e.target.closest('tr');
            if (!row) return;

            if (e.target.type !== 'checkbox' && !e.target.classList.contains('preview-image')) {
                const checkbox = row.querySelector('.tool-checkbox');
                if (checkbox) checkbox.checked = !checkbox.checked;
            }
        });

        // ===== TAMBAH TOOL KE TABEL UTAMA =====
        btnAddTool?.addEventListener('click', function() {
            const selected = document.querySelectorAll('.tool-checkbox:checked');

            if (selected.length === 0) {
                alert('Pilih minimal satu tool');
                return;
            }

            const tableBody = document.getElementById('tableSelectedTools');
            const emptyRow = document.getElementById('emptyRow');

            // Hapus placeholder "Belum ada tools"
            if (emptyRow) {
                emptyRow.remove();
            }

            let addedCount = 0;

            selected.forEach(checkbox => {
                const id = checkbox.value;

                selectedTools.add(id);

                const name = checkbox.dataset.name;
                const serial = checkbox.dataset.serial;
                const image = checkbox.dataset.image;

                // Cegah duplikat
                if (document.getElementById('row-' + id)) return;

                // Hitung nomor urut
                const currentRows = tableBody.querySelectorAll('tr').length;
                const rowCount = currentRows + 1;

                const imagePath = image ? `/storage/${image}` : `/images/no-image.png`;

                // Template baris baru (Sesuai style tabel utama)
                const rowHtml = `
                <tr id="row-${id}" class="hover:bg-gray-50 transition">
                    <td class="text-center py-4 px-6 text-gray-600 row-number">${rowCount}</td>
                    <td class="text-center py-3 px-6">
                        <img src="${imagePath}"
                             class="w-12 h-12 object-contain mx-auto rounded-lg shadow-sm preview-image cursor-pointer hover:scale-110 transition"
                             alt="${name}">
                    </td>
                    <td class="py-3 px-6 font-semibold text-gray-800">${name}</td>
                    <td class="text-center py-3 px-6 text-gray-500">${serial}</td>
                    <td class="text-center py-3 px-6">
                        <button type="button"
                                onclick="removeRow(this)"
                                class="bg-red-50 text-red-500 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-100 transition">
                            Hapus
                        </button>
                        <input type="hidden" name="serial_ids[]" value="${id}">
                    </td>
                </tr>
            `;

                tableBody.insertAdjacentHTML('beforeend', rowHtml);
                addedCount++;

                // Sembunyikan tool yang sudah dipilih di modal
                checkbox.closest('tr').style.display = 'none';
                checkbox.checked = false;
            });

            if (addedCount > 0) {
                updateRowNumbers();
            }

            closeModal();
        });

        // ===== REMOVE ROW =====
        window.removeRow = function(btn) {

            if (!confirm('Yakin ingin menghapus tool ini?')) {
                return; // batal hapus
            }

            const row = btn.closest('tr');
            const id = row.id.replace('row-', '');
            selectedTools.delete(id);

            // Tampilkan kembali di modal
            const modalRow = document.querySelector(`.tool-checkbox[value="${id}"]`)?.closest('tr');
            if (modalRow) modalRow.style.display = '';

            row.remove();
            updateRowNumbers();

            // Empty state kalau kosong
            const tableBody = document.getElementById('tableSelectedTools');
            if (tableBody.querySelectorAll('tr').length === 0) {
                tableBody.innerHTML = `
        <tr id="emptyRow">
            <td colspan="5" class="py-12 text-center text-gray-400 italic text-sm">
                Belum ada tools yang dipilih
            </td>
        </tr>
        `;
            }
        };

        // ===== UPDATE ROW NUMBERS =====
        function updateRowNumbers() {
            const rows = document.querySelectorAll('#tableSelectedTools tr[id^="row-"]');
            rows.forEach((row, index) => {
                const numCell = row.querySelector('.row-number');
                if (numCell) numCell.textContent = index + 1;
            });
        }

        // ===== IMAGE PREVIEW =====
        document.addEventListener('click', function(e) {
            const img = e.target.closest('.preview-image');
            if (!img) return;

            previewImage.src = img.src;
            previewModal.classList.remove('hidden');
            previewModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        });

        closePreview?.addEventListener('click', function() {
            previewModal.classList.add('hidden');
            previewModal.classList.remove('flex');
            document.body.style.overflow = '';
        });

        previewModal?.addEventListener('click', function(e) {
            if (e.target === previewModal) {
                previewModal.classList.add('hidden');
                previewModal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        });

        // ===== KEYBOARD SHORTCUTS =====
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (!previewModal.classList.contains('hidden')) {
                    previewModal.classList.add('hidden');
                    previewModal.classList.remove('flex');
                    document.body.style.overflow = '';
                } else if (!toolsModal.classList.contains('hidden')) {
                    closeModal();
                }
            }
        });
    });
</script>