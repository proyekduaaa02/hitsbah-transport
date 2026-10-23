<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServiceBooking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AdminBookingController extends ManageController
{
    public function __construct(protected BookingService $bookings)
    {
    }

    public function index(Request $request): View
    {
        $daftar = ServiceBooking::query()
            ->with(['user:id_user,nama,email', 'service:id_services,nama,durasi_pemasangan', 'order.pembayaran'])
            ->when($request->query('status'), fn ($q, $s) => $q->where('status_pemasangan', $s))
            ->when($request->query('tanggal'), fn ($q, $t) => $q->whereDate('jadwal_pemasangan', $t))
            ->when($request->query('q'), fn ($q, $kata) => $q->whereHas('user', fn ($u) => $u->where('nama', 'like', "%{$kata}%")))
            ->orderBy('jadwal_pemasangan', 'desc')
            ->paginate(10)
            ->withQueryString();

        return $this->render('manage.bookings.index', [
            'bookings' => $daftar,
            'jamBuka' => $this->bookings->jamBuka(),
            'jamTutup' => $this->bookings->jamTutup(),
        ]);
    }

    public function update(Request $request, ServiceBooking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status_pemasangan' => ['required', 'in:menunggu,dijadwalkan,diproses,selesai,dibatalkan'],
            'jadwal_pemasangan' => ['required', 'date_format:Y-m-d\TH:i'],
        ]);

        $booking->load(['order.pembayaran', 'service']);

        if ($booking->order->status === 'ditolak' && $data['status_pemasangan'] !== 'dibatalkan') {
            return back()->with('error', 'Pesanan ini sudah ditolak, booking hanya dapat berstatus dibatalkan.');
        }

        $statusBayar = $booking->order->pembayaran?->status;
        $butuhBayar = in_array($data['status_pemasangan'], ['dijadwalkan', 'diproses', 'selesai'], true);
        if ($butuhBayar && ! in_array($statusBayar, ['diverifikasi', 'berhasil'], true)) {
            return back()->with('error', 'Pemasangan belum dapat dijadwalkan atau diproses sebelum pembayaran diverifikasi.');
        }

        $jadwalBaru = Carbon::parse($data['jadwal_pemasangan']);
        if (! $jadwalBaru->equalTo($booking->jadwal_pemasangan)) {
            $this->bookings->jadwalkanUlang($booking, $jadwalBaru);
        }

        $booking->update(['status_pemasangan' => $data['status_pemasangan']]);

        return back()->with('success', 'Jadwal pemasangan berhasil diperbarui.');
    }
}
