<?php

namespace App\Http\Controllers;

use App\Models\InvTransaction;
use App\Models\InvTransactionItem;
use App\Models\InvSerialNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvTransactionController extends Controller
{
    public function index()
    {
        $transactions = InvTransaction::with([
                'items.toolkit',
                'items.serial'
            ])
            ->orderBy('date', 'desc')
            ->get();

        return view('peminjaman.index', compact('transactions'));
    }

    public function create()
    {
        $serials = InvSerialNumber::with('toolkit')
            ->where('status', 'TERSEDIA')
            ->get();

        return view('peminjaman.create', compact('serials'));
    }

   public function store(Request $request)
{
    $request->validate([
        'borrower_name' => 'required|string',
        'serial_ids'    => 'required|array|min:1'
    ]);

    DB::transaction(function () use ($request) {

        $transaction = InvTransaction::create([
            'id'            => 'TRX-' . Str::random(6),
            'borrower_name' => $request->borrower_name,
            'client_name'   => $request->client_name,
            'project'       => $request->project,
            'purpose'       => $request->purpose,
            'date'          => $request->date ?? now(),
            'is_confirm'    => false
        ]);

        foreach ($request->serial_ids as $serialId) {

            $serial = InvSerialNumber::findOrFail($serialId);

            // ❌ TIDAK ADA CEK STATUS DI SINI
            InvTransactionItem::create([
                'id'             => 'ITEM-' . Str::random(6),
                'transaction_id' => $transaction->id,
                'toolkit_id'     => $serial->toolkit_id,
                'serial_id'      => $serial->id,
                'status'         => 'DIPINJAM'
            ]);
        }
    });

    return redirect()->route('peminjaman.index')
        ->with('success', 'Transaksi berhasil dibuat (draft)');
}

public function edit($id)
{
    $transaction = InvTransaction::with('items.serial', 'items.toolkit')
        ->findOrFail($id);

    // hanya boleh edit jika belum confirm
    if ($transaction->is_confirm) {
        return redirect()->route('peminjaman.index')
            ->with('error', 'Transaksi sudah dikonfirmasi, tidak bisa diedit');
    }

    return view('peminjaman.edit', compact('transaction'));
}

public function update(Request $request, $id)
{
    $transaction = InvTransaction::findOrFail($id);

    if ($transaction->is_confirm) {
        return back()->with('error', 'Transaksi sudah dikonfirmasi');
    }

    $request->validate([
        'borrower_name' => 'required|string',
        'client_name'   => 'nullable|string',
        'project'       => 'nullable|string',
        'purpose'       => 'nullable|string',
        'date'          => 'required|date',
    ]);

    $transaction->update($request->only([
        'borrower_name',
        'client_name',
        'project',
        'purpose',
        'date',
    ]));

    return redirect()->route('peminjaman.index')
        ->with('success', 'Transaksi berhasil diperbarui');
}

public function destroy($id)
{
    $transaction = InvTransaction::with('items')->findOrFail($id);

    if ($transaction->is_confirm) {
        return back()->with('error', 'Transaksi sudah dikonfirmasi, tidak bisa dihapus');
    }

    $transaction->items()->delete();
    $transaction->delete();

    return back()->with('success', 'Transaksi berhasil dihapus');
}

    public function confirm($id)
{
    $transaction = InvTransaction::with('items.serial')
        ->findOrFail($id);

    if ($transaction->is_confirm) {
        return back()->with('error', 'Transaksi sudah dikonfirmasi');
    }

    DB::transaction(function () use ($transaction) {

        foreach ($transaction->items as $item) {

            // ✅ CEK STATUS DI SINI
            if ($item->serial->status !== 'TERSEDIA') {
                throw new \Exception('Serial sudah dipinjam');
            }

            // kunci serial
            $item->serial->update(['status' => 'DIPINJAM']);
        }

        $transaction->update(['is_confirm' => true]);
    });

    return back()->with('success', 'Transaksi berhasil dikonfirmasi');
}


    public function return($id)
    {
        $transaction = InvTransaction::with('items.serial')
            ->findOrFail($id);

        DB::transaction(function () use ($transaction) {

            foreach ($transaction->items as $item) {
                $item->serial->update(['status' => 'TERSEDIA']);
                $item->update(['status' => 'DIKEMBALIKAN']);
            }
        });

        return back()->with('success', 'Tools berhasil dikembalikan');
    }
}
