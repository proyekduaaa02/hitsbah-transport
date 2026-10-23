<?php

namespace App\Support;

/**
 * Label dan "tone" warna status yang konsisten di seluruh aplikasi.
 * Tone dipetakan menjadi kelas Tailwind di components/badge.blade.php.
 */
class Status
{
    private const LABELS = [
        'order' => [
            'pending' => 'Pending',
            'diterima' => 'Diterima',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ],
        'pembayaran' => [
            'belum' => 'Menunggu Pembayaran',
            'pending' => 'Menunggu Verifikasi',
            'diverifikasi' => 'Pembayaran Diverifikasi',
            'berhasil' => 'Pembayaran Berhasil',
            'ditolak' => 'Pembayaran Ditolak',
        ],
        'pemasangan' => [
            'menunggu' => 'Menunggu',
            'dijadwalkan' => 'Pemasangan Dijadwalkan',
            'diproses' => 'Sedang Dipasang',
            'selesai' => 'Pemasangan Selesai',
            'dibatalkan' => 'Dibatalkan',
        ],
    ];

    private const TONES = [
        'pending' => 'amber',
        'menunggu' => 'amber',
        'belum' => 'gray',
        'diterima' => 'blue',
        'diproses' => 'blue',
        'dijadwalkan' => 'indigo',
        'diverifikasi' => 'teal',
        'selesai' => 'green',
        'berhasil' => 'green',
        'ditolak' => 'red',
        'dibatalkan' => 'red',
    ];

    public static function label(string $tipe, ?string $status): string
    {
        $status = $status ?? 'belum';

        return self::LABELS[$tipe][$status] ?? ucfirst($status);
    }

    public static function tone(?string $status): string
    {
        return self::TONES[$status ?? 'belum'] ?? 'gray';
    }

    public static function options(string $tipe): array
    {
        return self::LABELS[$tipe] ?? [];
    }
}
