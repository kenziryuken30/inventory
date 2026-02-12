<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\InvConsumable;
use App\Models\InvConsumableTransaction;
use App\Models\InvConsumableTransactionItem;
use App\Models\InvConsumableReturn;
use App\Models\InvConsumableReturnItem;
use App\Models\InvEmployee;

class ConsumableTransactionController extends Controller
{
    /*
    ======================
    LIST TRANSAKSI
    ======================
    */
    public function index()
    {
        $transactions = InvConsumableTransaction::with([
            'items.consumable'
        ])
        ->orderBy('date', 'desc')
        ->get();

        return view('transaksi.index', compact('transactions'));
    }


    /*
    ======================
    FORM TRANSAKSI
    ======================
    */
    public function create()
    {
        session()->forget('selected_consumables');

        return view('transaksi.create', [
            'consumables' => InvConsumable::all(),
            'selectedConsumables' => [],
        ]);
    }

    

    /*
    ======================
    TAMBAH SESSION ITEM
    ======================
    */
    public function tambahPilihan(Request $request)
    {
        $items = $request->items ?? [];
        $selected = session()->get('selected_consumables', []);

        foreach ($items as $item) {

            if (($item['qty'] ?? 0) > 0) {

                $c = InvConsumable::find($item['id']);
                if (!$c) continue;

                $selected[] = [
                    'id' => $c->id,
                    'name' => $c->name,
                    'unit' => $c->unit,
                    'qty' => (int)$item['qty'],
                ];
            }
        }

        session()->put('selected_consumables', $selected);

        return redirect()->route('transaksi.create');
    }

    /*
    ======================
    HAPUS SESSION ITEM
    ======================
    */
    public function hapusPilihan($index)
    {
        $items = session('selected_consumables', []);
        unset($items[$index]);

        session([
            'selected_consumables' => array_values($items)
        ]);

        return back();
    }

    /*
    ======================
    STORE TRANSAKSI
    ======================
    */
    
public function store(Request $request)
{
    $request->validate([
        'borrower_name' => 'required',
        'date' => 'required|date',
        'items' => 'required|array'
    ]);

    DB::transaction(function () use ($request) {

        $trx = InvConsumableTransaction::create([
            'id' => Str::uuid(),
            'borrower_name' => $request->borrower_name,
            'client' => $request->client,
            'project' => $request->project,
            'purpose' => $request->purpose,
            'date' => $request->date,
            'is_confirm' => false,
        ]);

        foreach ($request->items as $item) {

            InvConsumableTransactionItem::create([
                'id' => Str::uuid(),
                'transaction_id' => $trx->id,
                'consumable_id' => $item['consumable_id'],
                'qty' => $item['qty'],
            ]);

            InvConsumable::where('id', $item['consumable_id'])
                ->decrement('stock', $item['qty']);
        }
    });

    return redirect()
        ->route('transaksi.index')
        ->with('success', 'Transaksi consumable berhasil');
}

    /*
    ======================
    FORM RETURN
    ======================
    */
    public function returnForm()
    {
        $consumables = InvConsumable::all();

        return view('transaksi.return', compact('consumables'));
    }

    /*
    ======================
    STORE RETURN
    ======================
    */
    public function returnStore(Request $request)
    {
        DB::transaction(function () use ($request) {

            $return = InvConsumableReturn::create([
                'transaction_id' => $request->transaction_id,
                'date' => $request->date,
                'note' => $request->note,
            ]);

            foreach ($request->items as $item) {

                if (($item['qty'] ?? 0) > 0) {

                    InvConsumableReturnItem::create([
                        'return_id' => $return->id,
                        'consumable_id' => $item['id'],
                        'qty' => $item['qty'],
                    ]);

                    InvConsumable::where('id', $item['id'])
                        ->increment('stock', $item['qty']);
                }
            }
        });

        return back()->with('success', 'Return berhasil');
    }

    public function edit($id)
    {
        $transaction = InvConsumableTransaction::with('items.consumable')
            ->findOrFail($id);

        return view('transaksi.edit', compact('transaction'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'borrower_name' => 'required',
            'date' => 'required|date',
        ]);

        $trx = InvConsumableTransaction::findOrFail($id);

        $trx->update([
            'borrower_name' => $request->borrower_name,
            'client' => $request->client,
            'project' => $request->project,
            'purpose' => $request->purpose,
            'date' => $request->date,
        ]);

        return back()->with('success', 'Transaksi berhasil diupdate');
    }

    public function updateItem(Request $request, $itemId)
    {
        $request->validate([
            'qty' => 'required|integer|min:1'
        ]);

        DB::transaction(function () use ($request, $itemId) {

            $item = InvConsumableTransactionItem::with('consumable')
                ->findOrFail($itemId);

            $oldQty = $item->qty;
            $newQty = $request->qty;
            $diff = $newQty - $oldQty;

            // cek stok kalau nambah
            if ($diff > 0 && $item->consumable->stock < $diff) {
                abort(400, 'Stok tidak mencukupi');
            }

            // rollback stok
            InvConsumable::where('id', $item->consumable_id)
                ->decrement('stock', $diff);

            $item->update([
                'qty' => $newQty
            ]);
        });

        return back()->with('success', 'Qty berhasil diupdate');
    }

    public function destroyItem($itemId)
    {
        DB::transaction(function () use ($itemId) {

            $item = InvConsumableTransactionItem::with('consumable')
                ->findOrFail($itemId);

            InvConsumable::where('id', $item->consumable_id)
                ->increment('stock', $item->qty);

            $item->delete();
        });

        return back()->with('success', 'Item berhasil dihapus');
    }

}
