@extends('layouts.app')

@section('content')

    <div class="w-full min-h-screen flex flex-col" x-data="{ openModal:false }" @close-modal.window="openModal = false">

        {{-- HEADER PAGE --}}
        <div class="flex justify-between items-end mb-6">
            <div>
                <h2 class="text-3xl font-bold text-[#5EA6FF] tracking-tight">Edit Permintaan</h2>
                <p class="text-sm text-gray-500 mt-1">Ubah data permintaan consumable</p>
            </div>
            <a href="{{ route('transaksi.index') }}"
                class="bg-[#E5E7EB] hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center shadow-sm">
                <span class="mr-1">←</span> Kembali
            </a>
        </div>

        {{-- NOTIF TOAST --}}
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

        <form id="formTransaksi" action="{{ route('transaksi.update', $transaction->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- MAIN CARD --}}
            <div class="bg-[#F9FAFB] rounded-3xl shadow-xl p-8 border border-gray-100 space-y-8">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-6">Edit Data Transaksi</h3>
                    <div class="space-y-6">

                        {{-- ROW 1 --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            {{-- Search Employee --}}
                            <div x-data="empSearchEdit('{{ $transaction->borrower_name }}', '{{ $transaction->employee_id }}')"
                                @click.away="show = false" class="relative">

                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Karyawan <span class="text-red-500">*</span>
                                </label>

                                <div class="relative">
                                    <input type="text" x-model="search" @focus="fetchEmployees(); if(!selected) show = true"
                                        @input="selected = ''; selectedId = ''; fetchEmployees(); show = true"
                                        placeholder="Ketik nama karyawan..."
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none pr-9">

                                    <div x-show="loading" x-cloak class="absolute right-9 top-1/2 -translate-y-1/2">
                                        <svg class="animate-spin h-4 w-4 text-[#5EA6FF]" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>

                                <input type="hidden" name="employee_id" :value="selectedId">
                                <input type="hidden" name="borrower_name" :value="selected">

                                <div x-show="show && search.length > 0 && !selected" x-transition style="display: none;"
                                    class="absolute z-50 mt-1 w-full bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                                    <div class="max-h-56 overflow-y-auto">
                                        <template x-for="emp in filtered" :key="getEmpId(emp)">
                                            <button type="button"
                                                @click="selected = getEmpName(emp); selectedId = getEmpId(emp); search = getEmpName(emp); show = false"
                                                class="w-full px-4 py-2.5 text-left text-sm hover:bg-blue-50 transition border-b border-gray-50 last:border-0">
                                                <span x-text="getEmpName(emp)"></span>
                                            </button>
                                        </template>
                                        <template x-if="filtered.length === 0 && !loading">
                                            <div class="px-4 py-6 text-center text-gray-400 text-sm">Tidak ditemukan</div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- Tanggal --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="date" value="{{ $transaction->date->format('Y-m-d') }}" required
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none">
                            </div>
                            <div></div>
                        </div>

                        {{-- ROW 2 --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            {{-- NAMA CLIENT --}}
                            <div x-data="clientSearch({ id: '{{ $initialClientId }}', name: '{{ $transaction->client }}' })"
                                @click.away="show = false" class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Client</label>
                                <div class="relative">
                                    <input type="text" x-model="search" @focus="if(!selected) show = true; fetchClients()"
                                        @input="selected = ''; selectedId = ''; show = true; fetchClients()"
                                        placeholder="Ketik nama client..." :class="loading ? 'animate-pulse' : ''"
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none pr-9">

                                    <button type="button" x-show="selected" x-cloak @click="clearSelection()"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    <div x-show="loading" x-cloak class="absolute right-9 top-1/2 -translate-y-1/2">
                                        <svg class="animate-spin h-4 w-4 text-[#5EA6FF]" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <input type="hidden" name="client_id" :value="selectedId">
                                <input type="hidden" name="client" :value="selected">
                                <div x-show="show && !selected && search.length > 0" x-transition x-cloak
                                    class="absolute z-50 mt-1 w-full bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden">
                                    <div class="max-h-56 overflow-y-auto">
                                        <template x-for="item in filtered" :key="getClientId(item)">
                                            <button type="button" @click="selectClient(item)"
                                                class="w-full px-4 py-2.5 text-left text-sm hover:bg-blue-50 transition border-b border-gray-50 last:border-0">
                                                <span x-text="getClientName(item)"></span>
                                            </button>
                                        </template>
                                        <template x-if="!loading && filtered.length === 0">
                                            <div class="px-4 py-6 text-center text-gray-400 text-sm">Tidak ditemukan</div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div x-data="projectDropdown(
                        '{{ $transaction->client_id ?? '' }}', 
                        '{{ $transaction->project_id ?? '' }}', 
                        '{{ $transaction->project ?? '' }}'     
                    )" x-init="init()" class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Proyek</label>
                                <select name="project" :disabled="!hasClientId"
                                    :class="!hasClientId ? 'bg-gray-100 cursor-not-allowed' : 'bg-white cursor-pointer'"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none appearance-none"
                                    x-model="selectedProject" @change="
                                selectedProject = $event.target.value; 
                                selectedProjectId = $event.target.options[$event.target.selectedIndex].dataset.id
                            ">
                                    
                                    <option value=""
                                        x-text="!hasClientId ? '-- Pilih Client dulu --' : (projects.length === 0 ? 'Tidak ada proyek tersedia' : '-- Pilih Proyek --')">
                                    </option>

                                    <template x-for="proj in projects" :key="getProjectId(proj) || getProjectName(proj)">
                                        <option :value="getProjectName(proj)" :data-id="getProjectId(proj)"
                                            :selected="getProjectId(proj) == selectedProjectId || getProjectName(proj) == selectedProject"
                                            x-text="getProjectName(proj) + (proj._isLegacy ? ' (data lama)' : '')">
                                        </option>
                                    </template>
                                </select>

                                <input type="hidden" name="project_id" x-model="selectedProjectId">
                            </div>

                            {{-- KEPERLUAN --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keperluan</label>
                                <input type="text" name="purpose" value="{{ $transaction->purpose ?? '' }}"
                                    placeholder="Masukkan keperluan"
                                    class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:ring-2 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none">
                            </div>
                        </div>

                        {{-- SECTION DAFTAR BARANG --}}
                        <div class="mt-10">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-800">Daftar Consumable</h3>
                                <button type="button" @click.stop="openModal = true"
                                    class="group inline-flex items-center px-4 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all duration-200 tracking-wide border-2 border-[#5EA6FF] bg-white text-sm text-[#5EA6FF] hover:bg-[#5EA6FF] hover:text-white hover:shadow-blue-500/40 hover:-translate-y-0.5">
                                    <svg class="w-4 h-4 mr-2 transition-transform duration-300 group-hover:rotate-90"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Pilih Consumable
                                </button>
                            </div>
                            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                                <table class="w-full text-sm" id="tableConsumables">
                                    <thead>
                                        <tr class="text-white text-xs uppercase tracking-wider"
                                            style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">
                                            <th class="py-3 px-4 font-semibold text-center w-12">No</th>
                                            <th class="py-3 px-4 font-semibold text-center w-20">Foto</th>
                                            <th class="py-3 px-4 font-semibold text-left">Nama Consumable</th>
                                            <th class="py-3 px-4 font-semibold text-center w-24">Stock</th>
                                            <th class="py-3 px-4 font-semibold text-center w-32">Jumlah</th>
                                            <th class="py-3 px-4 font-semibold text-center w-16">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-50">
                                        @if($transaction->items->count() > 0)
                                            @foreach($transaction->items as $i => $item)
                                                <tr data-id="{{ $item->consumable_id }}" data-stock="{{ $item->consumable->stock }}"
                                                    class="hover:bg-gray-50 transition">
                                                    <td class="py-3 px-4 text-center font-medium text-gray-600 w-12"><span
                                                            class="no-col">{{ $i + 1 }}</span></td>
                                                    <td class="py-3 px-4 text-center w-20">
                                                        <img src="{{ $item->consumable->image ? asset('storage/' . $item->consumable->image) : asset('images/no-image.png') }}"
                                                            class="w-10 h-10 object-cover rounded-lg shadow-sm mx-auto"
                                                            onerror="this.src='{{ asset('images/no-image.png') }}'">
                                                    </td>
                                                    <td class="py-3 px-4"><span
                                                            class="font-semibold text-gray-800 item-name">{{ $item->consumable->name }}</span>
                                                    </td>
                                                    <td class="text-center py-3 px-4 w-24">
                                                        <div class="font-medium text-blue-600 stock-display">
                                                            {{ $item->consumable->stock }}
                                                        </div>
                                                        <div class="text-xs text-gray-400 unit-display">
                                                            {{ $item->consumable->unit }}
                                                        </div>
                                                    </td>
                                                    <td class="text-center py-3 px-4 w-32">
                                                        <input type="number" value="{{ $item->qty }}" min="1"
                                                            max="{{ $item->consumable->stock }}" onchange="updateQty(this)"
                                                            class="w-20 h-8 text-center border border-gray-300 rounded-lg qty-input-main shadow-sm focus:ring-1 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none">
                                                    </td>
                                                    <td class="text-center py-3 px-4 w-16">
                                                        <button type="button" onclick="removeRow(this)" class="btn-delete-icon"
                                                            title="Hapus item">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor" stroke-width="1.8">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                            </svg>
                                                        </button>
                                                        <input type="hidden" name="items[{{ $i }}][consumable_id]"
                                                            value="{{ $item->consumable_id }}">
                                                        <input type="hidden" name="items[{{ $i }}][qty]" value="{{ $item->qty }}"
                                                            class="hidden-qty">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr id="emptyRow">
                                                <td colspan="6" class="py-10 text-center text-gray-400 italic text-sm">Belum ada
                                                    consumable yang dipilih</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- SAVE BUTTON --}}
                        <div class="pt-8 border-t border-gray-200 flex justify-end">
                            <button type="button" id="btnSave"
                                class="px-10 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all duration-200 tracking-wide border-2 border-[#5EA6FF] bg-white text-sm text-[#5EA6FF] hover:bg-[#5EA6FF] hover:text-white hover:shadow-blue-500/40 hover:-translate-y-0.5">
                                Update Transaksi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{-- ================= MODAL CONSUMABLE ================= --}}
        <div x-show="openModal" x-transition.opacity x-cloak
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div @click.away="openModal = false"
                class="bg-white w-full max-w-3xl rounded-2xl shadow-2xl relative max-h-[90vh] overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center text-white"
                    style="background: linear-gradient(180deg, #7FC4FF, #5EA6FF);">
                    <h3 class="text-lg font-bold">Consumable Tersedia</h3>
                    <button type="button" @click="openModal=false"
                        class="text-white/80 hover:text-white text-2xl transition">✕</button>
                </div>
                <div class="p-6 flex-1 overflow-auto">
                    <div id="modalNotifWrap" class="hidden mb-4">
                        <div id="modalNotifBox"
                            class="relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border">
                            <div id="modalNotifIcon"
                                class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"></div>
                            <p id="modalNotifText" class="text-sm font-medium"></p>
                            <button id="modalNotifClose"
                                class="ml-auto flex-shrink-0 opacity-50 hover:opacity-100 transition"><svg class="w-4 h-4"
                                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg></button>
                            <div id="modalNotifBar" class="absolute bottom-0 left-0 h-1 rounded-b-2xl" style="width:0%">
                            </div>
                        </div>
                    </div>
                    <div class="mb-5">
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"><svg class="w-5 h-5"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg></span>
                            <input type="text" id="searchConsumable" placeholder="Cari Nama Consumable"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3 shadow-inner focus:ring-2 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none transition text-sm">
                        </div>
                    </div>
                    <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                        <table id="popupTable" class="w-full text-sm">
                            <thead class="sticky top-0 bg-gray-50">
                                <tr class="text-gray-600 border-b border-gray-200">
                                    <th class="py-3 px-4 text-center w-10"><input type="checkbox" id="selectAllCons"
                                            class="w-4 h-4 accent-[#5EA6FF] rounded border-gray-300"></th>
                                    <th class="py-3 px-4 text-left font-semibold">Nama Consumable</th>
                                    <th class="py-3 px-4 text-center font-semibold">Stock</th>
                                    <th class="py-3 px-4 text-center w-24 font-semibold">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($consumables as $c)
                                    <tr class="border-b hover:bg-blue-50/30 transition cursor-pointer cons-row"
                                        data-name="{{ strtolower($c->name) }}">
                                        <td class="text-center py-3 px-4"><input type="checkbox"
                                                class="pick-consumable w-4 h-4 accent-[#5EA6FF] rounded border-gray-300"
                                                data-id="{{ $c->id }}" data-name="{{ $c->name }}" data-stock="{{ $c->stock }}"
                                                data-unit="{{ $c->unit }}"></td>
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ $c->image ? asset('storage/' . $c->image) : asset('images/no-image.png') }}"
                                                    class="preview-click w-10 h-10 object-cover rounded-lg cursor-pointer"
                                                    onerror="this.src='{{ asset('images/no-image.png') }}'">
                                                <span class="font-medium text-gray-800">{{ $c->name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center py-3 px-4">
                                            <span
                                                class="font-semibold {{ $c->stock <= $c->minimum_stock ? 'text-red-500' : 'text-blue-600' }}">{{ $c->stock }}</span>
                                            <div class="text-xs text-gray-400">{{ $c->unit }}</div>
                                        </td>
                                        <td class="text-center py-3 px-4"><input type="number" min="1" max="{{ $c->stock }}"
                                                value="1"
                                                class="w-16 h-8 border border-gray-300 rounded-lg text-center qty-input shadow-sm focus:ring-1 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50">
                    <button type="button" @click="openModal=false"
                        class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-100 transition font-medium text-sm shadow-sm">Batal</button>
                    <button type="button" id="btnAddConsumable"
                        class="group px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-500/20 transition-all duration-200 tracking-wide border-2 border-[#5EA6FF] bg-white text-sm text-[#5EA6FF] hover:bg-[#5EA6FF] hover:text-white hover:shadow-blue-500/40 hover:-translate-y-0.5 flex items-center gap-2">
                        <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-90" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4">
                            </path>
                        </svg>Tambahkan
                    </button>
                </div>
            </div>
        </div>

        {{-- ================= MODAL HAPUS ITEM ================= --}}
        <div id="deleteItemModal"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[10002] p-3 sm:p-4">
            <div class="w-[calc(100%-1.5rem)] sm:w-11/12 max-w-sm bg-white rounded-2xl shadow-2xl p-5 sm:p-6 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-red-100 flex items-center justify-center"><svg
                            class="w-7 h-7 sm:w-8 sm:h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg></div>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-gray-800 mb-2">Hapus Item?</h3>
                <p class="text-xs sm:text-sm text-gray-500 mb-1">Anda yakin ingin menghapus</p>
                <p id="deleteItemNameModal" class="text-xs sm:text-sm font-semibold text-[#5EA6FF] mb-5"></p>
                <div class="flex gap-3">
                    <button id="cancelDeleteItem"
                        class="flex-1 px-5 py-2.5 bg-[#dcdcdc] text-gray-700 rounded-xl text-xs sm:text-sm font-semibold hover:bg-[#c5c5c5] transition">Batal</button>
                    <button id="confirmDeleteItem"
                        class="flex-1 px-5 py-2.5 bg-red-500 text-white rounded-xl text-xs sm:text-sm font-semibold hover:bg-red-600 transition">Ya,
                        Hapus</button>
                </div>
            </div>
        </div>

        <!-- MODAL PREVIEW IMAGE -->
        <div id="imagePreviewModal" class="fixed inset-0 bg-black/70 hidden items-center justify-center z-[9999]">
            <div class="relative">
                <img id="previewImg" class="max-w-[90vw] max-h-[90vh] rounded-xl">
                <button id="closePreviewBtn"
                    class="absolute -top-3 -right-3 bg-white rounded-full px-3 py-1 shadow z-[10000]">✕</button>
            </div>
        </div>
    </div>

    <style>
        #tableConsumables th,
        #tableConsumables td {
            border: none !important;
        }

        .shadow-inner {
            box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05);
        }

        input::placeholder {
            color: #9CA3AF;
            font-weight: 400;
        }

        #notifWrap,
        #modalNotifWrap {
            animation: notifSlideIn 0.4s ease-out;
        }

        @keyframes notifSlideIn {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        #notifWrap.hiding,
        #modalNotifWrap.hiding {
            animation: notifSlideOut 0.35s ease-in forwards;
        }

        @keyframes notifSlideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(60px);
            }
        }

        #notifBar,
        #modalNotifBar {
            transition: width 3.5s linear;
        }

        .row-error {
            background-color: #fef2f2 !important;
            animation: shakeRow 0.4s ease-in-out;
        }

        @keyframes shakeRow {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
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

    <script>
        function empSearchEdit(initialName = '', initialId = '') {
            return {
                search: initialName,
                selected: initialName,
                selectedId: initialId,
                show: false,
                loading: false,
                employees: [],
                debounceTimer: null,

                fetchEmployees() {
                    clearTimeout(this.debounceTimer);
                    this.debounceTimer = setTimeout(async () => {
                        if (this.employees.length > 0) return;
                        this.loading = true;
                        try {
                            const res = await fetch('{{ url("/api/proxy/employee-list") }}');
                            const data = await res.json();

                            if (data.data && Array.isArray(data.data)) this.employees = data.data;
                            else if (Array.isArray(data)) this.employees = data;
                            else this.employees = [];

                        } catch (e) {
                            console.error('❌ Gagal fetch employees:', e);
                            this.employees = [];
                        } finally {
                            this.loading = false;
                        }
                    }, 300);
                },

                get filtered() {
                    if (!this.search || this.selected) return [];
                    const q = this.search.toLowerCase();
                    return this.employees.filter(emp => {
                        const name = (emp.full_name || emp.name || emp.nama || '').toLowerCase();
                        return name.includes(q);
                    });
                },

                getEmpName(emp) {
                    return emp.full_name || emp.name || emp.nama || '';
                },

                getEmpId(emp) {
                    return emp.id || emp.employee_id || '';
                }
            };
        }

        function clientSearch(initialState = {}) {
            return {
                search: initialState.name || '', selected: initialState.name || '', selectedId: initialState.id || '',
                show: false, loading: false, clients: [], debounceTimer: null,
                fetchClients() {
                    clearTimeout(this.debounceTimer);
                    this.debounceTimer = setTimeout(async () => {

                        this.loading = true;
                        try {
                            const url = '{{ url("/api/proxy/client-list") }}';
                            const response = await fetch(url, { method: 'GET' });
                            if (!response.ok) throw new Error('Server Error');
                            const data = await response.json();
                            let parsed = [];
                            if (data.data && Array.isArray(data.data)) parsed = data.data;
                            else if (Array.isArray(data)) parsed = data;
                            else if (typeof data === 'object') {
                                const val = Object.values(data).find(v => Array.isArray(v));
                                if (val) parsed = val;
                            }
                            this.clients = parsed;
                        } catch (error) { console.error(error); this.clients = []; } finally { this.loading = false; }
                    }, 300);
                },
                get filtered() {
                    if (!this.search || this.clients.length === 0) return [];
                    const q = this.search.toLowerCase();
                    return this.clients.filter(c => {
                        if (typeof c !== 'object' || c === null) return false;
                        const values = Object.values(c).join(' ').toLowerCase();
                        return values.includes(q);
                    });
                },
                getClientName(item) { return item.name || item.client_name || item.nama_client || item.nama || item.title || Object.values(item).find(v => typeof v === 'string' && v.length > 0) || ''; },
                getClientId(item) {
                    if (item.id) return item.id; if (item.client_id) return item.client_id;
                    const idField = Object.entries(item).find(([k, v]) => typeof v === 'string' && v.toUpperCase().startsWith('CLT'));
                    return idField ? idField[1] : '';
                },
                selectClient(item) {
                    this.selected = this.getClientName(item); this.selectedId = this.getClientId(item);
                    this.search = this.selected; this.show = false;
                    if (!this.selectedId) return;
                    window.dispatchEvent(new CustomEvent('client-selected', { detail: { clientId: this.selectedId, clientName: this.selected } }));
                },
                clearSelection() {
                    this.search = ''; this.selected = ''; this.selectedId = ''; this.clients = [];
                    window.dispatchEvent(new CustomEvent('client-selected', { detail: { clientId: '', clientName: '' } }));
                }
            };
        }

        function projectDropdown(initialClientId = '', initialProjectId = '', initialProjectName = '') {
            return {
                projects: [],
                hasClientId: !!initialClientId || !!initialProjectName,
                selectedProject: initialProjectName || '',
                selectedProjectId: initialProjectId || '', // ✅ Default ke ID dari DB

                getProjectName(p) {
                    return p.name || p.project_name || p.nama_proyek || p.nama || p.title ||
                        Object.values(p).find(v => typeof v === 'string' && v.length > 1 && v.length < 100) || '';
                },

                getProjectId(p) {
                    return p.id || p.project_id || p.code || p.project_code || p._id ||
                        Object.values(p).find(v =>
                            typeof v === 'string' && v.length >= 3 && v.length <= 20 && /^[A-Z0-9\-_]+$/.test(v)
                        ) || '';
                },

                init() {
                    window.addEventListener('client-selected', async (e) => {
                        await this.loadProjects(e.detail.clientId, '', ''); // Reset saat client berubah
                    });

                    if (initialClientId) {
                        // ✅ Load projects + match by ID + fallback to name
                        this.loadProjects(initialClientId, initialProjectId, initialProjectName);
                    }
                    else if (initialProjectName) {
                        this.hasClientId = true;
                        this.selectedProject = initialProjectName;
                        this.projects = [{ name: initialProjectName, id: '' }]; // Fallback placeholder
                    }
                },

                async loadProjects(clientId, projectIdToSelect = '', projectNameToSelect = '') {
                    this.projects = [];

                    if (!clientId) {
                        this.hasClientId = false;
                        return;
                    }
                    this.hasClientId = true;

                    try {
                        const res = await fetch(`{{ url("/api/proxy/client-projects") }}?client_id=${clientId}`);
                        if (!res.ok) throw new Error('Server Error');
                        const data = await res.json();

                        if (data.data && Array.isArray(data.data)) this.projects = data.data;
                        else if (Array.isArray(data)) this.projects = data;
                        else this.projects = data || [];

                        // 🔥 DEBUG: Liat apa yang kita dapat
                        console.log('📦 Projects:', this.projects);
                        console.log('🎯 Want to select - ID:', projectIdToSelect, '| Name:', projectNameToSelect);

                        if (this.projects.length > 0) {
                            console.log('🔑 Sample project keys:', Object.keys(this.projects[0]));
                            console.log('🔑 Sample project ID via getProjectId():', this.getProjectId(this.projects[0]));
                        }

                        if ((projectIdToSelect || projectNameToSelect) && this.projects.length > 0) {
                            this.$nextTick(() => {
                                // 1. Match by ID (loose comparison)
                                const matchById = this.projects.find(p => this.getProjectId(p) == projectIdToSelect);

                                // 2. Match by name (case-insensitive, trim)
                                const matchByName = !matchById && projectNameToSelect ?
                                    this.projects.find(p => {
                                        const name = (this.getProjectName(p) || '').toString().trim().toLowerCase();
                                        const target = projectNameToSelect.toString().trim().toLowerCase();
                                        return name && target && (name === target || name.includes(target) || target.includes(name));
                                    }) : null;

                                const match = matchById || matchByName;

                                console.log('🎯 Match result:', match ? 'FOUND' : 'NOT FOUND');

                                if (match) {
                                    this.selectedProject = this.getProjectName(match);
                                    this.selectedProjectId = this.getProjectId(match);
                                    console.log('✅ Auto-selected:', this.selectedProject, '(ID:', this.selectedProjectId + ')');
                                } else {
                                    // Fallback: tetap tampilkan project lama walau gak ada di list API
                                    if (projectNameToSelect) {
                                        this.selectedProject = projectNameToSelect;
                                        this.selectedProjectId = projectIdToSelect;
                                        // Tambah ke projects biar muncul di dropdown
                                        if (!this.projects.find(p => this.getProjectName(p) === projectNameToSelect)) {
                                            this.projects.unshift({
                                                name: projectNameToSelect,
                                                id: projectIdToSelect,
                                                _isLegacy: true
                                            });
                                        }
                                        console.log('⚠️ Fallback: using legacy project:', projectNameToSelect);
                                    }
                                }
                            });
                        }
                    } catch (error) {
                        console.error('❌ Failed to load projects:', error);
                        if (projectNameToSelect) {
                            this.selectedProject = projectNameToSelect;
                            this.selectedProjectId = projectIdToSelect;
                            this.projects = [{ name: projectNameToSelect, id: projectIdToSelect, _isLegacy: true }];
                        }
                    }
                }
            };
        }

        document.addEventListener('DOMContentLoaded', function () {
            const btnSave = document.getElementById('btnSave');
            const form = document.getElementById('formTransaksi');
            const btnAddConsumable = document.getElementById('btnAddConsumable');
            const searchInput = document.getElementById('searchConsumable');
            const selectAllCheckbox = document.getElementById('selectAllCons');
            const notifWrap = document.getElementById('notifWrap'), notifBox = document.getElementById('notifBox'), notifIcon = document.getElementById('notifIcon'), notifText = document.getElementById('notifText'), notifBar = document.getElementById('notifBar'), notifClose = document.getElementById('notifClose');
            let notifTimer = null;
            const modalNotifWrap = document.getElementById('modalNotifWrap'), modalNotifBox = document.getElementById('modalNotifBox'), modalNotifIcon = document.getElementById('modalNotifIcon'), modalNotifText = document.getElementById('modalNotifText'), modalNotifBar = document.getElementById('modalNotifBar'), modalNotifClose = document.getElementById('modalNotifClose');
            let modalNotifTimer = null;

            function showNotif(message, type) {
                if (notifTimer) clearTimeout(notifTimer); notifWrap.classList.remove('hidden', 'hiding');
                const styles = {
                    success: { box: 'bg-green-50 border-green-200 text-green-800', icon: 'bg-green-100', svg: '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>', bar: '#22c55e' },
                    warning: { box: 'bg-amber-50 border-amber-200 text-amber-800', icon: 'bg-amber-100', svg: '<svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>', bar: '#fbbf24' },
                    error: { box: 'bg-red-50 border-red-200 text-red-800', icon: 'bg-red-100', svg: '<svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>', bar: '#f87171' }
                }[type];
                notifBox.className = `relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border ${styles.box}`;
                notifIcon.className = `flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center ${styles.icon}`;
                notifIcon.innerHTML = styles.svg; notifBar.style.background = styles.bar;
                notifText.textContent = message; notifBar.style.transition = 'none'; notifBar.style.width = '0%';
                requestAnimationFrame(() => { requestAnimationFrame(() => { notifBar.style.transition = 'width 3.5s linear'; notifBar.style.width = '100%'; }); });
                notifTimer = setTimeout(() => { notifWrap.classList.add('hiding'); setTimeout(() => { notifWrap.classList.add('hidden'); notifWrap.classList.remove('hiding'); }, 250); }, 3500);
            }
            function showModalNotif(message, type) {
                if (modalNotifTimer) clearTimeout(modalNotifTimer); modalNotifWrap.classList.remove('hidden', 'hiding');
                const styles = { success: { box: 'bg-blue-50 border-blue-200 text-blue-800', icon: 'bg-blue-100', svg: '<svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>', bar: '#5EA6FF' }, error: { box: 'bg-red-50 border-red-200 text-red-800', icon: 'bg-red-100', svg: '<svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>', bar: '#f87171' } }[type];
                modalNotifBox.className = `relative overflow-hidden flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-lg border ${styles.box}`;
                modalNotifIcon.className = `flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center ${styles.icon}`;
                modalNotifIcon.innerHTML = styles.svg; modalNotifBar.style.background = styles.bar;
                modalNotifText.textContent = message; modalNotifBar.style.transition = 'none'; modalNotifBar.style.width = '0%';
                requestAnimationFrame(() => { requestAnimationFrame(() => { modalNotifBar.style.transition = 'width 3.5s linear'; modalNotifBar.style.width = '100%'; }); });
                modalNotifTimer = setTimeout(() => { modalNotifWrap.classList.add('hiding'); setTimeout(() => { modalNotifWrap.classList.add('hidden'); modalNotifWrap.classList.remove('hiding'); }, 250); }, 3500);
            }
            notifClose.addEventListener('click', () => { if (notifTimer) clearTimeout(notifTimer); notifWrap.classList.add('hiding'); setTimeout(() => { notifWrap.classList.add('hidden'); notifWrap.classList.remove('hiding'); }, 250); });
            modalNotifClose.addEventListener('click', () => { if (modalNotifTimer) clearTimeout(modalNotifTimer); modalNotifWrap.classList.add('hiding'); setTimeout(() => { modalNotifWrap.classList.add('hidden'); modalNotifWrap.classList.remove('hiding'); }, 250); });

                                                                                            @if(session('success')) showNotif('{{ session("success") }}', 'success'); @endif
            @if(session('error')) showNotif('{{ session("error") }}', 'error'); @endif

            btnSave.addEventListener('click', function () {
                if (btnSave.disabled) return; btnSave.disabled = true; const originalText = btnSave.innerText; btnSave.innerText = "Proses..."; btnSave.classList.add('opacity-75', 'cursor-not-allowed');
                const resetButton = () => { btnSave.disabled = false; btnSave.innerText = originalText; btnSave.classList.remove('opacity-75', 'cursor-not-allowed'); };
                const employeeId = form.querySelector('input[name="employee_id"]');
                const items = document.querySelectorAll('#tableConsumables tbody tr:not(#emptyRow)');
                document.querySelectorAll('.row-error').forEach(row => row.classList.remove('row-error'));
                if (!employeeId.value) { showNotif("Pilih karyawan terlebih dahulu", "error"); resetButton(); return; }
                if (items.length === 0) { showNotif("Pilih minimal 1 consumable terlebih dahulu", "error"); resetButton(); return; }
                let stockErrors = [];
                items.forEach(row => {
                    const qtyInput = row.querySelector('.qty-input-main'); const hiddenQty = row.querySelector('.hidden-qty'); const stock = parseInt(row.dataset.stock); let qty = parseInt(qtyInput.value); const itemName = row.querySelector('.item-name').textContent.trim();
                    if (isNaN(qty) || qty <= 0) { qty = 1; qtyInput.value = 1; } if (hiddenQty) hiddenQty.value = qty;
                    if (qty > stock) { stockErrors.push(itemName + " - stock hanya " + stock); row.classList.add('row-error'); }
                });
                if (stockErrors.length > 0) { showNotif(stockErrors[0], "error"); document.querySelector('.row-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' }); resetButton(); return; }
                form.submit();
            });

            searchInput?.addEventListener('keyup', function () {
                const keyword = this.value.toLowerCase();
                document.querySelectorAll('#popupTable tbody tr.cons-row').forEach(row => {
                    if (row.dataset.added === 'true') { row.style.display = 'none'; return; }
                    row.style.display = row.dataset.name.includes(keyword) ? '' : 'none';
                });
            });
            selectAllCheckbox?.addEventListener('change', function () { document.querySelectorAll('.pick-consumable').forEach(cb => { if (cb.closest('tr').style.display !== 'none') cb.checked = this.checked; }); });

            window.updateQty = function (input) {
                const row = input.closest('tr'); const qty = parseInt(input.value); const stock = parseInt(row.dataset.stock); row.classList.remove('row-error');
                if (isNaN(qty) || qty <= 0) { const h = row.querySelector('.hidden-qty'); if (h) h.value = input.value; return; }
                if (qty > stock) { showNotif("Stock hanya tersedia " + stock, "warning"); row.classList.add('row-error'); input.value = stock; const h = row.querySelector('.hidden-qty'); if (h) h.value = stock; return; }
                const h = row.querySelector('.hidden-qty'); if (h) h.value = qty;
            };

            let rowToDelete = null; const deleteItemModal = document.getElementById('deleteItemModal'), deleteItemNameModal = document.getElementById('deleteItemNameModal');
            window.removeRow = function (btn) { rowToDelete = btn.closest('tr'); deleteItemNameModal.textContent = rowToDelete.querySelector('.item-name').textContent.trim(); deleteItemModal.classList.remove('hidden'); deleteItemModal.classList.add('flex'); };
            function closeDeleteItemModal() { deleteItemModal.classList.add('hidden'); deleteItemModal.classList.remove('flex'); rowToDelete = null; }
            document.getElementById('cancelDeleteItem').addEventListener('click', closeDeleteItemModal);
            deleteItemModal.addEventListener('click', function (e) { if (e.target === deleteItemModal) closeDeleteItemModal(); });
            document.getElementById('confirmDeleteItem').addEventListener('click', function () {
                if (rowToDelete) {
                    const itemName = rowToDelete.querySelector('.item-name').textContent.trim(); const rowId = rowToDelete.dataset.id; rowToDelete.remove();
                    const mc = document.querySelector(`.pick-consumable[data-id="${rowId}"]`); if (mc) { const mr = mc.closest('tr'); delete mr.dataset.added; mr.style.display = ''; }
                    const tbody = document.querySelector('#tableConsumables tbody');
                    if (tbody.querySelectorAll('tr:not(#emptyRow)').length === 0) { tbody.innerHTML = '<tr id="emptyRow"><td colspan="6" class="py-10 text-center text-gray-400 italic text-sm">Belum ada consumable</td></tr>'; }
                    else { document.querySelectorAll('#tableConsumables tbody tr:not(#emptyRow)').forEach((r, i) => { r.querySelector('.no-col').innerText = i + 1; }); }
                    showNotif(itemName + " berhasil dihapus", "success");
                } closeDeleteItemModal();
            });

            let index = {{ $transaction->items ? $transaction->items->count() : 0 }};

            btnAddConsumable.addEventListener('click', function () {
                const selectedItems = document.querySelectorAll('.pick-consumable:checked');
                if (selectedItems.length === 0) { showModalNotif("Pilih minimal 1 consumable", "error"); return; }
                let hasError = false;
                selectedItems.forEach(s => { if (parseInt(s.closest('tr').querySelector('.qty-input').value) > parseInt(s.dataset.stock)) { showModalNotif("Stock " + s.dataset.name + " hanya tersedia " + s.dataset.stock, "error"); hasError = true; } });
                if (hasError) return;
                const emptyRow = document.getElementById('emptyRow'); if (emptyRow) emptyRow.remove();
                let addedCount = 0, updatedCount = 0; const existingRows = document.querySelectorAll('#tableConsumables tbody tr:not(#emptyRow)'); let startNo = existingRows.length;
                selectedItems.forEach(s => {
                    const row = s.closest('tr'); const id = s.dataset.id; const name = s.dataset.name; const stock = parseInt(s.dataset.stock); const unit = s.dataset.unit; const image = row.querySelector('img').src; const qty = parseInt(row.querySelector('.qty-input').value);
                    const exist = document.querySelector(`#tableConsumables tr[data-id="${id}"]`);
                    if (exist) { exist.querySelector('.qty-input-main').value = qty; exist.querySelector('.qty-input-main').max = stock; exist.querySelector('.hidden-qty').value = qty; updatedCount++; }
                    else {
                        startNo++;
                        document.querySelector('#tableConsumables tbody').insertAdjacentHTML('beforeend', `<tr data-id="${id}" data-stock="${stock}" class="hover:bg-gray-50 transition"><td class="py-3 px-4 text-center font-medium text-gray-600 w-12"><span class="no-col">${startNo}</span></td><td class="py-3 px-4 text-center w-20"><img src="${image}" class="w-10 h-10 object-cover rounded-lg shadow-sm mx-auto"></td><td class="py-3 px-4"><span class="font-semibold text-gray-800 item-name">${name}</span></td><td class="text-center py-3 px-4 w-24"><div class="font-medium text-blue-600 stock-display">${stock}</div><div class="text-xs text-gray-400 unit-display">${unit}</div></td><td class="text-center py-3 px-4 w-32"><input type="number" value="${qty}" min="1" max="${stock}" onchange="updateQty(this)" class="w-20 h-8 text-center border border-gray-300 rounded-lg qty-input-main shadow-sm focus:ring-1 focus:ring-[#5EA6FF] focus:border-[#5EA6FF] focus:outline-none"></td><td class="text-center py-3 px-4 w-16"><button type="button" onclick="removeRow(this)" class="btn-delete-icon" title="Hapus item"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button><input type="hidden" name="items[${index}][consumable_id]" value="${id}"><input type="hidden" name="items[${index}][qty]" value="${qty}" class="hidden-qty"></td></tr>`);
                        index++; addedCount++;
                    }
                    row.dataset.added = 'true'; row.style.display = 'none'; s.checked = false; row.querySelector('.qty-input').value = 1;
                });
                if (selectAllCheckbox) selectAllCheckbox.checked = false;
                window.dispatchEvent(new CustomEvent('close-modal'));
                if (addedCount > 0 && updatedCount === 0) showNotif(addedCount + " consumable ditambahkan", "success");
                else if (addedCount > 0 && updatedCount > 0) showNotif(addedCount + " ditambahkan, " + updatedCount + " diperbarui", "success");
                else if (updatedCount > 0 && addedCount === 0) showNotif(updatedCount + " consumable diperbarui", "success");
            });

            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeDeleteItemModal(); });
            document.querySelectorAll('.preview-click').forEach(img => { img.addEventListener('click', function () { document.getElementById('previewImg').src = this.src; const m = document.getElementById('imagePreviewModal'); m.classList.remove('hidden'); m.classList.add('flex'); }); });
            document.getElementById('closePreviewBtn').addEventListener('click', function () { const m = document.getElementById('imagePreviewModal'); m.classList.add('hidden'); m.classList.remove('flex'); });
        });
    </script>
@endsection