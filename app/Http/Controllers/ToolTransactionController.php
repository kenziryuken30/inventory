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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;



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
                            ->where(function ($q3) use ($search) {

                                $q3->whereHas('toolkit', function ($q4) use ($search) {
                                    $q4->where('toolkit_name', 'like', "%$search%");
                                })

                                ->orWhereHas('serial', function ($q4) use ($search) {
                                    $q4->where('serial_number', 'like', "%$search%");
                                });

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
            ->paginate(10)
            ->withQueryString();

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
        'employee_id' => 'required',
        'date'        => 'required|date|after_or_equal:today',
        'serial_ids'  => 'required|array|min:1',
        'serial_ids.*'=> 'required|exists:inv_serial_number,id|distinct',
    ]);

    try {

        DB::transaction(function () use ($request) {

            // generate kode
            do {
                $letters = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3));
                $numbers = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
                $transactionCode = $letters . $numbers;
            } while (ToolTransaction::where('transaction_code', $transactionCode)->exists());

            $transaction = ToolTransaction::create([
                'transaction_code' => $transactionCode,
                'employee_id'      => $request->employee_id,
                'borrower_name'    => $request->employee_name,
                'client_id'        => $request->client_id,
                'client_name'      => $request->client_name,
                'project_id'       => $request->project_id,
                'project'          => $request->project_name,
                'purpose'          => $request->purpose,
                'date'             => $request->date ?? now(),
                'is_confirm'       => false,
            ]);

            // 🔥 DEBUG PENTING
            if (!$transaction) {
                throw new \Exception('Gagal insert transaksi');
            }

            foreach ($request->serial_ids as $serialId) {

                $serial = InvSerialNumber::where('id', $serialId)
                    ->lockForUpdate()
                    ->firstOrFail();

                ToolTransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'toolkit_id'     => $serial->toolkit_id,
                    'serial_id'      => $serial->id,
                    'status'         => 'PENDING',
                ]);

                $serial->update([
                    'status' => 'DIPINJAM'
                ]);
            }
        });

        return redirect()
            ->route('peminjaman.index')
            ->with('success', 'Transaksi berhasil dibuat');

    } catch (\Exception $e) {

        dd($e->getMessage()); // INI AKAN MUNCULIN ERROR SEBENARNYA
    }
}
    public function edit($id)
    {
        $transaction = ToolTransaction::with('items.serial')
            ->findOrFail($id);

        // ambil serial yang sudah dipilih di transaksi ini
        $selectedSerialIds = $transaction->items->pluck('serial_id')->toArray();

        // ambil serial tersedia TAPI exclude yang sudah dipilih
        $serials = InvSerialNumber::with('toolkit')
            ->where('status', 'TERSEDIA')
            ->whereNotIn('id', $selectedSerialIds)
            ->get();

        return view('peminjaman.edit', compact('transaction', 'serials'));
    }

    public function update(Request $request, $id)
    {
        $transaction = ToolTransaction::with('items')->findOrFail($id);

        // Validasi form basic (SESUAIKAN DENGAN NAME DI BLADE)
        $request->validate([
            'employee_id' => 'required',
            'date'          => 'required|date',
            'client_name'   => 'nullable|string|max:255',
            'project_name'  => 'nullable|string|max:255',
            'purpose'       => 'nullable|string|max:255',
        ]);

        // Cek apakah ada tools
        if ($transaction->items->count() === 0) {
            return redirect()
                ->route('peminjaman.index')
                ->with('error', 'Tidak bisa menyimpan, belum ada tools yang dipilih!');
        }

        DB::transaction(function () use ($request, $transaction) {

            $employeeName = $request->employee_name;


            // Update data utama transaksi
            $transaction->update([
                'employee_id'   => $request->employee_id,
                'borrower_name' => $employeeName,
                'date'          => $request->date,

                'client_id'     => $request->client_id,
                'client_name'   => $request->client_name,

                'project_id'    => $request->project_id,
                'project'       => $request->project_name,

                'purpose'       => $request->purpose,
            ]);
        });

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

                $serial = InvSerialNumber::where('id', $serialId)
                    ->lockForUpdate()
                    ->firstOrFail();

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

            foreach ($transaction->items as $item) {

                if ($item->serial) {

                    // cek apakah masih dipakai transaksi lain
                    $isStillUsed = ToolTransactionItem::where('serial_id', $item->serial_id)
                        ->whereIn('status', ['PENDING', 'DIPINJAM'])
                        ->where('transaction_id', '!=', $transaction->id)
                        ->exists();

                    // hanya ubah kalau tidak dipakai lagi
                    if (!$isStillUsed) {
                        $item->serial->update([
                            'status' => 'TERSEDIA'
                        ]);
                    }
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

            $isStillUsed = ToolTransactionItem::where('serial_id', $item->serial_id)
                ->where('status', 'DIPINJAM')
                ->where('id', '!=', $item->id)
                ->exists();

            if (!$isStillUsed) {
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
                    DB::table('activity_logs')->insert([
                        'user_id' => Auth::id(),
                        'action' => 'Peminjaman_tools',
                        'description' => 'Pinjam: '
                            . optional($item->serial->toolkit)->toolkit_name
                            . ' (' . $item->serial->serial_number . ')',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

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
                    'status'      => 'TERSEDIA',
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

                DB::table('activity_logs')->insert([
                    'user_id' => Auth::id(),
                    'action' => 'Pengembalian_tools',
                    'description' => 'Kembali: '
                        . optional($item->serial->toolkit)->toolkit_name
                        . ' (' . $item->serial->serial_number . ')',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->route('peminjaman.index')
            ->with('success', 'Pengembalian berhasil');
    }

    public function getEmployeesApi()
    {
        $response = Http::get(
            'http://api-checkin.artimu.co.id/employee/list',
            [
                'key' => 'k4v9X8F1kqPz1LpYbG7aNzR6VnZ0TpQm'
            ]
        );

        return response()->json($response->json());
    }

    public function getClientsApi()
    {
        $response = Http::get(
            'http://api-checkin.artimu.co.id/ar_client/list',
            [
                'key' => 'k4v9X8F1kqPz1LpYbG7aNzR6VnZ0TpQm'
            ]
        );

        return response()->json($response->json());
    }

    public function getProjectsApi(Request $request)
    {
        $response = Http::get('http://api-checkin.artimu.co.id/ar_client/projects', [
            'key' => 'k4v9X8F1kqPz1LpYbG7aNzR6VnZ0TpQm',
            'client_id' => $request->client_id
        ]);

        return response()->json($response->json());
    }
}
