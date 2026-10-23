<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Produk;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /** Produk dari pesanan selesai milik user + ulasan yang sudah ditulis. */
    public function index(Request $request): View
    {
        $user = $request->user();

        $idProduk = OrderItem::query()
            ->whereHas('order', fn ($q) => $q->where('id_user', $user->id_user)->where('status', Order::STATUS_SELESAI))
            ->pluck('id_produk')->unique();

        return view('user.reviews', [
            'produk' => Produk::query()->whereIn('id_produk', $idProduk)->get(),
            'ulasan' => $user->reviews()->get()->keyBy('id_produk'),
        ]);
    }

    public function store(ReviewRequest $request): RedirectResponse
    {
        $user = $request->user();

        $pernahBeli = OrderItem::query()
            ->where('id_produk', $request->integer('id_produk'))
            ->whereHas('order', fn ($q) => $q->where('id_user', $user->id_user)->where('status', Order::STATUS_SELESAI))
            ->exists();

        if (! $pernahBeli) {
            return back()->with('error', 'Anda hanya dapat mengulas produk dari pesanan yang sudah selesai.');
        }

        Review::updateOrCreate(
            ['id_user' => $user->id_user, 'id_produk' => $request->integer('id_produk')],
            ['rating' => $request->integer('rating'), 'komen' => $request->input('komen')]
        );

        return back()->with('success', 'Terima kasih! Ulasan Anda berhasil disimpan.');
    }
}
