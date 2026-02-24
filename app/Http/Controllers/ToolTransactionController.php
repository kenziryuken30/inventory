<?php

namespace App\Http\Controllers;

use App\Models\ToolTransaction;
use App\Models\ToolTransactionItem;
use App\Models\InvSerialNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class ToolTransactionController extends Controller
{
public function index(Request $request)
{
    $query = ToolTransaction::with(['items.toolkit', 'items.serial'])
        ->whereHas('items', function ($q) {
            $q->whereNull('return_date'); 
        });

    if ($request->filled('search')) {
        $query->where('borrower_name', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('start_date')) {
        $query->whereDate('date', '>=', $request->start_date);
    }

    if ($request->filled('end_date')) {
        $query->whereDate('date', '<=', $request->end_date);
    }

    $transactions = $query
        ->latest()
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

    // Generate kode
    do {
        $letters = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3));
        $numbers = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        $transactionCode = $letters . $numbers;
    } while (ToolTransaction::where('transaction_code', $transactionCode)->exists());

    // Simpan transaksi
    $transaction = ToolTransaction::create([
        'transaction_code' => $transactionCode,
        'borrower_name'    => $request->borrower_name,
        'client_name'      => $request->client_name,
        'project'          => $request->project,
        'purpose'          => $request->purpose,
        'date'             => $request->date ?? now(),
        'is_confirm'       => false,
    ]);

    // Pastikan ID ada
    if (!$transaction->id) {
        throw new \Exception('Transaction ID kosong');
    }

    foreach ($request->serial_ids as $serialId) {

        $serial = InvSerialNumber::where('status', 'TERSEDIA')
            ->where('id', $serialId)
            ->firstOrFail();

        ToolTransactionItem::create([
            'transaction_id' => $transaction->id,
            'toolkit_id'     => $serial->toolkit_id,
            'serial_id'      => $serial->id,
            'status'         => 'Dipinjam',
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
    $transaction = ToolTransaction::with('items.serial', 'items.toolkit')
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
    $transaction = ToolTransaction::findOrFail($id);

    if ($transaction->is_confirm) {
        return back()->with('error', 'Transaksi sudah dikonfirmasi');
    }

    if ($transaction->items()->count() === 0) {
        return back()->with('error', 'Belum ada barang yang ditambahkan');
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

    $transaction = ToolTransaction::findOrFail($id);

    if ($transaction->is_confirm) {
        return back()->with('error', 'Transaksi sudah dikonfirmasi');
    }

    DB::transaction(function () use ($request, $transaction) {

        foreach ($request->serial_ids as $serialId) {

            $serial = InvSerialNumber::with('toolkit')
                ->where('status', 'TERSEDIA')
                ->findOrFail($serialId);

            // cegah duplikat
            $exists = ToolTransactionItem::where('transaction_id', $transaction->id)
                ->where('serial_id', $serialId)
                ->exists();

            if ($exists) continue;

            ToolTransactionItem::create([
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
    $transaction = ToolTransaction::with('items.serial')->findOrFail($id);

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
    $item = ToolTransactionItem::with(['serial', 'transaction'])
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
    $transaction = ToolTransaction::with('items.serial')->findOrFail($id);

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
    $transaction = ToolTransaction::with('items.serial')->findOrFail($id);

    $request->validate([
        'return_date' => 'required|date',
    ]);

    if (empty($request->items)) {
        return back()->with('error', 'Pilih alat yang dikembalikan');
    }

    DB::transaction(function () use ($request, $transaction) {

        foreach ($request->items as $itemId => $data) {

            $item = ToolTransactionItem::where('id', $itemId)
                ->where('transaction_id', $transaction->id)
                ->first();

            if (! $item) continue;

            // Update transaction item
            $item->update([
                'return_date' => $request->return_date,
                'status'      => 'TERSEDIA',
            ]);

            $condition = strtolower(trim($data['condition'] ?? 'baik'));

            // Simpan log kondisi
            DB::table('inv_tool_condition_logs')->insert([
                'serial_id'  => $item->serial_id,
                'condition'  => $condition,
                'note'       => $data['note'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Mapping kondisi ke status serial
            if ($condition === 'baik') {
                $statusSerial = 'TERSEDIA';
            } elseif ($condition === 'maintenance') {
                $statusSerial = 'TIDAK_TERSEDIA';
            } elseif ($condition === 'rusak') {
                $statusSerial = 'TIDAK_TERSEDIA';
            } else {
                $statusSerial = 'TERSEDIA';
            }


            $item->serial->update([
                'status' => $statusSerial
            ]);
        }
    });

    return back()->with('success', 'Pengembalian berhasil');
}

}
