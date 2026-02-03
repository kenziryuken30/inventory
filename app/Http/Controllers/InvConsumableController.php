<?php

namespace App\Http\Controllers;
use App\Models\InvConsumable;
use App\Models\InvCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class InvConsumableController extends Controller
{
    public function index(Request $request)
    {
        $consumables = InvConsumable::with('category')
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%');
            })
            ->latest()
            ->get();

        $categories = InvCategory::all();

        return view('dataconsumable.index', compact('consumables','categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'stock' => 'required|integer',
            'minimum_stock' => 'required|integer',
            'unit' => 'required',
            'image' => 'nullable|image',
        ]);

        $data['id'] = 'CN-' . Str::upper(Str::random(5));

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('consumables','public');
        }

        InvConsumable::create($data);

        return back();
    }

    public function update(Request $request, $id)
    {
        $item = InvConsumable::findOrFail($id);

        $data = $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'stock' => 'required|integer',
            'minimum_stock' => 'required|integer',
            'unit' => 'required',
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $request->file('image')->store('consumables','public');
        }

        $item->update($data);

        return back();
    }

    public function destroy($id)
    {
        $item = InvConsumable::findOrFail($id);

        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        return back();
    }
}
