@extends('layouts.app')

@section('content')
<div x-data="{ openModal: false }" class="min-h-screen bg-gray-100 p-8">

    <h1 class="text-2xl font-semibold mb-6">Edit Peminjaman Tools</h1>

    {{-- Flash Message --}}
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- ================= FORM UPDATE ================= --}}
    <div class="bg-white rounded-xl shadow-md p-6 mb-8">
        <form action="{{ route('peminjaman.update', $transaction->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Nama Peminjam</label>
                    <input type="text"
                        name="borrower_name"
                        value="{{ $transaction->borrower_name }}"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Tanggal</label>
                    <input type="date"
                        name="date"
                        value="{{ $transaction->date->format('Y-m-d') }}"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none"
                        required>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6 mb-6">
                <input type="text"
                    name="client_name"
                    value="{{ $transaction->client_name }}"
                    placeholder="Nama Client"
                    class="border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">

                <input type="text"
                    name="project"
                    value="{{ $transaction->project }}"
                    placeholder="Proyek"
                    class="border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">

                <input type="text"
                    name="purpose"
                    value="{{ $transaction->purpose }}"
                    placeholder="Keperluan"
                    class="border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                    Simpan Perubahan
                </button>

                <a href="{{ route('peminjaman.index') }}"
                    class="bg-gray-500 text-white px-5 py-2 rounded-lg shadow hover:bg-gray-600 transition">
                    Kembali
                </a>
            </div>
        </form>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="bg-white rounded-xl shadow-md p-6">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Daftar Alat yang Dipinjam</h2>

            <button type="button"
                id="openModalBtn"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                + Pilih Tools
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <th class="px-6 py-3">No</th>
                        <th class="px-6 py-3">Image</th>
                        <th class="px-6 py-3">Nama Tools</th>
                        <th class="px-6 py-3">No Seri</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($transaction->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">{{ $loop->iteration }}</td>

                            <td class="px-6 py-3">
                                @if ($item->toolkit->image)
                                    <img src="{{ asset('storage/'.$item->toolkit->image) }}"
                                         class="w-14 h-14 object-contain rounded border">
                                @else
                                    -
                                @endif
                            </td>

                            <td class="px-6 py-3">{{ $item->toolkit->toolkit_name }}</td>
                            <td class="px-6 py-3">{{ $item->serial->serial_number }}</td>

                            <td class="px-6 py-3">
                                <form action="{{ route('peminjaman.item.destroy', $item->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus item ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-400 py-6">
                                Belum ada barang yang ditambahkan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

 {{-- ================= MODAL ================= --}}
<div id="toolsModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white w-11/12 max-w-6xl rounded-xl shadow-xl p-6 relative">

        <form action="{{ route('peminjaman.item.add', $transaction->id) }}"
              method="POST">
            @csrf

            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Pilih Tools Tersedia</h2>
                <button type="button"
                        id="closeModalBtn"
                        class="text-gray-500 hover:text-gray-700 text-xl">
                    ✕
                </button>
            </div>

            <!-- Search -->
            <div class="mb-4">
                <input type="text"
                    id="searchToolsEdit"
                    placeholder="Cari nama tools atau no seri..."
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <!-- Table -->
            <div class="overflow-y-auto max-h-100 border rounded-lg">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600 uppercase text-xs">
                            <th class="px-4 py-3"></th>
                            <th class="px-4 py-3">Image</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">No Seri</th>
                        </tr>
                    </thead>
                    <tbody id="toolsTableEdit" class="divide-y divide-gray-200">

                        @forelse ($serials as $serial)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox"
                                           name="serial_ids[]"
                                           value="{{ $serial->id }}"
                                           class="w-4 h-4 text-blue-600 rounded">
                                </td>

                                <td class="px-4 py-3">
                                    @if ($serial->toolkit->image)
                                        <img src="{{ asset('storage/'.$serial->toolkit->image) }}"
                                             class="w-14 h-14 object-contain rounded border">
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    {{ $serial->toolkit->toolkit_name }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $serial->serial_number }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-gray-400 py-6">
                                    Tidak ada tools tersedia
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 mt-5">
                <button type="button"
                        id="cancelModalBtn"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>

                <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                    + Tambahkan
                </button>
            </div>

        </form>

    </div>
</div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('toolsModal');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelModalBtn');

    // Open Modal
    openBtn.addEventListener('click', function () {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    // Close Modal
    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    // Close when click outside
    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Close with ESC
    document.addEventListener('keydown', function (e) {
        if (e.key === "Escape") {
            closeModal();
        }
    });

    const searchInput = document.getElementById('searchToolsEdit');

    if(searchInput){
        searchInput.addEventListener('keyup', function (){

            let keyword =this.value.toLowerCase();

            document.querySelectorAll('#toolsTableEdit tr').forEach(function (row){

                row.style.display =
                    row.innerText.toLowerCase().includes(keyword)
                    ? ''
                    : 'none';
            })
        })
    }

});
</script>
@endsection