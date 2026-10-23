<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $pesananAktif = $user->orders()->aktif()->count();
        $pesananSelesai = $user->orders()->where('status', Order::STATUS_SELESAI)->count();

        return view('user.dashboard', [
            'pesananAktif' => $pesananAktif,
            'pesananSelesai' => $pesananSelesai,
            'bookingAktif' => $user->serviceBookings()->aktif()->count(),
            'menungguBayar' => $user->orders()->aktif()->doesntHave('pembayaran')->count(),
            'pesananTerbaru' => $user->orders()->with(['pembayaran', 'bookings'])->latest('tanggal_order')->take(5)->get(),
            'jadwalBerikut' => $user->serviceBookings()->aktif()->with('service:id_services,nama')
                ->where('jadwal_pemasangan', '>=', now())->orderBy('jadwal_pemasangan')->first(),
            'produkTerbaru' => Produk::with('kategori')->withRating()->latest('id_produk')->take(4)->get(),
        ]);
    }
}
