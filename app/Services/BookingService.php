<?php

namespace App\Services;

use App\Models\InstallationService;
use App\Models\Order;
use App\Models\ServiceBooking;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Logika jadwal pemasangan. Pemasangan hanya dilayani di tempat usaha (sesuai batasan proposal).
 */
class BookingService
{
    public const MAKS_HARI_KE_DEPAN = 60;

    public function jamBuka(): string
    {
        return (string) Setting::ambil('jam_buka', '08:00');
    }

    public function jamTutup(): string
    {
        return (string) Setting::ambil('jam_tutup', '16:00');
    }

    public function kapasitas(): int
    {
        return max(1, (int) Setting::ambil('kapasitas_pemasangan', 2));
    }

    /**
     * Daftar slot jam untuk satu tanggal dan jasa tertentu.
     *
     * @return array<int, array{jam: string, tersedia: bool}>
     */
    public function slotUntukTanggal(Carbon $tanggal, InstallationService $service): array
    {
        $slots = [];
        $mulai = $tanggal->copy()->setTimeFromTimeString($this->jamBuka());
        $tutup = $tanggal->copy()->setTimeFromTimeString($this->jamTutup());

        // Ambil booking pada hari itu sekali saja (hindari N+1).
        $bookingHariIni = ServiceBooking::query()
            ->memakaiSlot()
            ->whereDate('jadwal_pemasangan', $tanggal->toDateString())
            ->with('service:id_services,durasi_pemasangan')
            ->get();

        for ($jam = $mulai->copy(); $jam < $tutup; $jam->addHour()) {
            $slots[] = [
                'jam' => $jam->format('H:i'),
                'tersedia' => $this->tersedia($jam->copy(), $service, $bookingHariIni),
            ];
        }

        return $slots;
    }

    /**
     * Cek apakah jadwal masih tersedia: di jam operasional, belum lewat, dan kapasitas belum penuh.
     */
    public function tersedia(Carbon $mulai, InstallationService $service, $bookingHariIni = null): bool
    {
        $selesai = $mulai->copy()->addMinutes($service->durasi_pemasangan);
        $tutup = $mulai->copy()->setTimeFromTimeString($this->jamTutup());
        $buka = $mulai->copy()->setTimeFromTimeString($this->jamBuka());

        if ($mulai->lt(now()->addHour()) || $mulai->lt($buka) || $selesai->gt($tutup)) {
            return false;
        }

        if ($mulai->gt(now()->addDays(self::MAKS_HARI_KE_DEPAN))) {
            return false;
        }

        $bookingHariIni ??= ServiceBooking::query()
            ->memakaiSlot()
            ->whereDate('jadwal_pemasangan', $mulai->toDateString())
            ->with('service:id_services,durasi_pemasangan')
            ->get();

        $bentrok = $bookingHariIni->filter(function (ServiceBooking $b) use ($mulai, $selesai) {
            $bMulai = $b->jadwal_pemasangan;
            $bSelesai = $bMulai->copy()->addMinutes($b->service->durasi_pemasangan ?? 60);

            return $bMulai->lt($selesai) && $bSelesai->gt($mulai);
        })->count();

        return $bentrok < $this->kapasitas();
    }

    /**
     * Buat booking untuk sebuah pesanan. Melempar ValidationException bila jadwal tidak tersedia.
     */
    public function buat(Order $order, User $user, int $idServices, string $tanggal, string $jam): ServiceBooking
    {
        $service = InstallationService::findOrFail($idServices);
        $mulai = Carbon::parse("{$tanggal} {$jam}");

        if (! $this->tersedia($mulai, $service)) {
            throw ValidationException::withMessages([
                'jam' => 'Jadwal pemasangan yang dipilih sudah tidak tersedia. Silakan pilih jadwal lain.',
            ]);
        }

        return ServiceBooking::create([
            'id_user' => $user->id_user,
            'id_order' => $order->id_order,
            'id_services' => $service->id_services,
            'jadwal_pemasangan' => $mulai,
            'status_pemasangan' => 'menunggu',
            'harga' => $service->harga,
        ]);
    }

    /** Pindahkan jadwal booking (oleh admin), dengan pengecekan kapasitas. */
    public function jadwalkanUlang(ServiceBooking $booking, Carbon $mulai): void
    {
        $service = $booking->service;
        $lain = ServiceBooking::query()
            ->memakaiSlot()
            ->where('id_services_booking', '!=', $booking->id_services_booking)
            ->whereDate('jadwal_pemasangan', $mulai->toDateString())
            ->with('service:id_services,durasi_pemasangan')
            ->get();

        $selesai = $mulai->copy()->addMinutes($service->durasi_pemasangan);
        $buka = $mulai->copy()->setTimeFromTimeString($this->jamBuka());
        $tutup = $mulai->copy()->setTimeFromTimeString($this->jamTutup());

        if ($mulai->lt($buka) || $selesai->gt($tutup)) {
            throw ValidationException::withMessages(['jadwal_pemasangan' => 'Jadwal di luar jam operasional pemasangan.']);
        }

        $bentrok = $lain->filter(function (ServiceBooking $b) use ($mulai, $selesai) {
            $bSelesai = $b->jadwal_pemasangan->copy()->addMinutes($b->service->durasi_pemasangan ?? 60);

            return $b->jadwal_pemasangan->lt($selesai) && $bSelesai->gt($mulai);
        })->count();

        if ($bentrok >= $this->kapasitas()) {
            throw ValidationException::withMessages(['jadwal_pemasangan' => 'Kapasitas pemasangan pada jadwal tersebut sudah penuh.']);
        }

        $booking->update(['jadwal_pemasangan' => $mulai]);
    }
}
