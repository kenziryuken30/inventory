@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- ================= TITLE ================= --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-[#268397]">
                Edit Peminjaman Tools
            </h2>
            <p class="text-gray-500 text-sm">
                Edit Proses Peminjaman dan Daftar Tools yang dipinjam
            </p>
        </div>

        <a href="{{ route('peminjaman.index') }}"
           class="bg-gray-200 hover:bg-gray-300 text-sm px-4 py-2 rounded-lg shadow">
            ← Kembali
        </a>
    </div>

    {{-- ================= ALERT ================= --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif


    {{-- ================= PANEL BESAR ================= --}}
<div class="bg-white rounded-2xl shadow-xl p-8 space-y-10">

    {{-- ================= FORM UPDATE (HEADER ONLY) ================= --}}
    <form id="updateForm"
          action="{{ route('peminjaman.update', $transaction->id) }}"
          method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-6">

            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <label class="text-sm text-gray-600">Nama Peminjam</label>
                    <input type="text"
                           name="borrower_name"
                           value="{{ $transaction->borrower_name }}"
                           class="w-full mt-2 px-4 py-3 rounded-xl border border-gray-200">
                </div>

                <div>
                    <label class="text-sm text-gray-600">Tanggal</label>
                    <input type="date"
                           name="date"
                           value="{{ $transaction->date->format('Y-m-d') }}"
                           class="w-full mt-2 px-4 py-3 rounded-xl border border-gray-200">
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <label class="text-sm text-gray-600">Nama Client</label>
                    <input type="text"
                           name="client_name"
                           value="{{ $transaction->client_name }}"
                           class="w-full mt-2 px-4 py-3 rounded-xl border border-gray-200">
                </div>

                <div>
                    <label class="text-sm text-gray-600">Proyek</label>
                    <input type="text"
                           name="project"
                           value="{{ $transaction->project }}"
                           class="w-full mt-2 px-4 py-3 rounded-xl border border-gray-200">
                </div>

                <div>
                    <label class="text-sm text-gray-600">Keperluan</label>
                    <input type="text"
                           name="purpose"
                           value="{{ $transaction->purpose }}"
                           class="w-full mt-2 px-4 py-3 rounded-xl border border-gray-200">
                </div>
            </div>

        </div>
    </form>
    {{-- END FORM UPDATE --}}


    {{-- ================= DAFTAR TOOLS ================= --}}
    <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">

        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-gray-700">Daftar Tools</h3>

            <button type="button"
                    id="openToolsBtn"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700">
                + Pilih Tools
            </button>
        </div>

        <div class="rounded-xl overflow-hidden border">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-white bg-[linear-gradient(180deg,#268397_0%,#4CCAE6_100%)]">
                        <th class="py-3 text-center">NO</th>
                        <th class="py-3 text-center">Image</th>
                        <th class="py-3 text-center">Nama Tools</th>
                        <th class="py-3 text-center">No Seri</th>
                        <th class="py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transaction->items as $item)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="text-center py-4">
                            {{ $loop->iteration }}
                        </td>

                        <td class="text-center">
                            @if($item->toolkit->image)
                                <img src="{{ asset('storage/'.$item->toolkit->image) }}"
                                     class="w-12 h-12 object-contain mx-auto rounded">
                            @endif
                        </td>

                        <td class="text-center">
                            {{ $item->toolkit->toolkit_name }}
                        </td>

                        <td class="text-center">
                            {{ $item->serial->serial_number }}
                        </td>

                        <td class="text-center">
                            <form action="{{ route('peminjaman.item.destroy', $item->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus item ini?')"
                                  class="inline">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="bg-red-100 text-red-600 px-3 py-1 rounded-lg text-sm hover:bg-red-200">
                                    Hapus
                                </button>
                            </form>
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


    {{-- ================= SAVE BUTTON ================= --}}
    <div class="flex justify-end pt-6">
        <button type="submit"
                form="updateForm"
                class="bg-[#268397] hover:bg-[#1d6d7c] text-white px-8 py-3 rounded-xl shadow">
            Save Transaksi
        </button>
    </div>

</div>



{{-- ================= MODAL ================= --}}
{{-- ================= MODAL TOOLS ================= --}}
<div id="toolsModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white w-11/12 max-w-3xl rounded-2xl shadow-2xl relative">

        {{-- CLOSE BUTTON --}}
        <button type="button"
                id="closeToolsBtn"
                class="absolute top-4 right-5 text-gray-400 hover:text-gray-600 text-xl">
            ✕
        </button>

        {{-- HEADER --}}
        <div class="px-8 pt-8 pb-4">
            <h3 class="text-lg font-semibold text-gray-700">
                Tools Tersedia
            </h3>
        </div>

        {{-- BODY --}}
        <div class="px-8 pb-6">

            {{-- SEARCH --}}
            <div class="bg-gradient-to-r from-teal-400 to-teal-500 p-3 rounded-xl shadow-md mb-6">
                <input type="text"
                       id="searchToolsModal"
                       placeholder="Cari nama tools atau no seri..."
                       class="w-full bg-white rounded-lg px-4 py-2 outline-none">
            </div>

            {{-- TABLE --}}
            <form action="{{ route('peminjaman.item.add', $transaction->id) }}"
                  method="POST">
                @csrf

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

                        <tbody id="toolsTableBody" class="bg-white divide-y divide-gray-200">

                            @forelse ($serials as $serial)
                            <tr class="hover:bg-gray-50 transition">

                                <td class="py-3 px-4 text-center">
                                    <input type="checkbox"
                                           name="serial_ids[]"
                                           value="{{ $serial->id }}"
                                           class="w-4 h-4 accent-teal-600">
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
                                         class="w-12 h-12 object-contain mx-auto rounded shadow-sm">
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="4"
                                    class="text-center text-gray-400 py-6">
                                    Tidak ada tools tersedia
                                </td>
                            </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- FOOTER --}}
                <div class="flex justify-end gap-4 pt-6">

                    <button type="submit"
                            id="cancelToolsBtn"
                            class="px-5 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                        Batal
                    </button>

                    <button type="submit"
                            class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition shadow">
                        + Tambahkan
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>



{{-- ================= SCRIPT ================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modal = document.getElementById('toolsModal');
    const openBtn = document.getElementById('openToolsBtn');
    const closeBtns = document.querySelectorAll('#closeToolsBtn');
    const searchInput = document.getElementById('searchToolsModal');

    // ================= OPEN MODAL =================
    if (openBtn && modal) {
        openBtn.addEventListener('click', function () {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    }

    // ================= CLOSE MODAL =================
    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        // reset search
        if (searchInput) {
            searchInput.value = '';
            document.querySelectorAll('#toolsTableBody tr').forEach(row => {
                row.style.display = '';
            });
        }
    }

    closeBtns.forEach(btn => {
        btn.addEventListener('click', closeModal);
    });

    // ================= LIVE SEARCH =================
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {

            let keyword = this.value.toLowerCase();

            document.querySelectorAll('#toolsTableBody tr').forEach(function (row) {

                let text = row.innerText.toLowerCase();

                row.style.display = text.includes(keyword) ? '' : 'none';

            });

        });
    }

});
</script>
@endsection