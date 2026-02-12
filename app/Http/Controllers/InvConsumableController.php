<?php

namespace App\Http\Controllers;
use App\Models\InvConsumable;
use App\Models\InvCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\InvConsumableTransaction;
use App\Models\InvConsumableTransactionItem;

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

        return view('consumable.index', compact('consumables', 'categories'));
    }

   public function store(Request $request)
{
    $request->validate([
        'borrower_name' => 'required',
        'date' => 'required|date',
    ]);

    $items = session('selected_consumables', []);

    if (count($items) === 0) {
        return back()->with('error', 'Consumable belum dipilih');
    }

    DB::transaction(function () use ($request, $items) {

        $trx = InvConsumableTransaction::create([
            'id' => Str::uuid(),
            'borrower_name' => $request->borrower_name,
            'client' => $request->client,
            'project' => $request->project,
            'purpose' => $request->purpose,
            'date' => $request->date,
            'is_confirm' => false,
        ]);

        foreach ($items as $item) {

            InvConsumableTransactionItem::create([
                'id' => Str::uuid(),
                'transaction_id' => $trx->id,
                'consumable_id' => $item['id'],
                'qty' => $item['qty'],
            ]);

            InvConsumable::where('id', $item['id'])
                ->decrement('stock', $item['qty']);
        }
    });

    // 🔥 PENTING
    session()->forget('selected_consumables');

    return redirect()
        ->route('transaksi.index')
        ->with('success', 'Transaksi consumable berhasil');
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
