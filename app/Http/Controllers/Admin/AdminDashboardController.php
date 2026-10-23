<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Pembayaran;
use App\Models\ServiceBooking;
use Illuminate\View\View;

class AdminDashboardController extends ManageController
{
    public function index(): View
    {
        $stat = [
            'total' => Order::count(),
            'baru' => Order::where('status', Order::STATUS_PENDING)->count(),
            'diproses' => Order::whereIn('status', [Order::STATUS_DITERIMA, Order::STATUS_DIPROSES])->count(),
            'selesai' => Order::where('status', Order::STATUS_SELESAI)->count(),
            'pembayaran_pending' => Pembayaran::where('status', 'pending')->count(),
            'terjadwal' => ServiceBooking::where('status_pemasangan', 'dijadwalkan')->count(),
        ];

        return $this->render('admin.dashboard', [
            'stat' => $stat,
            'pesananTerbaru' => Order::with(['user:id_user,nama', 'pembayaran'])->latest('tanggal_order')->take(6)->get(),
            'pembayaranMenunggu' => Pembayaran::with('order.user:id_user,nama')->where('status', 'pending')->oldest()->take(5)->get(),
            'jadwalTerdekat' => ServiceBooking::with(['user:id_user,nama', 'service:id_services,nama'])
                ->aktif()->where('jadwal_pemasangan', '>=', now()->startOfDay())
                ->orderBy('jadwal_pemasangan')->take(5)->get(),
        ]);
    }
}
