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
        'items' => function ($query) {
            $query->whereNull('return_date');
        },
        'items.toolkit',
        'items.serial'
    ])->get();

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
            'is_confirm'    => false // langsung aktif
        ]);

        foreach ($request->serial_ids as $serialId) {

            $serial = InvSerialNumber::where('status', 'TERSEDIA')
                ->where('id', $serialId)
                ->firstOrFail();

            InvTransactionItem::create([
                'id'             => 'ITEM-' . Str::random(6),
                'transaction_id' => $transaction->id,
                'toolkit_id'     => $serial->toolkit_id,
                'serial_id'      => $serial->id,
                'status'         => 'DIPINJAM'
            ]);

            $serial->update([
                'status' => 'DIPINJAM'
            ]);
        }
    });

    return redirect()->route('peminjaman.index')
        ->with('success', 'Transaksi berhasil dibuat');
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

    $serials = InvSerialNumber::with('toolkit')
        ->where('status', 'TERSEDIA')
        ->get();

    return view('peminjaman.edit', compact('transaction', 'serials'));
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


public function addItem(Request $request, $id)
{
    $request->validate([
        'serial_ids' => 'required|array|min:1'
    ]);

    $transaction = InvTransaction::findOrFail($id);

    if ($transaction->is_confirm) {
        return back()->with('error', 'Transaksi sudah dikonfirmasi');
    }

    DB::transaction(function () use ($request, $transaction) {

        foreach ($request->serial_ids as $serialId) {

            $serial = InvSerialNumber::with('toolkit')
                ->where('status', 'TERSEDIA')
                ->findOrFail($serialId);

            // cegah duplikat
            $exists = InvTransactionItem::where('transaction_id', $transaction->id)
                ->where('serial_id', $serialId)
                ->exists();

            if ($exists) continue;

            InvTransactionItem::create([
                'id'             => 'ITEM-' . Str::random(6),
                'transaction_id' => $transaction->id,
                'toolkit_id'     => $serial->toolkit_id,
                'serial_id'      => $serial->id,
                'status'         => 'DIPINJAM',
            ]);
        }
    });

    return redirect()
        ->route('peminjaman.edit', $transaction->id)
        ->with('success', 'Barang berhasil ditambahkan');
}


public function destroy($id)
{
    $transaction = InvTransaction::with('items.serial')->findOrFail($id);

    if ($transaction->is_confirm) {
        return back()->with('error', 'Transaksi sudah dikonfirmasi, tidak bisa dihapus');
    }

    DB::transaction(function () use ($transaction) {

        // kembalikan status serial
        foreach ($transaction->items as $item) {
            $item->serial->update([
                'status' => 'TERSEDIA'
            ]);
        }

        // hapus item
        $transaction->items()->delete();

        // hapus transaksi
        $transaction->delete();
    });

    return back()->with('success', 'Transaksi berhasil dihapus');
}

public function destroyItem($id)
{
    $item = InvTransactionItem::with(['serial', 'transaction'])
        ->findOrFail($id);

    if ($item->transaction->is_confirm) {
        return back()->with('error', 'Transaksi sudah dikonfirmasi');
    }

    DB::transaction(function () use ($item) {
        $item->serial->update([
            'status' => 'TERSEDIA'
        ]);

        $item->delete();
    });

    return redirect()
        ->route('peminjaman.edit', $item->transaction->id)
        ->with('success', 'Barang berhasil dihapus!');
}

public function confirm($id)
{
    $transaction = InvTransaction::with('items.serial')->findOrFail($id);

    DB::transaction(function () use ($transaction) {

        $transaction->update([
            'is_confirm' => true
        ]);

        foreach ($transaction->items as $item) {
            $item->update([
                'status' => 'DIPINJAM'
            ]);

            $item->serial->update([
                'status' => 'DIPINJAM'
            ]);
        }
    });

    return back()->with('success', 'Transaksi berhasil dikonfirmasi');
}
public function returnProcess(Request $request, $id)
{
    $transaction = InvTransaction::with('items.serial')->findOrFail($id);

    if (empty($request->items)) {
        return back()->with('error', 'Pilih alat yang dikembalikan');
    }

    DB::transaction(function () use ($request, $transaction) {

        foreach ($request->items as $itemId => $data) {

            $item = InvTransactionItem::where('id', $itemId)
                ->where('transaction_id', $transaction->id)
                ->first();

            if (! $item) continue;

            // 1️⃣ Update item (TANPA condition)
            $item->update([
                'return_date' => now(),
                'status'      => 'TERSEDIA',
            ]);

            // 2️⃣ Simpan ke tabel log kondisi
            DB::table('inv_tool_condition_logs')->insert([
                'serial_id'  => $item->serial_id,
                'condition'  => strtolower($data['condition'] ?? 'baik'),
                'note'       => $data['note'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3️⃣ Update status serial
            $statusSerial = strtolower($data['condition']) === 'rusak'
                ? 'MAINTENANCE'
                : 'TERSEDIA';

            $item->serial->update([
                'status' => $statusSerial
            ]);
        }
    });

    return back()->with('success', 'Pengembalian berhasil');
}



}
