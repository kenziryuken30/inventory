@extends('layouts.app')

@section('content')
    <div class="w-full min-h-screen flex flex-col">
        {{-- Header Page --}}
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-3xl font-bold text-[#5EA6FF] tracking-tight">Peminjaman Tools</h2>
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
                <div id="notifBar" class="absolute bottom-0 left-0 h-1 rounded-b-2xl" style="width:0%"></div>
            </div>
        </div>

        @if ($errors->any())
    <div style="background:red;color:white;padding:10px;margin-bottom:10px;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <form method="POST" action="{{ route('peminjaman.store') }}" id="formPeminjaman">
            @csrf
            {{-- Main Card --}}
            <div class="bg-[#F9FAFB] rounded-3xl shadow-xl p-8 border border-gray-100 space-y-8">

                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-6">Proses peminjaman Alat</h3>

                    <div class="space-y-6">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Nama Peminjam <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="employee_name" placeholder="Cari nama karyawan..."
                                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm">
                                        <input type="hidden" name="employee_id" id="employee_id">
                                        <input type="hidden" name="employee_name" id="employee_name_hidden">
                                        <div id="employee_suggestions"
                                            class="absolute left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-md mt-1 max-h-48 overflow-y-auto hidden z-50">
                                        </div>
                                    </div>
                                </div>
                                <div class="justify-self-start">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Tanggal <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                        class="w-52 px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none transition">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">

                                <!-- CLIENT -->
                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Client</label>

                                    <input type="text" id="client_name" placeholder="Cari client..."
                                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm focus:ring-2 focus:ring-blue-400">

                                    <input type="hidden" name="client_id" id="client_id">
                                    <input type="hidden" name="client_name" id="client_name_hidden">

                                    <div id="client_suggestions"
                                        class="absolute left-0 right-0 bg-white border rounded-lg shadow-md mt-1 hidden z-50">
                                    </div>
                                </div>

                                <!-- PROJECT -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>

                                    <select id="project" name="project_id" disabled
                                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm focus:ring-2 focus:ring-blue-400">
                                        <option value="">Pilih client terlebih dahulu</option>
                                    </select>

                                    <input type="hidden" name="project_name" id="project_hidden">
                                </div>

                                <!-- KEPERLUAN -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Keperluan</label>

                                    <input type="text" name="purpose" id="purpose" placeholder="Tujuan peminjaman..."
                                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 shadow-sm focus:ring-2 focus:ring-blue-400">
                                </div>

                            </div>
                        </div>

                        {{-- Section Daftar Alat --}}
                        <div class="space-y-0 mt-10">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-bold text-gray-800">Daftar Alat yang Dipinjam</h3>
                                <button type="button" id="openToolsBtn"
                                    class="group inline-flex items-center px-5 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all duration-200 tracking-wide border-2 border-[#5EA6FF] bg-white text-xs text-[#5EA6FF] hover:bg-[#5EA6FF] hover:text-white hover:shadow-blue-500/40 hover:-translate-y-0.5">
                                    <svg class="w-4 h-4 mr-2 transition-transform duration-300 group-hover:rotate-90"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Pilih Tools
                                </button>
                            </div>

                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                                <table class="w-full">
                                    <thead>
                                        <tr class="text-white text-xs uppercase tracking-wider"
                                            style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">
                                            <th class="py-4 px-6 font-semibold text-center">No</th>
                                            <th class="py-4 px-6 font-semibold text-center">Foto</th>
                                            <th class="py-4 px-6 font-semibold text-left">Nama Tool</th>
                                            <th class="py-4 px-6 font-semibold text-center">No Seri</th>
                                            <th class="py-4 px-6 font-semibold text-center w-16">Aksi</th>
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

                        <div class="pt-8 border-t border-gray-200 flex justify-end">
                            <button type="submit" id="submitBtn"
                                class="group inline-flex items-center px-10 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all duration-200 tracking-wide border-2 border-[#5EA6FF] bg-white text-sm text-[#5EA6FF] hover:bg-[#5EA6FF] hover:text-white hover:shadow-blue-500/40 hover:-translate-y-0.5">
                                Save Transaksi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ================= MODAL TOOLS ================= --}}
    <div id="toolsModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl relative max-h-[90vh] overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center text-white"
                style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">
                <h3 class="text-lg font-bold">Pilih Tools Tersedia</h3>
                <button type="button" id="closeToolsBtn"
                    class="text-white/80 hover:text-white text-2xl transition">✕</button>
            </div>
            <div class="p-6 flex-1 overflow-auto">
                <div class="mb-5">
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </span>
                        <input type="text" id="searchTools" placeholder="Cari nama tools..."
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3 shadow-inner focus:ring-2 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none transition text-sm">
                    </div>
                </div>
                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 bg-gray-50">
                            <tr class="text-gray-600 border-b border-gray-200">
                                <th class="py-3 px-4 text-center w-12">
                                    <input type="checkbox" id="selectAllTools"
                                        class="w-4 h-4 accent-[#5EA6FF] rounded border-gray-300">
                                </th>
                                <th class="py-3 px-4 text-left font-semibold">Nama Tools</th>
                                <th class="py-3 px-4 text-center font-semibold">No Seri</th>
                                <th class="py-3 px-4 text-center w-24 font-semibold">Image</th>
                            </tr>
                        </thead>
                        <tbody id="toolsTable" class="bg-white divide-y divide-gray-100">
                            @foreach ($serials as $serial)
                                <tr class="hover:bg-blue-50/30 transition cursor-pointer tool-row"
                                    data-name="{{ strtolower(optional($serial->toolkit)->toolkit_name) }}">
                                    <td class="py-3 px-4 text-center">
                                        <input type="checkbox"
                                            class="tool-checkbox w-4 h-4 accent-[#5EA6FF] rounded border-gray-300"
                                            value="{{ $serial->id }}" data-name="{{ optional($serial->toolkit)->toolkit_name }}"
                                            data-serial="{{ $serial->serial_number }}"
                                            data-image="{{ optional($serial->toolkit)->image }}">
                                    </td>
                                    <td class="py-3 px-4 font-medium text-gray-800">
                                        {{ optional($serial->toolkit)->toolkit_name }}
                                    </td>
                                    <td class="py-3 px-4 text-center text-gray-500 font-mono text-xs">
                                        {{ $serial->serial_number }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <img src="{{ optional($serial->toolkit)->image 
                                        ? asset('storage/' . optional($serial->toolkit)->image) 
                                        : asset('images/no-image.png') }}"
                                        class="w-10 h-10 object-contain mx-auto rounded-lg shadow-sm preview-image cursor-pointer hover:scale-110 transition">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50">
                <button type="button" id="cancelToolsBtn"
                    class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-100 transition font-medium text-sm shadow-sm">
                    Batal
                </button>
                <button type="button" id="btnAddTool"
                    class="group inline-flex items-center px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all duration-200 tracking-wide border-2 border-[#5EA6FF] bg-white text-sm text-[#5EA6FF] hover:bg-[#5EA6FF] hover:text-white hover:shadow-blue-500/40 hover:-translate-y-0.5">
                    <svg class="w-4 h-4 mr-2 transition-transform duration-300 group-hover:rotate-90" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
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
                class="absolute -top-10 right-0 text-white text-3xl hover:text-[#5EA6FF] transition">✕</button>
            <img id="previewImage" class="max-h-[85vh] max-w-full mx-auto rounded-xl shadow-2xl">
        </div>
    </div>

    {{-- ================= KONFIRMASI HAPUS MODAL ================= --}}
    <div id="deleteConfirmModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[9998] p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
            <div class="px-6 py-5 text-center">
                <div class="mx-auto w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Hapus Tool?</h3>
                <p class="text-gray-500 text-sm">Apakah Anda yakin ingin menghapus <span id="deleteToolName"
                        class="font-semibold text-gray-700">-</span> dari daftar peminjaman?</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-center gap-3">
                <button type="button" id="deleteCancelBtn"
                    class="px-6 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-100 transition font-medium text-sm shadow-sm min-w-[100px]">
                    Batal
                </button>
                <button type="button" id="deleteConfirmBtn"
                    class="px-6 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl transition font-medium text-sm shadow-md min-w-[100px]">
                    Hapus
                </button>
            </div>
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

    .btn-delete-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #fef2f2;
        color: #ef4444;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-delete-icon:hover {
        background: #fee2e2;
        transform: scale(1.1);
    }

    .btn-delete-icon:active {
        transform: scale(0.95);
    }
