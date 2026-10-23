<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Models\CartItem;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $request->user()->getOrCreateCart();
        $cart->load('items.produk');

        return view('user.cart', ['cart' => $cart]);
    }

    public function add(AddToCartRequest $request): RedirectResponse|JsonResponse
    {
        $produk = Produk::findOrFail($request->integer('id_produk'));
        $jumlah = max(1, $request->integer('jumlah', 1));
        $cart = $request->user()->getOrCreateCart();

        $item = $cart->items()->where('id_produk', $produk->id_produk)->first();
        $totalBaru = ($item?->jumlah ?? 0) + $jumlah;

        if ($produk->stok < 1) {
            return $this->gagal($request, 'Stok produk habis.');
        }

        if ($totalBaru > $produk->stok) {
            return $this->gagal($request, 'Stok produk tidak mencukupi. Stok tersedia: '.$produk->stok.'.');
        }

        if ($item) {
            $item->update(['jumlah' => $totalBaru]);
        } else {
            $cart->items()->create(['id_produk' => $produk->id_produk, 'jumlah' => $jumlah]);
        }

        $pesan = 'Produk berhasil ditambahkan ke keranjang.';

        // Permintaan AJAX (tombol "Tambah ke Keranjang") menerima JSON tanpa reload halaman.
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $pesan,
                'cart_count' => (int) $cart->items()->sum('jumlah'),
            ]);
        }

        return back()->with('success', $pesan);
    }

    private function gagal(Request $request, string $pesan): RedirectResponse|JsonResponse
    {
        return $request->expectsJson()
            ? response()->json(['message' => $pesan], 422)
            : back()->with('error', $pesan);
    }

    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $this->pastikanMilikUser($request, $item);

        $data = $request->validate(['jumlah' => ['required', 'integer', 'min:1', 'max:100']]);
        $stok = $item->produk->stok;

        if ($data['jumlah'] > $stok) {
            return back()->with('error', 'Stok produk tidak mencukupi. Stok tersedia: '.$stok.'.');
        }

        $item->update(['jumlah' => $data['jumlah']]);

        return back()->with('success', 'Jumlah produk diperbarui.');
    }

    public function remove(Request $request, CartItem $item): RedirectResponse
    {
        $this->pastikanMilikUser($request, $item);
        $item->delete();

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    private function pastikanMilikUser(Request $request, CartItem $item): void
    {
        abort_unless($item->cart?->id_user === $request->user()->id_user, 403);
    }
}
