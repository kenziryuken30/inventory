<?php

namespace App\Http\Controllers;

use App\Models\ToolTransaction;
use App\Models\ToolTransactionItem;
use App\Models\InvSerialNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\InvToolConditionLog;
use App\Models\InvEmployee;


class ToolTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = ToolTransaction::with([
            'items' => function ($q) {
                $q->whereNull('return_date');
            },
            'items.toolkit',
            'items.serial'
        ])
            ->whereHas('items', function ($q) {
                $q->whereNull('return_date');
            });

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {

                // 🔍 Cari nama peminjam
                $q->whereHas('employee', function ($q2) use ($search) {
                    $q2->where('full_name', 'like', "%$search%");
                })

                    // 🔍 Cari nama tools (YANG BELUM DIKEMBALIKAN)
                    ->orWhereHas('items', function ($q2) use ($search) {
                        $q2->whereNull('return_date')
                            ->whereHas('toolkit', function ($q3) use ($search) {
                                $q3->where('toolkit_name', 'like', "%$search%");
                            });
                    })

                    // 🔍 Cari serial number (YANG BELUM DIKEMBALIKAN)
                    ->orWhereHas('items', function ($q2) use ($search) {
                        $q2->whereNull('return_date')
                            ->whereHas('serial', function ($q3) use ($search) {
                                $q3->where('serial_number', 'like', "%$search%");
                            });
                    });
            });
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

            $employees = InvEmployee::all();

        return view('peminjaman.create', compact('serials', 'employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:inv_employee,id',
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
            $employee = InvEmployee::findOrFail($request->employee_id);

            $transaction = ToolTransaction::create([
                'transaction_code' => $transactionCode,
                'employee_id'      => $request->employee_id,
                'borrower_name'    => $employee->full_name,
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
                    'status'         => 'PENDING',
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

        return view('peminjaman.edit', compact('transaction', 'serials'));
    }

        public function update(Request $request, $id)
    {
        $transaction = ToolTransaction::with('items')->findOrFail($id);

        // Validasi form basic (SESUAIKAN DENGAN NAME DI BLADE)
        $request->validate([
            'borrower_name' => 'required|string|max:255',
            'date'          => 'required|date',
            'client_name'   => 'nullable|string|max:255',
            'project'       => 'nullable|string|max:255',
            'purpose'       => 'nullable|string|max:255',
        ]);

        // Cek apakah ada tools
        if ($transaction->items->count() === 0) {
            return redirect()
                ->route('peminjaman.index')
                ->with('error', 'Tidak bisa menyimpan, belum ada tools yang dipilih!');
        }

        // Update data utama transaksi
        $transaction->update([
            'borrower_name' => $request->borrower_name,
            'date'          => $request->date,
            'client_name'   => $request->client_name,
            'project'       => $request->project,
            'purpose'       => $request->purpose,
        ]);

        return redirect()
            ->route('peminjaman.index')
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
                    'status'         => 'PENDING',
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

            foreach ($transaction->items as $item) {
                if ($item->serial) {
                    $item->serial->update([
                        'status' => 'TERSEDIA'
                    ]);
                }
            }

            $transaction->items()->delete();
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
    try {
        $transaction = ToolTransaction::with('items.serial')
            ->findOrFail($id);

        DB::transaction(function () use ($transaction) {

            foreach ($transaction->items as $item) {

                $isUsed = ToolTransactionItem::where('serial_id', $item->serial_id)
                    ->where('status', 'DIPINJAM')
                    ->where('transaction_id', '!=', $transaction->id)
                    ->exists();

                if ($isUsed) {
                    throw new \Exception('Serial ' . $item->serial->serial_number . ' sedang dipinjam!');
                }
            }

            $transaction->update([
                'is_confirm' => true
            ]);

            foreach ($transaction->items as $item) {
                $item->update(['status' => 'DIPINJAM']);
                $item->serial->update(['status' => 'DIPINJAM']);
            }
        });

        return back()->with('success', 'Transaksi berhasil dikonfirmasi');

    } catch (\Exception $e) {

        return back()->with('error', $e->getMessage());
    }
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
