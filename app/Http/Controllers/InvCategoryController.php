<?php

namespace App\Http\Controllers;

use App\Models\InvCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = InvCategory::query()
            ->when($request->search, function ($query, $search) {
                $query->where('category_name', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%");
            })
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:inv_category,category_name',
        ], [
            'category_name.required' => 'Nama kategori wajib diisi.',
            'category_name.unique'   => 'Nama kategori sudah ada.',
            'category_name.max'      => 'Nama kategori maksimal 255 karakter.',
        ]);

        DB::beginTransaction();
        try {
            InvCategory::create([
                'id'            => InvCategory::generateId(),
                'category_name' => $request->category_name,
            ]);

            DB::commit();

            return redirect()
                ->route('categories.index')
                ->with('success', 'Kategori berhasil ditambahkan.');

        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }

    public function edit(InvCategory $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, InvCategory $category)
    {
        $request->validate([
            'category_name' => 'required|string|max:255|unique:inv_category,category_name,' . $category->id . ',id',
        ], [
            'category_name.required' => 'Nama kategori wajib diisi.',
            'category_name.unique'   => 'Nama kategori sudah ada.',
            'category_name.max'      => 'Nama kategori maksimal 255 karakter.',
        ]);

        DB::beginTransaction();
        try {
            $category->update([
                'category_name' => $request->category_name,
            ]);

            DB::commit();

            return redirect()
                ->route('categories.index')
                ->with('success', 'Kategori berhasil diperbarui.');

        } catch (\Throwable $th) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }

    public function destroy(InvCategory $category)
    {
        $consumableCount = $category->consumables()->count();
        $toolkitCount    = $category->toolkits()->count();

        if ($consumableCount > 0 || $toolkitCount > 0) {
            $msg = 'Kategori tidak bisa dihapus karena masih digunakan oleh ';
            $parts = [];
            if ($consumableCount > 0) $parts[] = "{$consumableCount} consumable";
            if ($toolkitCount > 0)    $parts[] = "{$toolkitCount} toolkit";
            $msg .= implode(' dan ', $parts) . '.';

            return redirect()
                ->route('categories.index')
                ->with('error', $msg);
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}