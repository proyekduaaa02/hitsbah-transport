<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\InstallationService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private OrderService $orders)
    {
    }

    public function show(Request $request): View|RedirectResponse
    {
        $cart = $request->user()->getOrCreateCart();
        $cart->load('items.produk');

        if ($cart->items->isEmpty()) {
            return redirect()->route('user.cart.index')->with('error', 'Keranjang Anda masih kosong.');
        }

        return view('user.checkout', [
            'cart' => $cart,
            'jasa' => InstallationService::query()->orderBy('nama')->get(),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        $jasa = $request->filled('id_services')
            ? ['id_services' => $request->integer('id_services'), 'tanggal' => $request->input('tanggal'), 'jam' => $request->input('jam')]
            : null;

        $order = $this->orders->checkoutKeranjang($request->user(), $jasa);

        return redirect()->route('user.orders.show', ['order' => $order->id_order, 'baru' => 1])
            ->with('success', 'Pesanan berhasil dibuat. Silakan lanjutkan ke pembayaran.');
    }
}
