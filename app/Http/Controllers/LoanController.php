<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function store(Request $request)
    {
        $loan = Loan::create([
            'peminjam' => $request->peminjam,
            'tanggal_pinjam' => now(),
            'status' => 'dipinjam'
        ]);

        foreach ($request->item_ids as $itemId) {
            LoanDetail::create([
                'loan_id' => $loan->id,
                'item_id' => $itemId
            ]);

            Item::where('id', $itemId)
                ->update(['status' => 'dipinjam']);
        }

        return back();
    }
}
