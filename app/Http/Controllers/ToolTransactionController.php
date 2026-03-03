<?php

namespace App\Http\Controllers;

use App\Models\ToolTransaction;
use App\Models\ToolTransactionItem;
use App\Models\InvSerialNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\InvToolConditionLog;


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

    return redirect()
    ->route('peminjaman.index')
    ->with('success', 'Transaksi berhasil dibuat');
}

public function edit($id)
{
    $transaction = ToolTransaction::with('items.serial')
        ->findOrFail($id);

    // Hanya tampilkan serial yang masih tersedia
    $serials = InvSerialNumber::with('toolkit')
        ->where('status', 'TERSEDIA')
        ->get();

    return view('peminjaman.edit', compact('transaction','serials'));
}

public function update(Request $request, $id)
{
    $transaction = ToolTransaction::with('items')->findOrFail($id);

    // Validasi form basic
    $request->validate([
        'borrower_name' => 'required|string|max:255',
        'date' => 'required|date',
        'client_name' => 'nullable|string|max:255',
        'project' => 'nullable|string|max:255',
        'purpose' => 'nullable|string|max:255',
    ]);

    // Cek apakah ada tools
    if ($transaction->items->count() === 0) {
        return back()->with('error', 'Belum pilih tools!');
    }

    // Update data utama transaksi
    $transaction->update([
        'borrower_name' => $request->borrower_name,
        'date' => $request->date,
        'client_name' => $request->client_name,
        'project' => $request->project,
        'purpose' => $request->purpose,
    ]);

return redirect('/peminjaman')
    ->with('success', 'Transaksi berhasil diupdate');
}

public function addItem(Request $request, $id)
{
    $request->validate([
        'serial_ids' => 'required|array|min:1',
        'serial_ids.*' => 'exists:inv_serial_number,id'
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

            // Cegah duplikat
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

            // 🔥 Update status serial jadi dipinjam
            $serial->update([
                'status' => 'DIPINJAM'
            ]);
        }
    });

    return back()->with('success', 'Tools berhasil ditambahkan');
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
    $item = ToolTransactionItem::find($id);

    if (!$item) {
        return back()->with('error', 'Item tidak ditemukan');
    }

    $transactionId = $item->transaction_id;

    DB::transaction(function () use ($item) {

        if ($item->serial) {
            $item->serial->update([
                'status' => 'TERSEDIA'
            ]);
        }

        $item->delete();
    });

    return redirect()
        ->route('peminjaman.edit', $transactionId)
        ->with('success', 'Item berhasil dihapus!');
}

public function confirm($id)
{
    $transaction = ToolTransaction::with('items.serial')
        ->findOrFail($id);

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

    return redirect()
        ->route('peminjaman.index')
        ->with('success', 'Transaksi berhasil dikonfirmasi');
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

            // Normalisasi kondisi dulu (HARUS DI ATAS)
            $condition = strtolower(trim($data['condition'] ?? 'baik'));

            // Update transaction item
            $item->update([
                'return_date' => $request->return_date,
                'status'      => 'Tersedia',
                'return_condition' => $condition,
                'return_note' => $data['note'] ?? null,
            ]);

            // Simpan log kondisi (cukup sekali saja)
            InvToolConditionLog::create([
                'serial_id' => $item->serial_id,
                'condition' => $condition,
                'note'      => $data['note'] ?? null,
            ]);

            // Mapping kondisi ke status serial
            if ($condition === 'baik') {
                $statusSerial = 'TERSEDIA';
            } elseif (in_array($condition, ['maintenance', 'rusak'])) {
                $statusSerial = 'TIDAK_TERSEDIA';
            } else {
                $statusSerial = 'TERSEDIA';
            }

            // Update status serial
            $item->serial->update([
                'status' => $statusSerial
            ]);
        }
    });

    return redirect()->route('peminjaman.index')
    ->with('success', 'Pengembalian berhasil');
}

}
