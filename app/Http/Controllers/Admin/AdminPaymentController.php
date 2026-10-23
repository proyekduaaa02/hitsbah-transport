<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pembayaran;
use App\Services\PaymentService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPaymentController extends ManageController
{
    public function __construct(protected PaymentService $payments)
    {
    }

    public function index(Request $request): View
    {
        $daftar = Pembayaran::query()
            ->with('order.user:id_user,nama,email')
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->query('q'), fn ($q, $kata) => $q->whereHas('order.user', fn ($u) => $u->where('nama', 'like', "%{$kata}%")))
            ->latest('tanggal_bayar')
            ->paginate(10)
            ->withQueryString();

        return $this->render('manage.payments.index', ['payments' => $daftar]);
    }

    public function show(Pembayaran $payment): View
    {
        $payment->load(['order.user', 'order.items.produk', 'order.bookings.service']);

        return $this->render('manage.payments.show', ['payment' => $payment]);
    }

    public function verify(Pembayaran $payment): RedirectResponse
    {
        try {
            $this->payments->verifikasi($payment);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pembayaran berhasil diverifikasi. Pesanan masuk ke tahap diproses.');
    }

    public function reject(Request $request, Pembayaran $payment): RedirectResponse
    {
        $data = $request->validate(['catatan' => ['nullable', 'string', 'max:500']]);

        try {
            $this->payments->tolak($payment, $data['catatan'] ?? null);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pembayaran ditolak. Pelanggan dapat mengirim ulang bukti pembayaran.');
    }
}
