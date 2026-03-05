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

<div class="bg-gradient-to-r from-cyan-600 to-teal-500
            rounded-2xl shadow-lg p-5 mb-6
            flex justify-between items-center">

    {{-- SEARCH & FILTER --}}
    <form method="GET"
      action="{{ route('tools.index') }}"
      class="flex gap-3 items-center">

    <div class="relative">

    <input type="text"
           name="search"
           id="searchInput"
           value="{{ request('search') }}"
           placeholder="Cari barang..."
           class="w-72 bg-white text-gray-700
                  border border-gray-300
                  rounded-xl px-4 py-2 pr-10
                  shadow-sm
                  focus:ring-2 focus:ring-white
                  focus:outline-none">

    @if(request('search'))
        <button type="button"
                id="clearSearch"
                class="absolute right-3 top-1/2 -translate-y-1/2
                       text-gray-400 hover:text-red-500
                       text-sm font-bold">
            ✕
        </button>
    @endif

</div>

   <div class="relative inline-block">

    <select name="condition"
        onchange="this.form.submit()"
        class="appearance-none
               bg-white text-gray-700
               border border-gray-300
               rounded-xl
               pl-4 pr-10 py-2
               shadow-sm
               focus:ring-2 focus:ring-white
               focus:outline-none">

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

    <!-- PANAH MANUAL -->
   <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
    <svg class="w-4 h-4 text-gray-500"
         fill="none"
         stroke="currentColor"
         stroke-width="1.5"
         viewBox="0 0 24 24">
        <path stroke-linecap="round"
              stroke-linejoin="round"
              d="M19 9l-7 7-7-7" />
    </svg>
</div>

</div>

</form>

    <button type="button"
    id="openTambahBarang"
    class="bg-gray-100 text-gray-700
           px-6 py-2.5 rounded-xl
           shadow-[0_4px_12px_rgba(0,0,0,0.15)]
           hover:bg-gray-200
           hover:shadow-[0_6px_18px_rgba(0,0,0,0.2)]
           hover:-translate-y-0.5
           transition duration-200">
    + Tambah Barang
</button>
</div>


