<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\InvConsumable;
use App\Models\InvConsumableTransaction;
use App\Models\InvConsumableTransactionItem;



class ConsumableTransactionController extends Controller
{
    public function index()
    {
        $transactions = InvConsumableTransaction::with('items.consumable')
            ->latest()
            ->get();

        return view('transaksi.index', compact('transactions'));
    }

    public function create()
    {
        return view('transaksi.create', [
            'consumables' => InvConsumable::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'borrower_name' => 'required',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.consumable_id' => 'required',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {

            $trx = InvConsumableTransaction::create([
                'borrower_name' => $request->borrower_name,
                'client' => $request->client,
                'project' => $request->project,
                'purpose' => $request->purpose,
                'date' => $request->date,
                'is_confirm' => false,
            ]);

            foreach ($request->items as $item) {

                $consumable = InvConsumable::findOrFail($item['consumable_id']);

                if ($item['qty'] > $consumable->stock) {
                    throw new \Exception(
                        "Stock {$consumable->name} hanya tersedia {$consumable->stock}"
                    );
                }

                InvConsumableTransactionItem::create([
                    'transaction_id' => $trx->id,
                    'consumable_id' => $item['consumable_id'],
                    'qty' => $item['qty'],
                ]);

                $consumable->decrement('stock', $item['qty']);
            }
        });

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil');
    }

    public function returnFull($id)
{
    $trx = InvConsumableTransaction::with('items')->findOrFail($id);

    if ($trx->is_confirm) {
        return back()->with('error', 'Barang sudah dikembalikan sebelumnya');
    }

    DB::transaction(function () use ($trx) {
        foreach ($trx->items as $item) {
            InvConsumable::where('id', $item->consumable_id)
                ->increment('stock', $item->qty);
        }

        $trx->update([
            'is_confirm' => true
        ]);
    });

    return back()->with('success', 'Return berhasil');
}

    public function edit($id)
    {
        $transaction = InvConsumableTransaction::with('items.consumable')
            ->findOrFail($id);

        $selected = $transaction->items->pluck('consumable_id');

        $consumables = InvConsumable::whereNotIn('id', $selected)->get();

        return view('transaksi.edit', compact('transaction', 'consumables'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'borrower_name' => 'required',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request, $id) {

            $trx = InvConsumableTransaction::with('items')
                ->findOrFail($id);

            foreach ($trx->items as $item) {
                InvConsumable::where('id', $item->consumable_id)
                    ->increment('stock', $item->qty);
            }

            $trx->items()->delete();

            $trx->update([
                'borrower_name' => $request->borrower_name,
                'client' => $request->client,
                'project' => $request->project,
                'purpose' => $request->purpose,
                'date' => $request->date,
            ]);

            foreach ($request->items as $item) {

                $consumable = InvConsumable::findOrFail($item['consumable_id']);

                if ($item['qty'] > $consumable->stock) {
                    throw new \Exception(
                        "Stock {$consumable->name} tidak cukup"
                    );
                }

                InvConsumableTransactionItem::create([
                    'transaction_id' => $trx->id,
                    'consumable_id' => $item['consumable_id'],
                    'qty' => $item['qty'],
                ]);

                $consumable->decrement('stock', $item['qty']);
            }
        });

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil diupdate');
    }


    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            $trx = InvConsumableTransaction::with('items')
                ->findOrFail($id);

            foreach ($trx->items as $item) {
                InvConsumable::where('id', $item->consumable_id)
                    ->increment('stock', $item->qty);
            }

