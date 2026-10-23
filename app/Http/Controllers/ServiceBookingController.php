<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\InstallationService;
use App\Services\BookingService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ServiceBookingController extends Controller
{
    public function __construct(private BookingService $bookings, private OrderService $orders)
    {
    }

    /** Jadwal pemasangan yang akan datang / sedang berjalan. */
    public function index(Request $request): View
    {
        return view('user.services.index', [
            'bookings' => $request->user()->serviceBookings()
                ->aktif()->with(['service', 'order.pembayaran'])
                ->orderBy('jadwal_pemasangan')->get(),
        ]);
    }

    /** Riwayat pemasangan yang sudah selesai / dibatalkan. */
    public function history(Request $request): View
    {
        return view('user.services.history', [
            'bookings' => $request->user()->serviceBookings()
                ->whereIn('status_pemasangan', ['selesai', 'dibatalkan'])
                ->with(['service', 'order'])
                ->latest('jadwal_pemasangan')->paginate(10),
        ]);
    }

    public function create(Request $request): View
    {
        return view('user.services.booking', [
            'jasa' => InstallationService::query()->orderBy('nama')->get(),
            'dipilih' => $request->integer('jasa') ?: null,
        ]);
    }

    public function store(BookingRequest $request): RedirectResponse
    {
        $order = $this->orders->pesanJasa(
            $request->user(),
            $request->integer('id_services'),
            $request->input('tanggal'),
            $request->input('jam'),
        );

        return redirect()->route('user.orders.show', ['order' => $order->id_order, 'baru' => 1])
            ->with('success', 'Booking pemasangan berhasil dibuat. Silakan lanjutkan ke pembayaran.');
    }

    /** Slot jam yang tersedia (dipanggil via fetch oleh Alpine.js). */
    public function slots(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_services' => ['required', 'integer', 'exists:installation_services,id_services'],
            'tanggal' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
        ]);

        $service = InstallationService::findOrFail($data['id_services']);

        return response()->json([
            'slots' => $this->bookings->slotUntukTanggal(Carbon::parse($data['tanggal']), $service),
            'durasi' => $service->durasi_label,
        ]);
    }
}
