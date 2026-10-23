<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pembayaran;
use Illuminate\Support\Carbon;

class SalesReportService
{
    public const PERIODE = ['hari' => 'Hari', 'minggu' => 'Minggu', 'bulan' => 'Bulan', 'tahun' => 'Tahun'];

    /** @return array{0: Carbon, 1: Carbon} */
    public function rentang(string $periode, Carbon $acuan): array
    {
        return match ($periode) {
            'hari' => [$acuan->copy()->startOfDay(), $acuan->copy()->endOfDay()],
            'minggu' => [$acuan->copy()->startOfWeek(), $acuan->copy()->endOfWeek()],
            'tahun' => [$acuan->copy()->startOfYear(), $acuan->copy()->endOfYear()],
            default => [$acuan->copy()->startOfMonth(), $acuan->copy()->endOfMonth()],
        };
    }

    /**
     * Ringkasan laporan. Pendapatan dihitung dari pesanan berstatus "selesai".
     */
    public function bangun(string $periode, Carbon $acuan): array
    {
        [$dari, $sampai] = $this->rentang($periode, $acuan);

        $orders = Order::query()
            ->whereBetween('tanggal_order', [$dari, $sampai])
            ->with('user:id_user,nama')
            ->orderBy('tanggal_order')
            ->get();

        $selesai = $orders->where('status', Order::STATUS_SELESAI);

        $produkTerjual = (int) OrderItem::query()
            ->whereIn('id_order', $selesai->pluck('id_order'))
            ->sum('jumlah');

        $terlaris = OrderItem::query()
            ->selectRaw('id_produk, SUM(jumlah) as total_terjual, SUM(subtotal) as total_nilai')
            ->whereIn('id_order', $selesai->pluck('id_order'))
            ->groupBy('id_produk')
            ->orderByDesc('total_terjual')
            ->with('produk:id_produk,nama')
            ->limit(5)
            ->get();

        $pembayaranBerhasil = Pembayaran::query()
            ->where('status', 'berhasil')
            ->whereIn('id_order', $orders->pluck('id_order'))
            ->count();

        [$labels, $nilai, $jumlah] = $this->grafik($periode, $dari, $selesai, $orders);

        return [
            'periode' => $periode,
            'dari' => $dari,
            'sampai' => $sampai,
            'total_transaksi' => $orders->count(),
            'transaksi_berhasil' => $selesai->count(),
            'transaksi_ditolak' => $orders->where('status', Order::STATUS_DITOLAK)->count(),
            'total_pendapatan' => (float) $selesai->sum('total_pembayaran'),
            'produk_terjual' => $produkTerjual,
            'pembayaran_berhasil' => $pembayaranBerhasil,
            'produk_terlaris' => $terlaris,
            'transaksi' => $orders->sortByDesc('tanggal_order')->values(),
            'grafik' => ['labels' => $labels, 'pendapatan' => $nilai, 'transaksi' => $jumlah],
        ];
    }

    /** Data grafik per jam / hari / bulan sesuai periode. */
    private function grafik(string $periode, Carbon $dari, $selesai, $semua): array
    {
        $labels = [];
        $nilai = [];
        $jumlah = [];

        if ($periode === 'hari') {
            for ($jam = 0; $jam < 24; $jam++) {
                $labels[] = sprintf('%02d:00', $jam);
                $nilai[] = (float) $selesai->filter(fn ($o) => $o->tanggal_order->hour === $jam)->sum('total_pembayaran');
                $jumlah[] = $semua->filter(fn ($o) => $o->tanggal_order->hour === $jam)->count();
            }

            return [$labels, $nilai, $jumlah];
        }

        if ($periode === 'tahun') {
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $labels[] = Carbon::create($dari->year, $bulan, 1)->locale('id')->translatedFormat('M');
                $nilai[] = (float) $selesai->filter(fn ($o) => $o->tanggal_order->month === $bulan)->sum('total_pembayaran');
                $jumlah[] = $semua->filter(fn ($o) => $o->tanggal_order->month === $bulan)->count();
            }

            return [$labels, $nilai, $jumlah];
        }

        $akhir = $periode === 'minggu' ? $dari->copy()->endOfWeek() : $dari->copy()->endOfMonth();
        for ($hari = $dari->copy(); $hari <= $akhir; $hari->addDay()) {
            $tgl = $hari->toDateString();
            $labels[] = $periode === 'minggu' ? $hari->locale('id')->translatedFormat('D d/m') : $hari->format('d');
            $nilai[] = (float) $selesai->filter(fn ($o) => $o->tanggal_order->toDateString() === $tgl)->sum('total_pembayaran');
            $jumlah[] = $semua->filter(fn ($o) => $o->tanggal_order->toDateString() === $tgl)->count();
        }

        return [$labels, $nilai, $jumlah];
    }

    /** Pendapatan 12 bulan terakhir untuk grafik dashboard Super Admin. */
    public function pendapatanBulanan(int $jumlahBulan = 12): array
    {
        $mulai = now()->startOfMonth()->subMonths($jumlahBulan - 1);

        $orders = Order::query()
            ->where('status', Order::STATUS_SELESAI)
            ->where('tanggal_order', '>=', $mulai)
            ->get(['id_order', 'tanggal_order', 'total_pembayaran']);

        $labels = [];
        $nilai = [];
        $jumlah = [];
        for ($i = 0; $i < $jumlahBulan; $i++) {
            $bulan = $mulai->copy()->addMonths($i);
            $labels[] = $bulan->locale('id')->translatedFormat('M y');
            $dalamBulan = $orders->filter(fn ($o) => $o->tanggal_order->isSameMonth($bulan));
            $nilai[] = (float) $dalamBulan->sum('total_pembayaran');
            $jumlah[] = $dalamBulan->count();
        }

        return ['labels' => $labels, 'pendapatan' => $nilai, 'transaksi' => $jumlah];
    }
}
