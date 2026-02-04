<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvToolkit;
use App\Models\InvSerialNumber;
use App\Models\InvToolConditionLog;
use App\Models\InvCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ToolController extends Controller
{
    /**
     * =========================
     * LIST DATA TOOLS
     * =========================
     */
    public function index(Request $request)
    {
        $query = InvSerialNumber::with([
            'toolkit.category',
            'latestCondition'
        ]);

        // 🔍 SEARCH
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('serial_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('toolkit', function ($qt) use ($request) {
                      $qt->where('toolkit_name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // 🎯 FILTER KONDISI
        if ($request->condition) {
            $query->whereHas('latestCondition', function ($q) use ($request) {
                $q->where('condition', $request->condition);
            });
        }

        return view('tools.index', [
            'tools'      => $query->get(),
            'categories' => InvCategory::orderBy('category_name')->get(),
        ]);
    }

    /**
     * =========================
     * SIMPAN TOOL BARU
     * =========================
     */
    public function store(Request $request)
    {
        $request->validate([
            'toolkit_name'  => 'required|string|max:255',
            'category_id'   => 'required',
            'serial_number' => 'required|unique:inv_serial_number,serial_number',
            'image'         => 'nullable|image|max:2048',
        ]);

        // 📸 upload image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('tools', 'public');
        }

        // 🔧 buat toolkit
        $toolkit = InvToolkit::create([
            'id'           => 'TL-' . strtoupper(Str::random(6)),
            'toolkit_name' => $request->toolkit_name,
            'category_id'  => $request->category_id,
            'image'        => $imagePath,
        ]);

        // 🔢 buat serial number
        $serial = InvSerialNumber::create([
            'id'            => 'SN-' . strtoupper(Str::random(8)),
            'toolkit_id'    => $toolkit->id,
            'serial_number' => $request->serial_number,
            'status'        => 'tersedia', // default
        ]);

        // 📝 catat kondisi awal
        InvToolConditionLog::create([
            'serial_id' => $serial->id,
            'condition' => 'baik',
            'notes'     => 'Kondisi awal saat alat ditambahkan',
        ]);

        return redirect()->back()
            ->with('success', 'Barang berhasil ditambahkan');
    }

    /**
     * =========================
     * UPDATE DATA TOOL
     * =========================
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'toolkit_name'  => 'required',
            'category_id'   => 'required',
            'serial_number' => 'required',
            'image'         => 'nullable|image',
        ]);

        $serial = InvSerialNumber::findOrFail($id);

        // update toolkit
        $serial->toolkit->update([
            'toolkit_name' => $request->toolkit_name,
            'category_id'  => $request->category_id,
        ]);

        // update serial
        $serial->update([
            'serial_number' => $request->serial_number,
        ]);

        // upload image baru
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('tools', 'public');
            $serial->toolkit->update(['image' => $path]);
        }

        return redirect()->route('tools.index')
            ->with('success', 'Barang berhasil diperbarui');
    }


    public function destroy($id)
    {
        $serial = InvSerialNumber::findOrFail($id);
        $toolkit = $serial->toolkit;

        if ($serial->status === 'dipinjam') {
            return redirect()->back()
                ->with('error', 'Alat sedang dipinjam, tidak bisa dihapus');
        }

        $serial->delete();

        if ($toolkit && $toolkit->serialNumbers()->count() === 0) {
            if ($toolkit->image) {
                Storage::disk('public')->delete($toolkit->image);
            }
            $toolkit->delete();
        }

        return redirect()->back()
            ->with('success', 'Barang berhasil dihapus');
    }
}