</style>

{{-- ================= SCRIPT ================= --}}
<script>
    
    document.addEventListener('DOMContentLoaded', function () {

        let selectedTools = new Set();
        let pendingDeleteRow = null;
        let pendingDeleteId = null;
        let isSubmitting = false;

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
        const deleteConfirmModal = document.getElementById('deleteConfirmModal');
        const deleteToolName = document.getElementById('deleteToolName');
        const deleteCancelBtn = document.getElementById('deleteCancelBtn');
        const deleteConfirmBtn = document.getElementById('deleteConfirmBtn');
        const formPeminjaman = document.getElementById('formPeminjaman');

        const submitBtn = document.getElementById('submitBtn');

         let employees = [];
    let clients = [];

    fetch('/api/employees')
        .then(res => {
            if (!res.ok) throw new Error('Gagal load data');
            return res.json();
        })
        .then(data => {
            employees = data.data || [];
        })
        .catch(err => {
            console.error(err);
            showNotif('Gagal mengambil data karyawan', 'error');
        });

    fetch('/api/clients')
        .then(res => {
            if (!res.ok) throw new Error('Gagal load data');
            return res.json();
        })
        .then(data => {
            clients = data.data || [];
        })
        .catch(err => {
            console.error(err);
            showNotif('Gagal mengambil data client', 'error');
        });

        // ===== AUTOCOMPLETE EMPLOYEE =====
        const inputEmp = document.getElementById('employee_name');
        const hiddenEmp = document.getElementById('employee_id');
        const box = document.getElementById('employee_suggestions');

        inputEmp.addEventListener('input', function () {
            if (employees.length === 0) {
                box.innerHTML = `<div class="px-4 py-2 text-sm text-gray-400">
                    Data karyawan belum tersedia
                </div>`;
                box.classList.remove('hidden');
                return;
            }

            let value = this.value.toLowerCase().trim();
            box.innerHTML = '';

            // Reset jika kosong
            if (!value) {
                document.getElementById('project').disabled = true;
                document.getElementById('project').innerHTML = '<option>Pilih client terlebih dahulu</option>';

                box.classList.add('hidden');
                hiddenEmp.value = '';
                return;
            }

            //  VALIDASI: reset kalau user ubah manual
            const employeeId = hiddenEmp.value;
            if (employeeId) {
                const selectedEmp = employees.find(emp => emp.id == employeeId);
                if (selectedEmp && selectedEmp.full_name.toLowerCase() !== value) {
                    hiddenEmp.value = '';
                }
            }

            // FILTER DATA
            let filtered = employees.filter(emp =>
                (emp.full_name || '').toLowerCase().includes(value)
            );

            if (filtered.length === 0) {
                box.innerHTML = `<div class="px-4 py-2 text-sm text-gray-400">
                    Tidak ada hasil
                </div>`;
                box.classList.remove('hidden');
                return;
            }

            box.classList.remove('hidden');

            filtered.forEach(emp => {
                let item = document.createElement('div');
                item.className = "px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm";
                item.innerText = emp.full_name;

                item.addEventListener('click', function () {
                    inputEmp.value = emp.full_name;
                    hiddenEmp.value = emp.id;

                    document.getElementById('employee_name_hidden').value = emp.full_name;
                    box.classList.add('hidden');
                });

                box.appendChild(item);
            });
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('#employee_name')) box.classList.add('hidden');
        });

        const inputClient = document.getElementById('client_name');
        const hiddenClient = document.getElementById('client_id');
        const clientBox = document.getElementById('client_suggestions');

        inputClient.addEventListener('input', function () {
            let value = this.value.toLowerCase();
            clientBox.innerHTML = '';

            if (!value) {
                clientBox.classList.add('hidden');
                hiddenClient.value = '';
                return;
            }

            let filtered = clients.filter(c =>
                (c.client_name || '').toLowerCase().includes(value)
            );

            if (filtered.length === 0) {
                clientBox.innerHTML = `<div class="px-4 py-2 text-sm text-gray-400">
                    Tidak ada client ditemukan
                </div>`;
                clientBox.classList.remove('hidden');
                return;
            }

            clientBox.classList.remove('hidden');
            

            filtered.forEach(c => {
                let item = document.createElement('div');
                item.className = "px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm";
                item.innerText = c.client_name;

                item.addEventListener('click', function () {
                    console.log(c);
                    inputClient.value = c.client_name;
                    hiddenClient.value = c.id;

                    document.getElementById('client_name_hidden').value = c.client_name;


                    clientBox.classList.add('hidden');

                    const projectSelect = document.getElementById('project');
                    projectSelect.disabled = false;
                    projectSelect.innerHTML = '<option value="">Loading project...</option>';

                    console.log("CLIENT ID:", c.id);

                    loadProjects(c.id);
                });

                clientBox.appendChild(item);
            });
        });

        

        document.addEventListener('click', function (e) {
            if (!inputClient.contains(e.target) && !clientBox.contains(e.target)) {
                clientBox.classList.add('hidden');
            }
        });

        function loadProjects(clientId) {
            fetch(`/api/projects?client_id=${clientId}`)
                .then(res => res.json())
                .then(data => {
                    console.log(data);

                    let projects = data.data || [];
                    let select = document.getElementById('project');

                    select.innerHTML = '<option value="">Pilih project</option>';

                    if (projects.length === 0) {
                        let option = document.createElement('option');
                        option.value = "";
                        option.textContent = "Tidak ada project";
                        select.appendChild(option);
                        return;
                    }

                    projects.forEach(p => {
                        let option = document.createElement('option');
                        option.value = p.id;
                        option.textContent = p.project_name;
                        select.appendChild(option);
                    });

                    console.log('Projects loaded:', projects);
                })
                .catch(err => {
                    console.error(err);
                    showNotif('Gagal mengambil project', 'error');
                });
        }

        document.getElementById('project').addEventListener('change', function () {
            let selected = this.options[this.selectedIndex];
            document.getElementById('project_hidden').value = selected.text;

            console.log("PROJECT DIPILIH:", selected.text);
        });

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
            notifWrap.classList.remove('hidden');

            if (type === 'success') {
                notifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-green-50 border-green-200 text-green-800';
                notifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-green-100';
                notifIcon.innerHTML = '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>';
                notifBar.style.background = '#22c55e';
            } else {
                notifBox.className = 'relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border bg-red-50 border-red-200 text-red-800';
                notifIcon.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-red-100';
                notifIcon.innerHTML = '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';
                notifBar.style.background = '#f87171';
            }

            notifText.textContent = message;

            notifBar.style.transition = 'none';
            notifBar.style.width = '0%';
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    notifBar.style.transition = 'width 3.5s linear';
                    notifBar.style.width = '100%';
                });
            });

            notifTimer = setTimeout(() => {
                notifWrap.classList.add('hidden');
            }, 3500);
        }

        notifClose?.addEventListener('click', function () {
            if (notifTimer) clearTimeout(notifTimer);
            notifWrap.classList.add('hidden');
        });

        // ===== MODAL TOOLS =====
        function openModal() {
            toolsModal.classList.remove('hidden');
            toolsModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            document.querySelectorAll('.tool-checkbox').forEach(cb => {
                cb.closest('tr').style.display = selectedTools.has(cb.value) ? 'none' : '';
            });
        }

        function closeModal() {
            toolsModal.classList.add('hidden');
            toolsModal.classList.remove('flex');
            document.body.style.overflow = '';
            document.querySelectorAll('.tool-checkbox').forEach(cb => cb.checked = false);
            if (selectAllCheckbox) selectAllCheckbox.checked = false;
            if (searchInput) searchInput.value = '';
            modalTable.querySelectorAll('tr').forEach(row => {
                const cb = row.querySelector('.tool-checkbox');
                if (!cb) return;
                row.style.display = selectedTools.has(cb.value) ? 'none' : '';
            });
        }

        openToolsBtn?.addEventListener('click', openModal);
        closeToolsBtn?.addEventListener('click', closeModal);
        cancelToolsBtn?.addEventListener('click', closeModal);
        toolsModal?.addEventListener('click', function (e) { if (e.target === toolsModal) closeModal(); });

        selectAllCheckbox?.addEventListener('change', function () {
            document.querySelectorAll('.tool-checkbox').forEach(cb => {
                if (cb.closest('tr').style.display !== 'none') cb.checked = this.checked;
            });
        });

        searchInput?.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();
            modalTable.querySelectorAll('tr').forEach(row => {
                const cb = row.querySelector('.tool-checkbox');
                if (!cb) return;
                const name = row.dataset.name || '';
                if (selectedTools.has(cb.value)) {
                    row.style.display = 'none';
                } else if (name.includes(keyword)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        modalTable?.addEventListener('click', function (e) {
            const row = e.target.closest('tr');
            if (!row) return;
            if (e.target.type !== 'checkbox' && !e.target.classList.contains('preview-image')) {
                const cb = row.querySelector('.tool-checkbox');
                if (cb) cb.checked = !cb.checked;
            }
        });

        // ===== TAMBAH TOOL =====
        btnAddTool?.addEventListener('click', function () {
            const selected = document.querySelectorAll('.tool-checkbox:checked');
            if (selected.length === 0) {
                showNotif('Pilih minimal satu tool terlebih dahulu', 'error');
                return;
            }

            const tableBody = document.getElementById('tableSelectedTools');
            const emptyRow = document.getElementById('emptyRow');
            if (emptyRow) emptyRow.remove();

            let addedCount = 0;
            selected.forEach(cb => {
                const id = cb.value;
                selectedTools.add(id);
                if (document.getElementById('row-' + id)) return;
                const name = cb.dataset.name;
                const serial = cb.dataset.serial;
                const image = cb.dataset.image;
                const rowCount = tableBody.querySelectorAll('tr').length + 1;
                const imagePath = image ? `/storage/${image}` : `/images/no-image.png`;

                tableBody.insertAdjacentHTML('beforeend', `
                    <tr id="row-${id}" class="hover:bg-gray-50 transition">
                        <td class="text-center py-4 px-6 text-gray-600 row-number">${rowCount}</td>
                        <td class="text-center py-3 px-6">
                            <img src="${imagePath}" class="w-12 h-12 object-contain mx-auto rounded-lg shadow-sm preview-image cursor-pointer hover:scale-110 transition" alt="${name}">
                        </td>
                        <td class="py-3 px-6 font-semibold text-gray-800 tool-name-cell">${name}</td>
                        <td class="text-center py-3 px-6 text-gray-500">${serial}</td>
                        <td class="text-center py-3 px-6">
                            <button type="button" onclick="removeRow(this)" class="btn-delete-icon" title="Hapus item">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                            <input type="hidden" name="serial_ids[]" value="${id}">
                        </td>
                    </tr>
                `);
                addedCount++;
                cb.closest('tr').style.display = 'none';
                cb.checked = false;
            });

            if (addedCount > 0) {
                updateRowNumbers();
                showNotif(addedCount + ' tool berhasil ditambahkan', 'success');
            }
            closeModal();
        });

        // ===== DELETE CONFIRM MODAL =====
        function openDeleteModal(btn) {
            const row = btn.closest('tr');
            const nameCell = row.querySelector('.tool-name-cell');
            pendingDeleteRow = row;
            pendingDeleteId = row.id.replace('row-', '');
            deleteToolName.textContent = nameCell ? nameCell.textContent.trim() : '-';
            deleteConfirmModal.classList.remove('hidden');
            deleteConfirmModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            deleteConfirmModal.classList.add('hidden');
            deleteConfirmModal.classList.remove('flex');
            document.body.style.overflow = '';
            pendingDeleteRow = null;
            pendingDeleteId = null;
        }

        deleteCancelBtn?.addEventListener('click', closeDeleteModal);
        deleteConfirmModal?.addEventListener('click', function (e) {
            if (e.target === deleteConfirmModal) closeDeleteModal();
        });

        deleteConfirmBtn?.addEventListener('click', function () {
            if (!pendingDeleteRow || !pendingDeleteId) return;

            const toolName = pendingDeleteRow.querySelector('.tool-name-cell')?.textContent.trim() || 'Tool';
            const id = pendingDeleteId;

            selectedTools.delete(id);
            const modalRow = document.querySelector(`.tool-checkbox[value="${id}"]`)?.closest('tr');
            if (modalRow) modalRow.style.display = '';

            pendingDeleteRow.remove();
            updateRowNumbers();

            const tableBody = document.getElementById('tableSelectedTools');
            if (tableBody.querySelectorAll('tr').length === 0) {
                tableBody.innerHTML = `<tr id="emptyRow"><td colspan="5" class="py-12 text-center text-gray-400 italic text-sm">Belum ada tools yang dipilih</td></tr>`;
            }

            closeDeleteModal();
            showNotif('"' + toolName + '" berhasil dihapus', 'error');
        });

        window.removeRow = function (btn) { openDeleteModal(btn); };

        // ===== UPDATE ROW NUMBERS =====
        function updateRowNumbers() {
            document.querySelectorAll('#tableSelectedTools tr[id^="row-"]').forEach((row, i) => {
                const numCell = row.querySelector('.row-number');
                if (numCell) numCell.textContent = i + 1;
            });
        }

        // ===== IMAGE PREVIEW =====
        document.addEventListener('click', function (e) {
            const img = e.target.closest('.preview-image');
            if (!img) return;
            previewImage.src = img.src;
            previewModal.classList.remove('hidden');
            previewModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        });

        closePreview?.addEventListener('click', function () {
            previewModal.classList.add('hidden');
            previewModal.classList.remove('flex');
            document.body.style.overflow = '';
        });

        previewModal?.addEventListener('click', function (e) {
            if (e.target === previewModal) {
                previewModal.classList.add('hidden');
                previewModal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        });

        // ===== VALIDASI FORM SUBMIT =====
        formPeminjaman.addEventListener('submit', function (e) {

            console.log("SUBMIT KE TRIGGER");

            // Cegah double submit
            if (isSubmitting) {
                e.preventDefault();
                return;
            }

            // 1. Validasi Employee harus dipilih dari autocomplete
            const employeeId = hiddenEmp.value;
            const employeeName = inputEmp.value.trim();

            if (!employeeId || !employeeName) {
                e.preventDefault();
                showNotif('Pilih nama peminjam dari daftar karyawan', 'error');
                inputEmp.focus();
                return;
            }

            // Cek nama yang diketik cocok dengan data yang tersimpan
            const selectedEmp = employees.find(emp => emp.id == employeeId);
            if (selectedEmp && selectedEmp.full_name.toLowerCase() !== employeeName.toLowerCase()) {
                e.preventDefault();
                showNotif('Nama peminjam tidak valid, pilih dari daftar', 'error');
                inputEmp.focus();
                return;
            }

            // 2. Validasi minimal 1 tool dipilih
            const selectedCount = document.querySelectorAll('#tableSelectedTools tr[id^="row-"]').length;
            if (selectedCount === 0) {
                e.preventDefault();
                showNotif('Pilih minimal 1 tool untuk dipinjam', 'error');
                return;
            }

            // 3. Validasi tanggal tidak boleh masa lalu
            const dateInput = document.querySelector('input[name="date"]'); 

            const selectedDate = new Date(dateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (isNaN(selectedDate.getTime())) {
                e.preventDefault();
                showNotif('Format tanggal tidak valid', 'error');
                dateInput.focus();
                return;
            }

            if (selectedDate < today) {
                e.preventDefault();
                showNotif('Tanggal peminjaman tidak boleh di masa lalu', 'error');
                dateInput.focus();
                return;
            }

            // 4. Set loading state & cegah double submit
            isSubmitting = true;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = `
                <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Menyimpan...
            `;
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
            submitBtn.classList.remove('hover:bg-[#5EA6FF]', 'hover:text-white', 'hover:-translate-y-0.5');

            // Fallback reset 10 detik kalau gagal redirect
            setTimeout(() => {
                isSubmitting = false;
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                submitBtn.classList.add('hover:bg-[#5EA6FF]', 'hover:text-white', 'hover:-translate-y-0.5');
            }, 10000);
        });

        // ===== KEYBOARD =====
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (!deleteConfirmModal.classList.contains('hidden')) closeDeleteModal();
                else if (!previewModal.classList.contains('hidden')) {
                    previewModal.classList.add('hidden');
                    previewModal.classList.remove('flex');
                    document.body.style.overflow = '';
                }
                else if (!toolsModal.classList.contains('hidden')) closeModal();
            }
        });
    });
</script>