@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto w-full px-6 py-4">

    {{-- JUDUL HALAMAN + SUBTITLE --}}
    <div class="mb-5">
        <h1 class="tools-page-title">Data Tools</h1>
        <p class="tools-page-subtitle">Kelola semua data peralatan, inventaris, dan kondisi barang di satu tempat.</p>
    </div>

    {{-- ALERT --}}
    @if (session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 text-green-700 border border-green-200 flex items-center gap-2">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if (session('error'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 border border-red-200 flex items-center gap-2">
        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('error') }}
    </div>
    @endif


    <div class="tools-header-bar rounded-2xl shadow-md mb-6">
        <div class="tools-header-inner">

            <form method="GET"
                action="{{ route('tools.index') }}"
                class="tools-header-form">

                <div class="tools-search-wrap relative">
                    <svg class="tools-search-icon absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>

                    <input type="text"
                        name="search"
                        id="searchInput"
                        value="{{ request('search') }}"
                        placeholder="Cari barang..."
                        class="tools-search-input">

                    @if(request('search'))
                    <button type="button"
                        id="clearSearch"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500 text-sm font-bold">
                        ✕
                    </button>
                    @endif
                </div>

                <div class="tools-filter-wrap relative">
                    <select name="condition"
                        onchange="this.form.submit()"
                        class="tools-filter-select">

                        <option value="">Semua Kondisi</option>
                        <option value="baik" {{ request('condition') === 'baik' ? 'selected' : '' }}>Baik</option>
                        <option value="rusak" {{ request('condition') === 'rusak' ? 'selected' : '' }}>Rusak</option>
                        <option value="maintenance" {{ request('condition') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>

                    <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

            </form>

            <button type="button"
                id="openTambahBarang"
                class="tools-add-btn">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Barang
            </button>

        </div>
    </div>


    <div class="bg-white rounded-2xl tools-shadow overflow-hidden min-h-[400px]">

        <div class="overflow-x-auto">

            <table class="w-full text-sm min-w-[700px] table-auto">

                <thead>
                    <tr class="tools-thead-row">
                        <th class="tools-th tools-th-foto">Foto</th>
                        <th class="tools-th tools-th-nama">Nama</th>
                        <th class="tools-th tools-th-kategori hidden md:table-cell">Kategori</th>
                        <th class="tools-th tools-th-seri hidden md:table-cell">No Seri</th>
                        <th class="tools-th tools-th-status">Status</th>
                        <th class="tools-th tools-th-kondisi hidden md:table-cell">Kondisi</th>
                        <th class="tools-th tools-th-aksi">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-100">

                    @forelse ($tools as $tool)

                    @php
                    $condition = $tool->latestCondition->condition ?? 'baik';
                    @endphp

                    <tr class="tools-row hover:bg-cyan-50 transition h-[70px]">

                        <td class="tools-td-foto">
                            <img
                                src="{{ $tool->toolkit->image ? asset('storage/'.$tool->toolkit->image) : asset('images/no-image.png') }}"
                                class="tools-img w-12 h-12 min-w-[48px] min-h-[48px] object-contain bg-white rounded-lg shadow p-1 cursor-pointer previewImage"
                                onerror="this.src='{{ asset('images/no-image.png') }}'">
                        </td>

                        <td class="tools-td-nama font-semibold text-gray-800">
                            {{ $tool->toolkit->toolkit_name }}
                        </td>

                        <td class="tools-td-center hidden md:table-cell">
                            <span class="tools-badge tools-badge-category">
                                {{ $tool->toolkit->category->category_name ?? '-' }}
                            </span>
                        </td>

                        <td class="tools-td-center font-medium text-gray-700 hidden md:table-cell">
                            {{ $tool->serial_number }}
                        </td>

                        <td class="tools-td-center">
                            @if (strtolower($tool->status) == 'dipinjam')
                            <span class="tools-badge tools-badge-dipinjam">Dipinjam</span>
                            @elseif (strtolower($tool->status) == 'tersedia')
                            <span class="tools-badge tools-badge-tersedia">Tersedia</span>
                            @else
                            <span class="tools-badge tools-badge-tidak">Tidak Tersedia</span>
                            @endif
                        </td>

                        <td class="tools-td-center hidden md:table-cell">
                            @if($condition == 'baik')
                            <span class="tools-badge tools-kondisi-baik">Baik</span>
                            @elseif($condition == 'rusak')
                            <span class="tools-badge tools-kondisi-rusak">Rusak</span>
                            @else
                            <span class="tools-badge tools-kondisi-maint">Maintenance</span>
                            @endif
                        </td>

                        <td class="tools-td-aksi">
                            <div class="flex justify-center items-center gap-2 sm:gap-3">

                                @if(($tool->latestCondition->condition ?? '') === 'maintenance')
                                <form action="{{ route('tools.finishMaintenance', $tool->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="tools-icon-btn tools-icon-done"
                                        title="Selesai Maintenance">
                                        <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                </form>
                                @endif

                                <button type="button"
                                    class="editBtn tools-icon-btn tools-icon-edit"
                                    data-id="{{ $tool->id }}"
                                    data-name="{{ $tool->toolkit->toolkit_name }}"
                                    data-category="{{ $tool->toolkit->category_id }}"
                                    data-serial="{{ $tool->serial_number }}"
                                    data-image="{{ $tool->toolkit->image }}"
                                    title="Edit Barang">
                                    <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                    </svg>
                                </button>

                                @if (strtolower($tool->status) == 'dipinjam')
                                <button
                                    type="button"
                                    onclick="alert('Barang sedang dipinjam, tidak bisa dihapus!')"
                                    class="tools-icon-btn tools-icon-delete opacity-40 cursor-not-allowed"
                                    title="Barang sedang dipinjam">
                                    <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9" />
                                    </svg>
                                </button>
                                @else
                                <form action="{{ route('tools.destroy', $tool->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="tools-icon-btn tools-icon-delete"
                                        title="Hapus Barang">
                                        <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 7h12M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-7 0h8l-1 13a2 2 0 01-2 2H11a2 2 0 01-2-2L8 7z" />
                                        </svg>
                                    </button>
                                </form>
                                @endif

                            </div>
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="7" class="text-center py-10 text-gray-400 italic">
                            <div class="flex flex-col itmes-center gap-2">
                                <div class="text-4xl">📦</div>
                                <p class="text-gray-500">Tidak ada data tool</p>
                            </div>
                        </td>
                    </tr>

                    @endforelse

                </tbody>
            </table>

        </div>

    </div>

    <div class="mt-6 flex justify-center">
        {{ $tools->links() }}
    </div>

    {{-- ================= MODAL TAMBAH BARANG ================= --}}
    <div id="tambahBarangModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center z-[1000]">

        <div class="tools-modal-box w-11/12 max-w-xl
            bg-[#efefef]
            rounded-2xl
            shadow-[0_15px_40px_rgba(0,0,0,0.25)]
            p-6 sm:p-8 relative">

            <form action="{{ route('tools.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @if ($errors->any())
                <div class="mb-4 bg-red-100 text-red-700 p-3 rounded-lg">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="flex justify-between items-center mb-4">
                    <h2 class="tools-modal-title text-lg font-semibold">Tambah Barang</h2>
                    <button type="button" id="closeTambahBarang" class="tools-modal-close text-gray-500 hover:text-gray-700 text-xl">✕</button>
                </div>

                <div class="space-y-4">
                    <input type="text" name="toolkit_name" placeholder="Nama Barang"
                        class="tools-modal-input w-full bg-[#f5f5f5] border border-gray-400 rounded-xl px-5 py-3 shadow-[0_6px_10px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-0 transition">

                    <select name="category_id" required
                        class="tools-modal-input w-full bg-[#f5f5f5] border border-gray-400 rounded-xl px-5 py-3 shadow-[0_6px_10px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-0 transition">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>

                    <input type="text" name="serial_number" placeholder="No Seri"
                        class="tools-modal-input w-full bg-white border border-gray-300 rounded-xl px-5 py-3 shadow-[0_4px_10px_rgba(0,0,0,0.08)] focus:ring-2 focus:ring-cyan-500 focus:outline-none transition">

                    @error('serial_number')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror

                    <input type="file" name="image"
                        class="tools-modal-file bg-[#e6e6e6] border border-gray-400 rounded-lg px-4 py-2 shadow-sm cursor-pointer">
                </div>

                <div class="flex justify-end gap-4 mt-8">
                    <button type="button" id="cancelTambahBarang"
                        class="tools-modal-btn-cancel px-6 py-2.5 bg-[#dcdcdc] text-gray-800 rounded-xl shadow-[0_6px_10px_rgba(0,0,0,0.2)] hover:bg-[#cfcfcf] transition">Batal</button>
                    <button type="submit"
                        class="tools-modal-btn-submit px-6 py-2.5 bg-[#e0e0e0] text-black rounded-xl shadow-[0_6px_12px_rgba(0,0,0,0.25)] hover:bg-[#d5d5d5] transition">Tambah</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL EDIT BARANG ================= --}}
    <div id="editBarangModal"
        class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

        <div class="tools-modal-box w-11/12 max-w-xl
            bg-[#efefef]
            rounded-2xl
            shadow-[0_15px_40px_rgba(0,0,0,0.25)]
            p-6 sm:p-8 relative">

            <form id="editBarangForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="flex justify-between items-center mb-4">
                    <h2 class="tools-modal-title text-lg font-semibold">Edit Barang</h2>
                    <button type="button" id="closeEditModal" class="tools-modal-close text-gray-500 hover:text-gray-700 text-xl">✕</button>
                </div>

                <div class="space-y-4">
                    <input type="text" name="toolkit_name" id="editName"
                        class="tools-modal-input w-full bg-[#f5f5f5] border border-gray-400 rounded-xl px-5 py-3 shadow-[0_6px_10px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-0 transition">

                    <select name="category_id" id="editCategory"
                        class="tools-modal-input w-full bg-[#f5f5f5] border border-gray-400 rounded-xl px-5 py-3 shadow-[0_6px_10px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-0 transition">
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>

                    <input type="text" name="serial_number" id="editSerial"
                        class="tools-modal-input w-full bg-[#f5f5f5] border border-gray-400 rounded-xl px-5 py-3 shadow-[0_6px_10px_rgba(0,0,0,0.15)] focus:outline-none focus:ring-0 transition">

                    <div>
                        <input type="file" name="image"
                            class="tools-modal-file bg-[#e6e6e6] border border-gray-400 rounded-lg px-4 py-2 shadow-sm cursor-pointer">
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-8">
                    <button type="button" id="cancelEditModal"
                        class="tools-modal-btn-cancel px-6 py-2.5 bg-[#dcdcdc] text-gray-800 rounded-xl shadow-[0_6px_10px_rgba(0,0,0,0.2)] hover:bg-[#cfcfcf] transition">Batal</button>
                    <button type="submit"
                        class="tools-modal-btn-submit px-6 py-2.5 bg-[#e0e0e0] text-black rounded-xl shadow-[0_6px_12px_rgba(0,0,0,0.25)] hover:bg-[#d5d5d5] transition">Tambah</button>
                </div>
            </form>
        </div>
    </div>

    <div id="imagePreviewModal"
        class="fixed inset-0 bg-black/70 hidden items-center justify-center z-[999]">
        <div class="relative">
            <button id="closePreview" class="absolute -top-10 right-0 text-white text-3xl hover:scale-110 transition">✕</button>
            <img id="previewImg" class="max-w-[500px] max-h-[400px] object-contain rounded-xl shadow-2xl bg-white p-6">
        </div>
    </div>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .max-w-7xl {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .tools-page-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: #0e7490;
            letter-spacing: -0.3px;
            line-height: 1.2;
            margin: 0;
        }

        .tools-page-subtitle {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: #64748b;
            margin: 4px 0 0 0;
            line-height: 1.4;
        }

        .tools-header-bar {
            background: linear-gradient(180deg, #5fd0df 0%, #22a8b8 100%);
            padding: 0;
            box-shadow: 0 4px 16px rgba(34, 168, 184, 0.25);
        }

        .tools-header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 24px;
        }

        .tools-header-form {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 0;
        }

        .tools-search-wrap {
            flex: 0 0 280px;
        }

        .tools-search-input {
            font-family: 'Plus Jakarta Sans', sans-serif;
            width: 100%;
            height: 42px;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 0 36px 0 38px;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .tools-search-input:focus {
            border-color: #ffffff;
            box-shadow: 0 2px 12px rgba(255, 255, 255, 0.3);
        }

        .tools-search-input::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        .tools-filter-wrap {
            flex: 0 0 180px;
        }

        .tools-filter-select {
            font-family: 'Plus Jakarta Sans', sans-serif;
            width: 100%;
            height: 42px;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 0 36px 0 14px;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            outline: none;
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .tools-filter-select:focus {
            border-color: #ffffff;
            box-shadow: 0 2px 12px rgba(255, 255, 255, 0.3);
        }

        .tools-add-btn {
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            height: 42px;
            padding: 0 22px;
            background: #ffffff;
            color: #374151;
            font-size: 13.5px;
            font-weight: 600;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .tools-add-btn:hover {
            background: #f8fafc;
            box-shadow: 0 5px 16px rgba(0, 0, 0, 0.14);
            transform: translateY(-1px);
        }

        .tools-add-btn svg {
            flex-shrink: 0;
        }

        .tools-thead-row {
            background: linear-gradient(180deg, #5fd0df 0%, #1ca7b6 100%);
        }

        .tools-th {
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 15px 16px;
            font-size: 11px;
            font-weight: 700;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            white-space: nowrap;
            border: none;
            line-height: 1;
        }

        .tools-th-foto {
            width: 80px;
            text-align: center;
        }

        .tools-th-nama {
            text-align: left;
            padding-left: 24px;
        }

        .tools-th-kategori {
            text-align: center;
        }

        .tools-th-seri {
            text-align: center;
        }

        .tools-th-status {
            text-align: center;
        }

        .tools-th-kondisi {
            text-align: center;
        }

        .tools-th-aksi {
            width: 110px;
            text-align: center;
        }

        .tools-row {
            background: #ffffff;
            transition: background 0.15s ease;
        }

        .tools-row:hover {
            background: #f0fafa;
        }

        .tools-row td {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .tools-td-foto {
            width: 80px;
            padding: 12px 8px;
            text-align: center;
            vertical-align: middle;
        }

        .tools-td-nama {
            padding: 14px 24px;
            vertical-align: middle;
            font-size: 14px;
            font-weight: 600;
        }

        .tools-td-center {
            padding: 14px 16px;
            text-align: center;
            vertical-align: middle;
            font-size: 13px;
        }

        .tools-td-aksi {
            width: 110px;
            padding: 14px 8px;
            text-align: center;
            vertical-align: middle;
        }

        .tools-img {
            width: 46px;
            height: 46px;
            background: #fff;
            padding: 4px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .tools-img:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        }

        .tools-badge {
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 90px;
            padding: 5px 14px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 999px;
            letter-spacing: 0.15px;
            transition: transform 0.15s ease;
        }

        .tools-badge:hover {
            transform: scale(1.05);
        }

        .tools-badge-category {
            background: #f1f5f9;
            color: #475569;
            min-width: auto;
            padding: 4px 12px;
            font-size: 11px;
            font-weight: 500;
        }

        .tools-badge-tersedia {
            background: #dcfce7;
            color: #15803d;
        }

        .tools-badge-dipinjam {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .tools-badge-tidak {
            background: #fef3c7;
            color: #92400e;
        }

        .tools-kondisi-baik {
            border: 1.5px solid #4ade80;
            color: #15803d;
            background: #f0fdf4;
        }

        .tools-kondisi-rusak {
            border: 1.5px solid #f87171;
            color: #b91c1c;
            background: #fef2f2;
        }

        .tools-kondisi-maint {
            border: 1.5px solid #a1a1aa;
            color: #52525b;
            background: #f4f4f5;
        }

        .tools-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .tools-icon-btn svg {
            display: block;
        }

        .tools-icon-done {
            color: #22c55e;
            background: #f0fdf4;
        }

        .tools-icon-done:hover {
            color: #16a34a;
            background: #dcfce7;
            transform: scale(1.1);
        }

        .tools-icon-edit {
            color: #6b7280;
            background: #f9fafb;
        }

        .tools-icon-edit:hover {
            color: #3b82f6;
            background: #eff6ff;
            transform: scale(1.1);
        }

        .tools-icon-delete {
            color: #6b7280;
            background: #f9fafb;
        }

        .tools-icon-delete:hover {
            color: #ef4444;
            background: #fef2f2;
            transform: scale(1.1);
        }

        .tools-modal-box {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(180deg, #f7f7f7 0%, #eeeeee 100%) !important;
            border-radius: 22px !important;
            animation: toolsModalIn 0.22s ease-out;
        }

        @keyframes toolsModalIn {
            from {
                opacity: 0;
                transform: translateY(-14px) scale(0.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .tools-modal-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: #1CA7B6;
        }

        .tools-modal-close {
            transition: transform 0.15s ease;
        }

        .tools-modal-close:hover {
            transform: scale(1.2);
        }

        .tools-modal-input {
            font-family: 'Plus Jakarta Sans', sans-serif;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 500;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .tools-modal-input:focus {
            border-color: #3fb2c8 !important;
            box-shadow: 0 0 0 3px rgba(63, 178, 200, 0.15) !important;
        }

        .tools-modal-input::placeholder {
            font-weight: 400;
            color: #9ca3af;
        }

        .tools-modal-file {
            font-family: 'Plus Jakarta Sans', sans-serif;
            border-radius: 12px;
            font-size: 13px;
        }

        .tools-modal-btn-cancel {
            font-family: 'Plus Jakarta Sans', sans-serif;
            border-radius: 14px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }

        .tools-modal-btn-cancel:hover {
            background: #c5c5c5 !important;
        }

        .tools-modal-btn-submit {
            font-family: 'Plus Jakarta Sans', sans-serif;
            border-radius: 14px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }

        .tools-modal-btn-submit:hover {
            background: #cbcbcb !important;
        }

        .tools-shadow {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .overflow-x-auto {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .overflow-x-auto::-webkit-scrollbar {
            height: 5px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        @media (max-width: 767px) {
            .tools-header-inner {
                flex-direction: column;
                align-items: stretch;
                padding: 16px;
                gap: 12px;
            }

            .tools-header-form {
                flex-direction: column;
                gap: 10px;
            }

            .tools-search-wrap,
            .tools-filter-wrap {
                flex: 1 1 100%;
            }

            .tools-add-btn {
                width: 100%;
            }
        }
    </style>


    @php
    $hasErrors = $errors->any();
    @endphp

    {{-- ================= SCRIPT ================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const tambahModal = document.getElementById('tambahBarangModal');
            const openTambahBtn = document.getElementById('openTambahBarang');
            const closeTambahBtn = document.getElementById('closeTambahBarang');
            const cancelTambahBtn = document.getElementById('cancelTambahBarang');

            if (openTambahBtn) {
                openTambahBtn.addEventListener('click', function() {
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

            tambahModal?.addEventListener('click', function(e) {
                if (e.target === tambahModal) closeTambah();
            });


            const editModal = document.getElementById('editBarangModal');
            const editForm = document.getElementById('editBarangForm');
            const editName = document.getElementById('editName');
            const editCategory = document.getElementById('editCategory');
            const editSerial = document.getElementById('editSerial');
            const closeEditBtn = document.getElementById('closeEditModal');
            const cancelEditBtn = document.getElementById('cancelEditModal');
            const clearBtn = document.getElementById('clearSearch');
            const searchInput = document.getElementById('searchInput');

            if (clearBtn) {
                clearBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    window.location.href = "{{ route('tools.index') }}";
                });
            }

            document.addEventListener('click', function(e) {
                const button = e.target.closest('.editBtn');
                if (!button) return;

                editName.value = button.dataset.name;
                editCategory.value = button.dataset.category;
                editSerial.value = button.dataset.serial;
                editForm.action = '/data-tools/' + button.dataset.id;

                editModal.classList.remove('hidden');
                editModal.classList.add('flex');
            });

            function closeEdit() {
                editModal.classList.add('hidden');
                editModal.classList.remove('flex');
            }

            closeEditBtn?.addEventListener('click', closeEdit);
            cancelEditBtn?.addEventListener('click', closeEdit);

            editModal?.addEventListener('click', function(e) {
                if (e.target === editModal) closeEdit();
            });


            const previewModal = document.getElementById("imagePreviewModal");
            const previewImg = document.getElementById("previewImg");
            const closePreview = document.getElementById("closePreview");

            document.querySelectorAll(".previewImage").forEach(img => {
                img.addEventListener("click", function() {
                    previewImg.src = this.src;
                    previewModal.classList.remove("hidden");
                    previewModal.classList.add("flex");
                });
            });

            closePreview?.addEventListener("click", () => {
                previewModal.classList.add("hidden");
                previewModal.classList.remove("flex");
            });

            previewModal?.addEventListener("click", e => {
                if (e.target === previewModal) {
                    previewModal.classList.add("hidden");
                    previewModal.classList.remove("flex");
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === "Escape") {
                    closeTambah();
                    closeEdit();
                }
            });

            setTimeout(() => {
                document.querySelectorAll('[class*="bg-green"], [class*="bg-red"]').forEach(el => {
                    el.style.display = 'none';
                });
            }, 3000);

            const hasErrors = @json($errors->any());

            if (hasErrors) {
                const modal = document.getElementById('tambahBarangModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

        });
    </script>

</div>
@endsection