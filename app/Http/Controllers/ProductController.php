<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request, ?Kategori $kategori = null): View
    {
        $produk = Produk::query()
            ->with('kategori')
            ->withRating()
            ->search($request->query('q'))
            ->when($kategori, fn ($q) => $q->where('id_kategori', $kategori->id_kategori))
            ->when(! $kategori && $request->filled('kategori'), fn ($q) => $q->where('id_kategori', (int) $request->query('kategori')))
            ->urut($request->query('urut'))
            ->paginate(12)
            ->withQueryString();

        return view('produk.index', [
            'produk' => $produk,
            'kategoriAktif' => $kategori,
            'daftarKategori' => Kategori::query()->orderBy('nama')->get(),
        ]);
    }

    public function show(Produk $produk): View
    {
        $produk->load('kategori')->loadAvg('reviews', 'rating')->loadCount('reviews');

        return view('produk.show', [
            'produk' => $produk,
            'ulasan' => $produk->reviews()->with('user:id_user,nama')->latest()->paginate(5),
            'terkait' => Produk::query()->with('kategori')->withRating()
                ->where('id_kategori', $produk->id_kategori)
                ->where('id_produk', '!=', $produk->id_produk)
                ->take(4)->get(),
        ]);
    }
}
