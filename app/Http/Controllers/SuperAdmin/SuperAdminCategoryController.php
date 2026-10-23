<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Admin\ManageController;
use App\Http\Requests\CategoryRequest;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperAdminCategoryController extends ManageController
{
    protected string $prefix = 'superadmin';

    protected string $layout = 'layouts.super-admin';

    public function index(Request $request): View
    {
        return $this->render('super-admin.categories.index', [
            'categories' => Kategori::query()
                ->withCount('produk')
                ->when($request->query('q'), fn ($q, $kata) => $q->where('nama', 'like', "%{$kata}%"))
                ->orderBy('nama')
                ->paginate(10)
                ->withQueryString(),
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        Kategori::create($request->validated());

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(CategoryRequest $request, Kategori $category): RedirectResponse
    {
        $category->update($request->validated());

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $category): RedirectResponse
    {
        if ($category->produk()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk.');
        }

        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
