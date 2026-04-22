<?php

namespace App\Http\Controllers;

use App\Models\InvConsumable;
use App\Models\InvCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class InvConsumableController extends Controller
{
    public function index(Request $request)
    {
        $consumables = InvConsumable::with('category')
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->get();

        $categories = InvCategory::all();

        return view('consumable.index', compact('consumables', 'categories'));
    }

    // ★ TAMBAH METHOD INI ★
    public function checkName(Request $request)
    {
        $name = $request->query('name');
        $id = $request->query('id');

        if (!$name) {
            return response()->json(['exists' => false]);
        }

        $query = InvConsumable::whereRaw('LOWER(name) = ?', [strtolower(trim($name))]);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        return response()->json(['exists' => $query->exists()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|unique:inv_consumables,name',
            'category_id' => 'required',
            'stock'       => 'required|integer',
            'minimum_stock' => 'nullable|integer',
            'unit'        => 'required',
            'image'       => 'nullable|image',
        ]);

        $data['id'] = Str::uuid();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('consumables', 'public');
        }

        InvConsumable::create($data);

        DB::table('activity_logs')->insert([
            'user_id' => Auth::id(),
            'action' => 'create_consumable',
            'description' => 'Tambah consumable: ' . $data['name'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Consumable berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $item = InvConsumable::findOrFail($id);

        $data = $request->validate([
            'name'          => 'required|unique:inv_consumables,name,' . $id,
            'category_id'   => 'required',
            'stock'         => 'required|integer',
            'minimum_stock' => 'required|integer',
            'unit'          => 'required',
            'image'         => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $request->file('image')->store('consumables', 'public');
        }

        $item->update($data);

        DB::table('activity_logs')->insert([
            'user_id' => Auth::id(),
            'action' => 'update_consumable',
            'description' => 'Update consumable: ' . $data['name'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Consumable berhasil diupdate');
    }

    public function destroy($id)
    {
        $item = InvConsumable::findOrFail($id);

        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        return back()->with('success', 'Anda telah menghapus barang');
    }

    public function restock(Request $request, $id)
    {
        $request->validate([
            'qty' => 'required|integer|min:1'
        ]);

        $item = InvConsumable::findOrFail($id);
        $item->stock += $request->qty;
        $item->save();

        DB::table('activity_logs')->insert([
            'user_id' => Auth::id(),
            'action' => 'add_stock',
            'description' => 'Tambah stok: ' . $item->name . ' (+' . $request->qty . ')',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Stok berhasil ditambah');
    }
}
