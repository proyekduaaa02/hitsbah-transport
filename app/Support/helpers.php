<?php

if (! function_exists('rupiah')) {
    /**
     * Format angka menjadi mata uang Rupiah, contoh: Rp 1.250.000
     */
    function rupiah(float|int|string|null $nilai): string
    {
        return 'Rp '.number_format((float) $nilai, 0, ',', '.');
    }
}

if (! function_exists('tanggal_id')) {
    /**
     * Format tanggal Indonesia, contoh: 7 Okt 2026
     */
    function tanggal_id(\DateTimeInterface|string|null $tanggal, bool $denganJam = false): string
    {
        if (! $tanggal) {
            return '-';
        }

        $format = $denganJam ? 'd M Y, H:i' : 'd M Y';

        return \Illuminate\Support\Carbon::parse($tanggal)->locale('id')->translatedFormat($format);
    }
}