<div class="bg-white rounded-2xl shadow-lg overflow-hidden">

    <table class="w-full text-sm">

        <thead class="bg-gradient-to-r from-cyan-600 to-teal-500 text-white">
            <tr>
                <th class="px-6 py-3 text-left">Foto</th>
                <th class="px-6 py-3 text-left">Nama</th>
                <th class="px-6 py-3 text-left">Kategori</th>
                <th class="px-6 py-3 text-left">No Seri</th>
                <th class="px-6 py-3 text-left">Status</th>
                <th class="px-6 py-3 text-left">Kondisi</th>
                <th class="px-6 py-3 text-left">Aksi</th>
            </tr>
        </thead>

        <tbody class="bg-gray-50 divide-y divide-gray-200">

        @foreach ($tools as $tool)

           @php
                $condition = $tool->latestCondition->condition ?? 'baik';
            @endphp

            <tr class="hover:bg-gray-100 transition">

                {{-- FOTO --}}
                <td class="px-6 py-4">
                    <img src="{{ $tool->toolkit->image
                        ? asset('storage/'.$tool->toolkit->image)
                        : asset('images/no-image.png') }}"
                        class="w-12 h-12 object-cover rounded-lg shadow">
                </td>

                {{-- NAMA --}}
                <td class="px-6 py-4 font-medium">
                    {{ $tool->toolkit->toolkit_name }}
                </td>

                {{-- KATEGORI --}}
                <td class="px-6 py-4">
                    <span class="px-3 py-1 text-xs bg-gray-200 rounded-full">
                        {{ $tool->toolkit->category->category_name ?? '-' }}
                    </span>
                </td>

                {{-- NO SERI --}}
                <td class="px-6 py-4">
                    {{ $tool->serial_number }}
                </td>

                {{-- STATUS --}}
                <td class="px-6 py-4">

                    @if (strtolower($tool->status) == 'dipinjam')

                        <span class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full shadow">
                            Dipinjam
                        </span>
                        
                    @elseif (strtolower($tool->status) == 'tersedia')

                    <span class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-full shadow">
                        Tersedia
                    </span>

                    @else
                    <span class="px-3 py-1 text-xs bg-yellow-200 text-yellow-800 rounded-full shadow">
                        Tidak Tersedia
                    </span>
                    @endif
                    
                </td>

                {{-- KONDISI --}}
                <td class="px-6 py-4">
                    @if($condition == 'baik')
                        <span class="px-4 py-1 text-xs border border-green-500 text-green-600 rounded-full">
                            Baik
                        </span>
                    @elseif($condition == 'rusak')
                        <span class="px-4 py-1 text-xs border border-red-500 text-red-600 rounded-full">
                            Rusak
                        </span>
                    @else
                        <span class="px-4 py-1 text-xs border border-gray-400 text-gray-600 rounded-full">
                            Maintenance
                        </span>
                    @endif
                </td>

                {{-- AKSI --}}
                <td class="px-6 py-4 flex gap-3 items-center">

                @if(($tool->latestCondition->condition ?? '') === 'maintenance')
                    <form action="{{ route('tools.finishMaintenance', $tool->id) }}"
                        method="POST">
                        @csrf
                        <button type="submit"
                            class="text-green-600 hover:text-green-800 text-lg transition"
                            title="Selesai Maintenace">
                            ✔
                        </button>
                    </form>
                    
                @endif

                    <button type="button"
                        class="editBtn text-gray-600 hover:text-black transition"
                        data-id="{{ $tool->id }}"
                        data-name="{{ $tool->toolkit->toolkit_name }}"
                        data-category="{{ $tool->toolkit->category_id }}"
                        data-serial="{{ $tool->serial_number }}"
                        data-image="{{ $tool->toolkit->image }}">
                        ✏
                    </button>
                    <form action="{{ route('tools.destroy', $tool->id) }}"
                        method="POST"
                        onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-red-600 hover:text-red-800 transition">
                            🗑
                        </button>
                    </form>
                </td>

            </tr>

        @endforeach

        </tbody>
    </table>
</div>

