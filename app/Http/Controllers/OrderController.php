<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()->orders()
            ->with(['pembayaran', 'items.produk:id_produk,nama', 'bookings.service:id_services,nama'])
            ->status($request->query('status'))
            ->latest('tanggal_order')
            ->paginate(8)
            ->withQueryString();

        return view('user.orders.index', ['orders' => $orders]);
    }

    public function show(Request $request, int $order): View
    {
        $order = $request->user()->orders()
            ->with(['items.produk', 'pembayaran', 'bookings.service'])
            ->findOrFail($order);

        return view('user.orders.show', [
            'order' => $order,
            'baru' => $request->boolean('baru'),
        ]);
    }
}
