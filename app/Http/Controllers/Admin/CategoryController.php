<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori.
     */
    public function index(): View
    {
        $categories = Kategori::all();

        return view('admin.category.index', compact('categories'));
    }

    /**
     * Menyimpan kategori baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        if (!isset($payload['nama'])) {
            return redirect()->route('admin.categories.index')->with('error', 'Nama kategori wajib diisi.');
        }

        Kategori::create([
            'nama' => $payload['nama'],
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Memperbarui kategori.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $payload = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        if (!isset($payload['nama'])) {
            return redirect()->route('admin.categories.index')->with('error', 'Nama kategori wajib diisi.');
        }

        $category = Kategori::findOrFail($id);
        $category->nama = $payload['nama'];
        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Menghapus kategori.
     */
    public function destroy(string $id): RedirectResponse
    {
        Kategori::destroy($id);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
