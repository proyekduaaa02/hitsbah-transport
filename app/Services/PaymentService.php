<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Pembayaran;
use DomainException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Pembayaran manual-digital: pelanggan mengirim bukti (QRIS / e-wallet) atau memilih bayar di tempat,
 * kemudian Admin / Super Admin memverifikasi. Tidak ada Virtual Account Bank (sesuai batasan proposal).
 */
class PaymentService
{
    public function kirim(Order $order, string $metode, ?UploadedFile $bukti): Pembayaran
    {
        if (! in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_DITERIMA], true)) {
            throw new DomainException('Pesanan ini sudah tidak dapat dibayar.');
        }

        $existing = $order->pembayaran;
        if ($existing && ! in_array($existing->status, ['ditolak'], true)) {
            throw new DomainException('Pembayaran untuk pesanan ini sudah dikirim.');
        }

        $path = $existing?->bukti_pembayaran;
        if ($bukti) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            $path = $bukti->store('payments', 'public');
        } elseif ($metode === 'tunai') {
            $path = null;
        }

        return Pembayaran::updateOrCreate(
            ['id_order' => $order->id_order],
            [
                'metode' => $metode,
                'total_pembayaran' => $order->total_pembayaran,
                'status' => 'pending',
                'bukti_pembayaran' => $path,
                'tanggal_bayar' => now(),
                'catatan' => null,
            ]
        );
    }

    /** Setujui pembayaran: pesanan masuk "diproses" dan jadwal pemasangan menjadi "dijadwalkan". */
    public function verifikasi(Pembayaran $pembayaran): void
    {
        if ($pembayaran->status !== 'pending') {
            throw new DomainException('Hanya pembayaran berstatus menunggu verifikasi yang dapat diverifikasi.');
        }

        DB::transaction(function () use ($pembayaran) {
            $order = $pembayaran->order()->lockForUpdate()->first();

            if (! in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_DITERIMA], true)) {
                throw new DomainException('Status pesanan tidak memungkinkan verifikasi pembayaran.');
            }

            $pembayaran->update(['status' => 'diverifikasi', 'catatan' => null]);
            $order->update(['status' => Order::STATUS_DIPROSES]);
            $order->bookings()->where('status_pemasangan', 'menunggu')->update(['status_pemasangan' => 'dijadwalkan']);
        });
    }

    /** Tolak pembayaran: pelanggan dapat mengirim ulang bukti pembayaran. */
    public function tolak(Pembayaran $pembayaran, ?string $alasan): void
    {
        if ($pembayaran->status !== 'pending') {
            throw new DomainException('Hanya pembayaran berstatus menunggu verifikasi yang dapat ditolak.');
        }

        $pembayaran->update([
            'status' => 'ditolak',
            'catatan' => $alasan ?: 'Bukti pembayaran tidak valid.',
        ]);
    }
}
