<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\InvConsumable;
use App\Models\InvConsumableReturn;
use App\Models\InvConsumableReturnItem;
use App\Models\InvConsumableTransaction;

use Illuminate\Http\Request;


class ConsumableReturnController extends Controller
{

    public function index($transaction_id)
    {
        $transaction = InvConsumableTransaction::with('items.consumable')
            ->findOrFail($transaction_id);

        return view('dataconsumable.consumable.return', compact('transaction'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required',
            'date' => 'required|date',
            'items.*.qty' => 'required|integer|min:1'
        ]);

        DB::transaction(function () use ($request) {

            $return = InvConsumableReturn::create([
                'id' => 'RTN-CN-' . time(),
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'note' => $request->note,
            ]);

            foreach ($request->items as $item) {

                InvConsumableReturnItem::create([
                    'return_id' => $return->id,
                    'consumable_id' => $item['id'],
                    'qty' => $item['qty'],
                ]);

                // stok MASUK
                InvConsumable::where('id', $item['id'])
                    ->increment('stock', $item['qty']);
            }
        });

        return redirect()->back()->with('success','Return berhasil');
    }
}
