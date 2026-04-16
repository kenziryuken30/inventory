<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\InvConsumable;
use App\Models\InvConsumableTransaction;
use App\Models\InvConsumableTransactionItem;
use App\Models\InvEmployee;
use Illuminate\Support\Facades\Http;

class ConsumableTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = InvConsumableTransaction::with('items.consumable');

        if ($request->search) {
            $query->where('borrower_name', 'like', '%' . $request->search . '%');
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $transactions = $query
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(10);

        return view('transaksi.index', compact('transactions'));
    }

    public function create()
    {
        return view('transaksi.create', [
            'consumables' => InvConsumable::all(),
        ]);
    }

    public function store(Request $request)
    {
        // ✅ FIX: employee_id nullable, borrower_name wajib ada
        $request->validate([
            'employee_id' => 'nullable|string|max:255',
            'borrower_name' => 'required|string|max:255',
            'date' => 'required|date',
            'client_id' => 'nullable|string|max:255',
            'client' => 'nullable|string|max:255',
            'project_id' => 'nullable|string|max:255',
            'project' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.consumable_id' => 'required|integer|exists:inv_consumables,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {

            // Generate unique transaction code
            do {
                $letters = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3));
                $numbers = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
                $transactionCode = $letters . $numbers;
            } while (InvConsumableTransaction::where('transaction_code', $transactionCode)->exists());

            // ✅ FIX: Simpan employee_id sebagai string (bisa external ID)
            $trx = InvConsumableTransaction::create([
                'transaction_code' => $transactionCode,
                'employee_id' => $request->employee_id ?: null, // Bisa NULL atau string external ID
                'borrower_name' => $request->borrower_name,    // Wajib ada dari frontend
                'client_id' => $request->client_id,
                'client' => $request->client,
                'project_id' => $request->project_id,
                'project' => $request->project,
                'purpose' => $request->purpose,
                'date' => $request->date,
                'is_confirm' => false,
            ]);

            foreach ($request->items as $item) {
                $consumable = InvConsumable::findOrFail($item['consumable_id']);

                // Validasi stock sebelum simpan
                if ($item['qty'] > $consumable->stock) {
                    throw new \Exception(
                        "Stock {$consumable->name} hanya tersedia {$consumable->stock}, Anda meminta {$item['qty']}"
                    );
                }

                InvConsumableTransactionItem::create([
                    'transaction_id' => $trx->id,
                    'consumable_id' => $item['consumable_id'],
                    'qty' => $item['qty'],
                ]);
            }
        });

        return redirect()->route('transaksi.index')
            ->with('success', 'Permintaan consumable berhasil ditambahkan');
    }

    public function returnFull($id)
    {
        $trx = InvConsumableTransaction::with('items')->findOrFail($id);

        if ($trx->is_confirm) {
            return back()->with('error', 'Transaksi ini sudah dikonfirmasi sebelumnya');
        }

        DB::transaction(function () use ($trx) {
            foreach ($trx->items as $item) {
                InvConsumable::where('id', $item->consumable_id)
                    ->increment('stock', $item->qty);
            }

            $trx->update(['is_confirm' => true]);
        });

        return back()->with('success', 'Semua consumable berhasil dikembalikan');
    }

    public function edit($id)
    {
        $transaction = InvConsumableTransaction::with('items.consumable')->findOrFail($id);
        $consumables = InvConsumable::all();

        $initialClientId = $transaction->client_id;
        $initialProject = $transaction->project;

        if (!$initialClientId && $transaction->client) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Api Key',
                    'Accept' => 'application/json',
                ])->get('http://api-checkin.artimu.co.id/ar_client/list', [
                    'key' => 'k4v9X8F1kqPz1LpYbG7aNzR6VnZ0TpQm',
                    'search' => $transaction->client
                ]);

                $clients = $response->json();
                $found = collect($clients['data'] ?? $clients)
                    ->first(
                        fn($c) =>
                        strtolower($c['name'] ?? $c['client_name'] ?? '') ===
                            strtolower($transaction->client)
                    );

                if ($found) {
                    $initialClientId = $found['id'] ?? $found['client_id'] ?? null;
                }
            } catch (\Exception $e) {
                Log::warning('Gagal fetch client saat edit: ' . $e->getMessage());
            }
        }

        return view('transaksi.edit', compact(
            'transaction',
            'consumables',
            'initialClientId',
            'initialProject'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'nullable|string|max:255',
            'borrower_name' => 'required|string|max:255',
            'client_id' => 'nullable|string|max:255',
            'client' => 'nullable|string|max:255',
            'project_id' => 'nullable|string|max:255',
            'project' => 'nullable|string|max:255',
            'purpose' => 'nullable|string|max:255',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.consumable_id' => 'required|integer|exists:inv_consumables,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $id) {

            $trx = InvConsumableTransaction::with('items')->findOrFail($id);

            // Kalau sudah confirmed, kembalikan stock dulu sebelum update
            if ($trx->is_confirm) {
                foreach ($trx->items as $item) {
                    $sisa = $item->qty - ($item->qty_return ?? 0);
                    if ($sisa > 0) {
                        InvConsumable::where('id', $item->consumable_id)
                            ->increment('stock', $sisa);
                    }
                }
            }

            $trx->items()->delete();

            $trx->update([
                'employee_id' => $request->employee_id ?: null,
                'borrower_name' => $request->borrower_name,
                'client_id' => $request->client_id,
                'client' => $request->client,
                'project_id' => $request->project_id,
                'project' => $request->project,
                'purpose' => $request->purpose,
                'date' => $request->date,
            ]);

            foreach ($request->items as $item) {
                $consumable = InvConsumable::findOrFail($item['consumable_id']);

                if ($item['qty'] > $consumable->stock) {
                    throw new \Exception(
                        "Stock {$consumable->name} tidak cukup (tersedia: {$consumable->stock})"
                    );
                }

                InvConsumableTransactionItem::create([
                    'transaction_id' => $trx->id,
                    'consumable_id' => $item['consumable_id'],
                    'qty' => $item['qty'],
                ]);

                // Kalau transaksi sudah confirmed, langsung kurangi stock
                if ($trx->is_confirm) {
                    $consumable->decrement('stock', $item['qty']);
                }
            }
        });

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil diupdate');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            $trx = InvConsumableTransaction::with('items')->findOrFail($id);

            if ($trx->is_confirm) {
                foreach ($trx->items as $item) {
                    $sisa = $item->qty - ($item->qty_return ?? 0);
                    if ($sisa > 0) {
                        InvConsumable::where('id', $item->consumable_id)
                            ->increment('stock', $sisa);
                    }
                }
            }

            $trx->items()->delete();
            $trx->delete();
        });

        return back()->with('success', 'Anda telah menghapus transaksi');
    }

    public function returnItem(Request $request, $id)
    {
        DB::transaction(function () use ($request, $id) {

            $item = InvConsumableTransactionItem::findOrFail($id);

            $request->validate([
                'qty_return' => 'required|integer|min:1'
            ]);

            $sisa = $item->qty - $item->qty_return;

            if ($request->qty_return > $sisa) {
                throw new \Exception('Qty melebihi sisa');
            }

            $item->increment('qty_return', $request->qty_return);

            InvConsumable::where('id', $item->consumable_id)
                ->increment('stock', $request->qty_return);

            $trx = $item->transaction;
            $allReturned = $trx->items->every(function ($i) {
                return $i->qty == $i->qty_return;
            });

            $trx->update(['return_date' => $request->return_date]);

            if ($allReturned) {
                $trx->update(['is_return' => true]);
            }
        });

        return back()->with('success', 'Pengembalian consumable berhasil diproses');
    }

    public function returnProcess(Request $request, $id)
    {
        $request->validate([
            'return_date' => 'required|date',
            'selected_items' => 'required|array',
            'selected_items.*' => 'exists:inv_consumable_transaction_items,id',
        ]);

        DB::transaction(function () use ($request, $id) {

            $trx = InvConsumableTransaction::with('items')->findOrFail($id);

            foreach ($request->selected_items as $itemId) {
                $data = $request->items[$itemId] ?? null;
                if (!$data) continue;

                $qtyReturn = (int) ($data['qty'] ?? 0);
                if ($qtyReturn <= 0) continue;

                $item = InvConsumableTransactionItem::findOrFail($itemId);

                if ($item->transaction_id != $trx->id) {
                    throw new \Exception("Item tidak valid");
                }

                $sisa = $item->qty - $item->qty_return;
                if ($qtyReturn > $sisa) {
                    throw new \Exception("Qty return {$item->consumable->name} melebihi sisa pemakaian (sisa: {$sisa})");
                }

                $item->increment('qty_return', $qtyReturn);
                InvConsumable::where('id', $item->consumable_id)->increment('stock', $qtyReturn);
            }

            $allReturned = $trx->items->every(function ($i) {
                return $i->fresh()->qty == $i->fresh()->qty_return;
            });

            if ($allReturned) {
                $trx->update(['return_date' => $request->return_date]);
            }
        });

        return redirect()->route('transaksi.index')
            ->with('success', 'Pengembalian consumable berhasil diproses');
    }

    public function updateItem(Request $request, $id)
    {
        $item = InvConsumableTransactionItem::findOrFail($id);

        $request->validate(['qty' => 'required|integer|min:1']);

        $item->update(['qty' => $request->qty]);

        return back()->with('success', 'Jumlah consumable berhasil diupdate');
    }

    public function destroyItem($id)
    {
        $item = InvConsumableTransactionItem::findOrFail($id);
        $item->delete();
        return back()->with('success', 'Item consumable berhasil dihapus');
    }

    public function confirm($id)
    {
        try {
            DB::transaction(function () use ($id) {

                $trx = InvConsumableTransaction::with('items.consumable')->findOrFail($id);

                if ($trx->is_confirm) {
                    throw new \Exception("Transaksi sudah dikonfirmasi");
                }

                foreach ($trx->items as $item) {
                    $consumable = InvConsumable::lockForUpdate()->find($item->consumable_id);

                    if ($item->qty > $consumable->stock) {
                        throw new \Exception("Stock {$consumable->name} tidak cukup");
                    }

                    $consumable->decrement('stock', $item->qty);
                }

                $trx->update(['is_confirm' => true]);
            });

            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dikonfirmasi');
        } catch (\Exception $e) {
            return redirect()->route('transaksi.index')->with('error', $e->getMessage());
        }
    }

    public function kembali(Request $request, $id)
    {
        try {
            $request->validate(['return_date' => 'required|date']);

            DB::transaction(function () use ($request, $id) {

                $trx = InvConsumableTransaction::with('items.consumable')->findOrFail($id);

                foreach ($request->items as $itemId => $data) {
                    if (!empty($data['qty']) && $data['qty'] > 0) {
                        $item = InvConsumableTransactionItem::findOrFail($itemId);
                        $sisa = $item->qty - $item->qty_return;

                        if ($data['qty'] > $sisa) {
                            throw new \Exception("Qty melebihi sisa");
                        }

                        $item->increment('qty_return', $data['qty']);
                        $item->update(['note' => $data['note'] ?? '-']);
                        InvConsumable::where('id', $item->consumable_id)->increment('stock', $data['qty']);
                    }
                }

                $trx->load('items');
                $trx->update(['return_date' => $request->return_date]);

                $allReturned = $trx->items->every(function ($i) {
                    return $i->qty == $i->qty_return;
                });

                if ($allReturned) {
                    $trx->update(['is_return' => true]);
                }
            });

            return redirect()->route('transaksi.index')->with('success', 'Consumable berhasil dikembalikan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function storeItem(Request $request, $id)
    {
        $request->validate([
            'consumable_id' => 'required|integer|exists:inv_consumables,id',
            'qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $id) {
            $consumable = InvConsumable::findOrFail($request->consumable_id);

            if ($request->qty > $consumable->stock) {
                throw new \Exception("Stock tidak cukup");
            }

            InvConsumableTransactionItem::create([
                'transaction_id' => $id,
                'consumable_id' => $request->consumable_id,
                'qty' => $request->qty,
            ]);

            $consumable->decrement('stock', $request->qty);
        });

        return back()->with('success', 'Consumable berhasil ditambahkan ke transaksi');
    }

    // ===== PROXY API EMPLOYEE =====
    public function proxyEmployeeList(Request $request)
    {
        try {
            $response = Http::get('http://api-checkin.artimu.co.id/employee/list', [
                'key' => 'k4v9X8F1kqPz1LpYbG7aNzR6VnZ0TpQm',
                'search' => $request->search
            ]);
            return response()->json($response->json());
        } catch (\Exception $e) {
            Log::error('Proxy Employee Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengambil data employee'], 500);
        }
    }

    // ===== PROXY API CLIENT =====
    public function proxyClientList()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Api Key',
                'Accept' => 'application/json',
            ])->get('http://api-checkin.artimu.co.id/ar_client/list', [
                'key' => 'k4v9X8F1kqPz1LpYbG7aNzR6VnZ0TpQm'
            ]);
            return response()->json($response->json());
        } catch (\Exception $e) {
            Log::error('Proxy Client Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengambil data client'], 500);
        }
    }

    // ===== PROXY API PROJECT =====
    public function proxyClientProjects(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Api Key',
                'Accept' => 'application/json',
            ])->get('http://api-checkin.artimu.co.id/ar_client/projects', [
                'key' => 'k4v9X8F1kqPz1LpYbG7aNzR6VnZ0TpQm',
                'client_id' => $request->client_id
            ]);
            return response()->json($response->json());
        } catch (\Exception $e) {
            Log::error('Proxy Project Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengambil data project'], 500);
        }
    }
}