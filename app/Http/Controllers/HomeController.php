<?php

namespace App\Http\Controllers;

use App\Models\InstallationService;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Review;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'totalProduk' => Produk::count(),
            'totalJasa' => InstallationService::count(),
            'produkUnggulan' => Produk::query()->with('kategori')->withRating()->orderByDesc('reviews_count')->latest('id_produk')->take(8)->get(),
            'kategori' => Kategori::query()->withCount('produk')->orderBy('nama')->get(),
            'jasa' => InstallationService::query()->orderBy('harga')->take(4)->get(),
            'testimoni' => Review::query()->with(['user:id_user,nama', 'produk:id_produk,nama'])->where('rating', '>=', 4)->latest()->take(3)->get(),
        ]);
    }
}