            $trx->items()->delete();
            $trx->delete();
        });

        return back()->with('success', 'Transaksi dihapus');
    }

    public function returnItem(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {

            $item = InvConsumableTransactionItem::findOrFail($id);

            $request->validate([
                'qty_return' => 'required|integer|min:1'
            ]);

            $sisa = $item->qty - $item->qty_return;

            if ($request->qty_return > $sisa) {
                throw new \Exception('Qty melebihi sisa');
            }

            $item->increment('qty_return', $request->qty_return);

            InvConsumable::where('id', $item->consumable_id)
                ->increment('stock', $request->qty_return);

            $trx = $item->transaction;
            $allReturned = $trx->items->every(function ($i) {
                return $i->qty == $i->qty_return;
            });

            if ($allReturned) {
                $trx->update(['is_confirm' => true]);
            }
        });

        return back()->with('success', 'Return berhasil');
    }

    public function returnProcess(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {

            $trx = InvConsumableTransaction::with('items')
                ->findOrFail($id);

            foreach ($request->items as $itemId => $data) {

                $item = InvConsumableTransactionItem::findOrFail($itemId);

                $qtyReturn = $data['qty_return'] ?? 0;

                if ($qtyReturn > 0) {

                    $sisa = $item->qty - $item->qty_return;

                    if ($qtyReturn > $sisa) {
                        throw new \Exception("Qty melebihi sisa");
                    }

                    // update return
                    $item->increment('qty_return', $qtyReturn);

                    // update stock
                    InvConsumable::where('id', $item->consumable_id)
                        ->increment('stock', $qtyReturn);
                }
            }

            // cek apakah semua sudah kembali
            $allReturned = $trx->items->every(function ($i) {
                return $i->qty == $i->qty_return;
            });

            if ($allReturned) {
                $trx->update(['is_confirm' => true]);
            }
        });

        return redirect()->route('transaksi.index')
            ->with('success', 'Return berhasil');
    }

    public function updateItem(Request $request, $id)
    {
        $item = InvConsumableTransactionItem::findOrFail($id);

        $request->validate([
            'qty' => 'required|integer|min:1'
        ]);

        $item->update([
            'qty' => $request->qty
        ]);

        return back()->with('success', 'Qty berhasil diupdate');
    }

    public function destroyItem($id)
    {
        $item = InvConsumableTransactionItem::findOrFail($id);

        $item->delete();

        return back()->with('success', 'Item berhasil dihapus');
    }

    public function confirm($id)
    {
        $trx = InvConsumableTransaction::findOrFail($id);

        // Update status confirm
        $trx->is_confirm = true;
        $trx->save();

        return redirect()
            ->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dikonfirmasi');
    }

    public function kembali(Request $request, $id)
    {
        $trx = InvConsumableTransaction::with('items')->findOrFail($id);

        foreach ($request->items as $itemId => $data) {

            if (!empty($data['qty']) && $data['qty'] > 0) {

                $item = InvConsumableTransactionItem::find($itemId);

                $sisa = $item->qty - $item->qty_return;

                if ($data['qty'] > $sisa) {
                    throw new \Exception("Qty melebihi sisa");
                }

                $item->increment('qty_return', $data['qty']);

                $consumable = $item->consumable;
                $consumable->increment('stock', $data['qty']);
            }
        }

        $allReturned = $trx->items->every(function ($i) {
            return $i->qty == $i->qty_return;
        });

        if ($allReturned) {
            $trx->update([
                'is_return' => true,
                'return_date' => $request->return_date
            ]);
        }

        return redirect()
            ->route('transaksi.index')
            ->with('success', 'Barang berhasil dikembalikan');
    }
    
    public function storeItem(Request $request, $id)
    {
        $request->validate([
            'consumable_id' => 'required',
            'qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $id) {

            $consumable = InvConsumable::findOrFail($request->consumable_id);

            if ($request->qty > $consumable->stock) {
                throw new \Exception("Stock tidak cukup");
            }

            InvConsumableTransactionItem::create([
                'transaction_id' => $id,
                'consumable_id' => $request->consumable_id,
                'qty' => $request->qty,
            ]);

            $consumable->decrement('stock', $request->qty);
        });

        return back()->with('success', 'Consumable berhasil ditambahkan');
    }
}
