<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Services\OrderService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrderController extends ManageController
{
    public function __construct(protected OrderService $orders)
    {
    }

    public function index(Request $request): View
    {
        $daftar = Order::query()
            ->with(['user:id_user,nama,email', 'pembayaran', 'bookings.service:id_services,nama'])
            ->withCount('items')
            ->search($request->query('q'))
            ->status($request->query('status'))
            ->tanggal($request->query('dari'), $request->query('sampai'))
            ->latest('tanggal_order')
            ->paginate(10)
            ->withQueryString();

        return $this->render('manage.orders.index', ['orders' => $daftar]);
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items.produk', 'pembayaran', 'bookings.service']);

        return $this->render('manage.orders.show', [
            'order' => $order,
            'transisi' => $this->orders->transisiTersedia($order),
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:diterima,diproses,selesai,ditolak']]);

        try {
            $this->orders->ubahStatus($order, $data['status']);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