{{-- ================= MODAL TAMBAH BARANG ================= --}}
<div id="tambahBarangModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="w-11/12 max-w-xl
            bg-[#efefef]
            rounded-2xl
            shadow-[0_15px_40px_rgba(0,0,0,0.25)]
            p-8 relative">          

        <form action="{{ route('tools.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @if ($errors->any())
    <div class="mb-4 bg-red-100 text-red-700 p-3 rounded">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

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
                       class="w-full
                        bg-[#f5f5f5]
                        border border-gray-400
                        rounded-xl
                        px-5 py-3
                        shadow-[0_6px_10px_rgba(0,0,0,0.15)]
                        focus:outline-none
                        focus:ring-0
                        transition">

                <select name="category_id"
                    required
                    class="w-full
                    bg-[#f5f5f5]
                    border border-gray-400
                    rounded-xl
                    px-5 py-3
                    shadow-[0_6px_10px_rgba(0,0,0,0.15)]
                    focus:outline-none
                    focus:ring-0
                    transition">

                <option value="">Pilih Kategori</option>

                @foreach($categories as $category)
                    <option value="{{ $category->id }}">
                        {{ $category->category_name }}
                    </option>
                @endforeach

            </select>

                <input type="text"
                       name="serial_number"
                       placeholder="No Seri"
                       class="w-full bg-white
                        border border-gray-300
                        rounded-xl
                        px-5 py-3
                        shadow-[0_4px_10px_rgba(0,0,0,0.08)]
                        focus:ring-2 focus:ring-cyan-500
                        focus:outline-none
                        transition">

                <input type="file"
                name="image"
                class="bg-[#e6e6e6]
                        border border-gray-400
                        rounded-lg
                        px-4 py-2
                        shadow-sm
                        cursor-pointer">

            </div>

            <!-- Footer -->
           <div class="flex justify-end gap-4 mt-8">

                <button type="button"
                    id="cancelTambahBarang"
                    class="px-6 py-2.5
                        bg-[#dcdcdc]
                        text-gray-800
                        rounded-xl
                        shadow-[0_6px_10px_rgba(0,0,0,0.2)]
                        hover:bg-[#cfcfcf]
                        transition">
                    Batal
                </button>

                <button type="submit"
                    class="px-6 py-2.5
                        bg-[#e0e0e0]
                        text-black
                        rounded-xl
                        shadow-[0_6px_12px_rgba(0,0,0,0.25)]
                        hover:bg-[#d5d5d5]
                        transition">
                    Tambah
                </button>

            </div>

        </form>

    </div>
</div>

{{-- ================= MODAL EDIT BARANG ================= --}}
<div id="editBarangModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="w-11/12 max-w-xl
            bg-[#efefef]
            rounded-2xl
            shadow-[0_15px_40px_rgba(0,0,0,0.25)]
            p-8 relative">  

        <form id="editBarangForm" method="POST" enctype="multipart/form-data">
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
                       class="w-full
                        bg-[#f5f5f5]
                        border border-gray-400
                        rounded-xl
                        px-5 py-3
                        shadow-[0_6px_10px_rgba(0,0,0,0.15)]
                        focus:outline-none
                        focus:ring-0
                        transition">

                <select name="category_id"
                        id="editCategory"
                        class="w-full
                        bg-[#f5f5f5]
                        border border-gray-400
                        rounded-xl
                        px-5 py-3
                        shadow-[0_6px_10px_rgba(0,0,0,0.15)]
                        focus:outline-none
                        focus:ring-0
                        transition">    

                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">
                            {{ $category->category_name }}
                        </option>
                    @endforeach

                </select>

                <input type="text"
                       name="serial_number"
                       id="editSerial"
                       class="w-full
                        bg-[#f5f5f5]
                        border border-gray-400
                        rounded-xl
                        px-5 py-3
                        shadow-[0_6px_10px_rgba(0,0,0,0.15)]
                        focus:outline-none
                        focus:ring-0
                        transition">

                <div>
                        <input type="file"
                        name="image"
                        class="bg-[#e6e6e6]
                                border border-gray-400
                                rounded-lg
                                px-4 py-2
                                shadow-sm
                                cursor-pointer">

                    
                </div>
            </div>

            

            <!-- Footer -->
            <div class="flex justify-end gap-4 mt-8">

                <button type="button"
                    id="cancelTambahBarang"
                    class="px-6 py-2.5
                        bg-[#dcdcdc]
                        text-gray-800
                        rounded-xl
                        shadow-[0_6px_10px_rgba(0,0,0,0.2)]
                        hover:bg-[#cfcfcf]
                        transition">
                    Batal
                </button>

                <button type="submit"
                    class="px-6 py-2.5
                        bg-[#e0e0e0]
                        text-black
                        rounded-xl
                        shadow-[0_6px_12px_rgba(0,0,0,0.25)]
                        hover:bg-[#d5d5d5]
                        transition">
                    Tambah
                </button>

            </div>
        </form>

    </div>
</div>

{{-- ================= STYLE & SCRIPT ================= --}}

<style>
/* =================================================
   GLOBAL
================================================= */
body {
    background-color: #f4f8fb;
}

h2 {
    font-size: 26px !important;
    font-weight: 700;
    color: #169fb2;
    letter-spacing: 0.2px;
}

/* =================================================
   HEADER BAR (SEARCH AREA)
================================================= */
.bg-gradient-to-r {
    background: linear-gradient(
        135deg,
        #63d3e3 0%,
        #4fbecb 45%,
        #3fa9b5 100%
    ) !important;
}

.bg-gradient-to-r {
    padding: 22px !important;
}

/* =================================================
   SEARCH INPUT & SELECT
================================================= */
input[name="search"],
select[name="condition"] {
    height: 44px;
    font-size: 13px;
    border-radius: 14px !important;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    box-shadow: 0 6px 14px rgba(0,0,0,0.08);
}

input[name="search"]::placeholder {
    color: #9ca3af;
}

/* =================================================
   ADD BUTTON
================================================= */
#openTambahBarang {
    background: #ffffff;
    font-weight: 600;
    border-radius: 14px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

#openTambahBarang:hover {
    background: #f8fafc;
}

/* =================================================
   TABLE CONTAINER
================================================= */
table {
    border-collapse: separate !important;
    border-spacing: 0;
}

thead tr {
    background: linear-gradient(
        135deg,
        #5fd0df 0%,
        #47b9c7 100%
    ) !important;
}

thead th {
    font-size: 13px;
    font-weight: 600;
    color: #ffffff;
    padding-top: 16px !important;
    padding-bottom: 16px !important;
}

/* =================================================
   TABLE BODY
================================================= */
tbody tr {
    background: #ffffff;
}

tbody tr:hover {
    background: #eef8fb !important;
}

td {
    vertical-align: middle;
}

/* =================================================
   IMAGE
================================================= */
td img {
    width: 46px;
    height: 46px;
    background: #ffffff;
    padding: 4px;
    border-radius: 12px;
    box-shadow: 0 6px 14px rgba(0,0,0,0.12);
}

/* =================================================
   BADGE GENERAL
================================================= */
td span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 92px;
    padding: 6px 12px !important;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 999px;
}

/* =================================================
   STATUS COLOR
================================================= */
.bg-green-100 {
    background: #e6f7ef !important;
    color: #1f9254 !important;
}

.bg-blue-100 {
    background: #e9f2ff !important;
    color: #1e40af !important;
}

.bg-yellow-200,
.bg-yellow-100 {
    background: #fff4da !important;
    color: #9a6a19 !important;
}

/* =================================================
   KONDISI OUTLINE
================================================= */
.border-green-500 {
    border: 1.8px solid #4fbf9c !important;
    color: #1f9254 !important;
}

.border-red-500 {
    border: 1.8px solid #ef6b6b !important;
    color: #b42318 !important;
}

.border-gray-400 {
    border: 1.8px solid #9ca3af !important;
    color: #4b5563 !important;
}

/* =================================================
   ACTION ICON
================================================= */
td button {
    font-size: 17px;
}

td button:hover {
    transform: scale(1.15);
}

/* =================================================
   MODAL
================================================= */
#tambahBarangModal > div,
#editBarangModal > div {
    background: linear-gradient(
        180deg,
        #f7f7f7 0%,
        #eeeeee 100%
    ) !important;
    border-radius: 22px !important;
}

#tambahBarangModal h2,
#editBarangModal h2 {
    font-size: 18px;
    font-weight: 700;
    color: #169fb2;
}

/* =================================================
   MODAL INPUT
================================================= */
#tambahBarangModal input,
#tambahBarangModal select,
#editBarangModal input,
#editBarangModal select {
    border-radius: 14px;
    font-size: 14px;
}

/* =================================================
   BUTTON
================================================= */
button[type="submit"] {
    border-radius: 14px;
    font-weight: 600;
}

/* =================================================
   SHADOW GLOBAL
================================================= */
.shadow-lg {
    box-shadow: 0 18px 40px rgba(0,0,0,0.18) !important;
}
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
    const clearBtn = document.getElementById('clearSearch');
const searchInput = document.getElementById('searchInput');

if (clearBtn) {
    clearBtn.addEventListener('click', function () {
        searchInput.value = '';
        window.location.href = "{{ route('tools.index') }}";
    });
}

   
document.addEventListener('click', function (e) {

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