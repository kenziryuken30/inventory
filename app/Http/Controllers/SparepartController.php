<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SparepartController extends Controller
{
    public function store(Request $request)
    {
        $item = Item::findOrFail($request->item_id);

        SparepartTransaction::create([
            'item_id' => $item->id,
            'qty' => $request->qty
        ]);

        $item->decrement('stok', $request->qty);

        return back();
    }
}
