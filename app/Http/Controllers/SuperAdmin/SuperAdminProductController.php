<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Admin\ManageController;
use App\Http\Requests\ProductRequest;
use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SuperAdminProductController extends ManageController
{
    protected string $prefix = 'superadmin';

    protected string $layout = 'layouts.super-admin';

    public function index(Request $request): View
    {
        $daftar = Produk::query()
            ->with('kategori:id_kategori,nama')
            ->search($request->query('q'))
            ->when($request->query('kategori'), fn ($q, $k) => $q->where('id_kategori', (int) $k))
            ->when($request->query('stok') === 'menipis', fn ($q) => $q->where('stok', '<=', 5))
            ->urut($request->query('urut'))
            ->paginate(10)
            ->withQueryString();

        return $this->render('super-admin.products.index', [
            'products' => $daftar,
            'kategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function create(): View
    {
        return $this->render('super-admin.products.form', [
            'produk' => new Produk(['stok' => 0]),
            'kategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('gambar');

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('products', 'public');
        }

        Produk::create($data);

        return redirect()->route('superadmin.products.index')->with('success', 'Data produk berhasil ditambahkan.');
    }

    public function edit(Produk $product): View
    {
        return $this->render('super-admin.products.form', [
            'produk' => $product,
            'kategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function update(ProductRequest $request, Produk $product): RedirectResponse
    {
        $data = $request->safe()->except('gambar');

        if ($request->hasFile('gambar')) {
            if ($product->gambar) {
                Storage::disk('public')->delete($product->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('superadmin.products.index')->with('success', 'Data produk berhasil diperbarui.');
    }

    public function destroy(Produk $product): RedirectResponse
    {
        if ($product->orderItems()->exists()) {
            return back()->with('error', 'Produk tidak dapat dihapus karena sudah ada dalam riwayat pesanan.');
        }

        if ($product->gambar) {
            Storage::disk('public')->delete($product->gambar);
        }

        $product->delete();

        return back()->with('success', 'Data produk berhasil dihapus.');
    }
}
