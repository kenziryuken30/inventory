<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvToolkit;
use App\Models\InvSerialNumber;
use Illuminate\Support\Str;
use App\Models\InvToolConditionLog;
use Illuminate\Support\Facades\Storage;
use App\Models\InvCategory;

class ToolController extends Controller
{
    /**
     * TAMPILKAN DATA TOOLS
     */
   public function index(Request $request)
{
    $query = InvSerialNumber::with([
        'toolkit.category',
        'latestCondition'
    ]);

    if($request->search){
        $query->where(function($q) use ($request) {
            $q->where('serial_number', 'like', '%' . $request->search . '%')
              ->orWhereHas('toolkit', function($qt) use ($request) {
                  $qt->where('toolkit_name', 'like', '%' . $request->search . '%');
              });
        });
    }

    if ($request->condition) {
        $query->whereHas('latestCondition', function ($q) use ($request) {
            $q->where('condition', $request->condition);
        });
    }

    $tools = $query->get();
    $categories = InvCategory::orderBy('category_name')->get();

    return view('tools.index', compact('tools', 'categories'));
}

    /**
     * SIMPAN DATA BARU (DARI MODAL)
     */
        public function store(Request $request)
{
    $request->validate([
        'toolkit_name'  => 'required|string|max:255',
        'category_id'   => 'required',
        'serial_number' => 'required|unique:inv_serial_number,serial_number',
        'image'         => 'nullable|image|max:2048',
    ]);

    // 1️⃣ Upload foto
    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('tools', 'public');
    }

    // 2️⃣ Buat Toolkit
    $toolkit = InvToolkit::create([
        'id'           => 'TL-' . strtoupper(Str::random(6)),
        'toolkit_name' => $request->toolkit_name,
        'category_id'  => $request->category_id,
        'image'        => $imagePath,
        'status'       => 'tersedia',
    ]);

    // 3️⃣ Buat Serial Number (SIMPAN KE VARIABEL)
    $serial = InvSerialNumber::create([
        'id'            => 'SN-' . strtoupper(Str::random(8)),
        'toolkit_id'    => $toolkit->id,
        'serial_number' => $request->serial_number,
        'status'        => 'tersedia',
    ]);

    // 4️⃣ Catat kondisi awal
    InvToolConditionLog::create([
        'serial_id' => $serial->id,
        'condition' => 'baik',
        'note'      => 'Kondisi awal alat saat ditambahkan',
    ]);

    return redirect()->back()->with('success', 'Barang berhasil ditambahkan');
}

    /**
     * UPDATE DATA (EDIT MODAL)
     */
   public function update(Request $request, $id)
{
    $request->validate([
        'toolkit_name' => 'required',
        'category_id'  => 'required',
        'serial_number'=> 'required',
        'image'        => 'nullable|image'
    ]);

    $tool = InvSerialNumber::findOrFail($id);

    // update toolkit
    $tool->toolkit->update([
        'toolkit_name' => $request->toolkit_name,
        'category_id'  => $request->category_id,
    ]);

    // update serial
    $tool->update([
        'serial_number' => $request->serial_number,
    ]);

    // upload image jika ada
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('tools', 'public');
        $tool->update(['image' => $path]);
    }

    return redirect()->route('tools.index')
        ->with('success', 'Barang berhasil diperbarui');
}


   public function destroy($id)
    {
        $serial = InvSerialNumber::findOrFail($id);
        $toolkit = $serial->toolkit;

        // ❗ CEGAH HAPUS JIKA DIPINJAM
        if ($serial->status === 'dipinjam') {
            return redirect()->back()
                ->with('error', 'Alat sedang dipinjam, tidak bisa dihapus');
        }

        // 🗑️ HAPUS GAMBAR TOOLKIT (JIKA ADA)
        if ($toolkit && $toolkit->image) {
            Storage::disk('public')->delete($toolkit->image);
        }

        // 🗑️ HAPUS SERIAL NUMBER
        $serial->delete();

        // 🗑️ HAPUS TOOLKIT JIKA TIDAK ADA SERIAL LAIN
        if ($toolkit && $toolkit->serialNumbers()->count() === 0) {
            $toolkit->delete();
        }

        return redirect()->back()
            ->with('success', 'Barang berhasil dihapus');
    }
}
