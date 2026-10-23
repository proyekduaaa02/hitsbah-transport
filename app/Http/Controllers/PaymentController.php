<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\Pembayaran;
use App\Services\PaymentService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $payments)
    {
    }

    public function show(Request $request, int $order): View|RedirectResponse
    {
        $order = $request->user()->orders()->with(['pembayaran', 'items.produk', 'bookings.service'])->findOrFail($order);

        if ($order->pembayaran && $order->pembayaran->status !== 'ditolak') {
            return redirect()->route('user.orders.show', $order->id_order);
        }

        return view('user.payment', ['order' => $order, 'metode' => Pembayaran::METODE]);
    }

    public function store(PaymentRequest $request, int $order): RedirectResponse
    {
        $order = $request->user()->orders()->with('pembayaran')->findOrFail($order);

        try {
            $this->payments->kirim($order, $request->input('metode'), $request->file('bukti_pembayaran'));
        } catch (DomainException $e) {
            return redirect()->route('user.orders.show', $order->id_order)->with('error', $e->getMessage());
        }

        return redirect()->route('user.orders.show', $order->id_order)
            ->with('success', 'Pembayaran berhasil dikirim. Mohon tunggu verifikasi dari admin.')
            ->with('checkout_selesai', true);
    }
}
