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

                if ($serial->status !== 'TERSEDIA') {
                    throw new \Exception('Serial tidak tersedia');
                }

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

    public function confirm($id)
    {
        $transaction = InvTransaction::with('items.serial')
            ->findOrFail($id);

        if ($transaction->is_confirm) {
            return back()->with('error', 'Transaksi sudah dikonfirmasi');
        }

        DB::transaction(function () use ($transaction) {

            $transaction->update(['is_confirm' => true]);

            foreach ($transaction->items as $item) {
                $item->serial->update(['status' => 'DIPINJAM']);
            }
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
