<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Admin\ManageController;
use App\Models\Kategori;
use App\Models\Order;
use App\Models\Pembayaran;
use App\Models\Produk;
use App\Models\ServiceBooking;
use App\Models\User;
use App\Services\SalesReportService;
use Illuminate\View\View;

class SuperAdminDashboardController extends ManageController
{
    protected string $prefix = 'superadmin';

    protected string $layout = 'layouts.super-admin';

    public function index(SalesReportService $laporan): View
    {
        $stat = [
            'produk' => Produk::count(),
            'kategori' => Kategori::count(),
            'user' => User::role('user')->count(),
            'pesanan' => Order::count(),
            'pendapatan' => (float) Order::where('status', Order::STATUS_SELESAI)->sum('total_pembayaran'),
            'pembayaran_pending' => Pembayaran::where('status', 'pending')->count(),
            'pemasangan_aktif' => ServiceBooking::aktif()->count(),
        ];

        return $this->render('super-admin.dashboard', [
            'stat' => $stat,
            'grafik' => $laporan->pendapatanBulanan(12),
            'pesananTerbaru' => Order::with(['user:id_user,nama', 'pembayaran'])->latest('tanggal_order')->take(6)->get(),
            'stokMenipis' => Produk::with('kategori:id_kategori,nama')->where('stok', '<=', 5)->orderBy('stok')->take(5)->get(),
        ]);
    }
}
