@extends('layouts.super-admin')
@section('title', 'Laporan Penjualan')

@section('content')
@php
    $label = [
        'hari' => tanggal_id($laporan['dari']),
        'minggu' => tanggal_id($laporan['dari']).' – '.tanggal_id($laporan['sampai']),
        'bulan' => $laporan['dari']->locale('id')->translatedFormat('F Y'),
        'tahun' => 'Tahun '.$laporan['dari']->year,
    ][$periode];
@endphp
<form method="GET" class="card mb-6 flex flex-wrap items-end gap-3 p-4">
    <div>
        <span class="form-label">Periode</span>
        <div class="inline-flex rounded-lg border border-ink-200 bg-white p-1" role="radiogroup" aria-label="Periode laporan">
            @foreach (\App\Services\SalesReportService::PERIODE as $k => $l)
                <label class="cursor-pointer"><input type="radio" name="periode" value="{{ $k }}" class="peer sr-only" @checked($periode === $k) onchange="this.form.requestSubmit()"><span class="block rounded-md px-4 py-1.5 text-sm font-semibold text-ink-600 transition peer-checked:bg-accent peer-checked:text-white">{{ $l }}</span></label>
            @endforeach
        </div>
    </div>
    <div><label for="tanggal" class="form-label">Tanggal acuan</label><input id="tanggal" type="date" name="tanggal" value="{{ $acuan->toDateString() }}" class="form-input" onchange="this.form.requestSubmit()"></div>
    <div class="ml-auto text-right"><p class="text-xs uppercase tracking-wider text-ink-500">Menampilkan</p><p class="font-bold text-ink-900">{{ $label }}</p></div>
</form>

<div data-stagger="80" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-stat-card label="Total Transaksi" :count="$laporan['total_transaksi']" icon="list" tone="dark" />
    <x-stat-card label="Total Pendapatan" :count="$laporan['total_pendapatan']" format="rupiah" icon="money" tone="green" />
    <x-stat-card label="Produk Terjual" :count="$laporan['produk_terjual']" icon="cube" tone="blue" />
    <x-stat-card label="Pembayaran Berhasil" :count="$laporan['pembayaran_berhasil']" icon="credit-card" tone="green" />
    <x-stat-card label="Transaksi Berhasil" :count="$laporan['transaksi_berhasil']" icon="check" tone="green" />
    <x-stat-card label="Transaksi Ditolak" :count="$laporan['transaksi_ditolak']" icon="x" tone="red" />
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-3">
    <section class="card p-5 xl:col-span-2">
        <h2 class="font-semibold text-ink-900">Grafik Penjualan</h2>
        <p class="text-xs text-ink-500">Pendapatan dari pesanan selesai dan jumlah transaksi</p>
        <div class="relative mt-4 h-72"><canvas id="grafikLaporan" role="img" aria-label="Grafik laporan penjualan"></canvas></div>
    </section>
    <section class="card">
        <div class="border-b border-ink-100 px-5 py-4"><h2 class="font-semibold text-ink-900">Produk Terlaris</h2></div>
        @forelse ($laporan['produk_terlaris'] as $i => $t)
            <div class="flex items-center gap-3 border-b border-ink-100 px-5 py-3 last:border-0"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $i === 0 ? 'bg-accent text-white' : 'bg-ink-100 text-ink-700' }} text-xs font-bold">{{ $i + 1 }}</span><div class="min-w-0 flex-1"><p class="truncate text-sm font-semibold text-ink-900">{{ $t->produk->nama ?? 'Produk dihapus' }}</p><p class="text-xs text-ink-500">{{ rupiah($t->total_nilai) }}</p></div><span class="text-sm font-bold">{{ $t->total_terjual }} pcs</span></div>
        @empty
            <x-empty-state icon="cube" title="Belum ada penjualan" text="Tidak ada produk terjual pada periode ini." class="py-8!" />
        @endforelse
    </section>
</div>

<section class="card mt-6 overflow-hidden">
    <h2 class="border-b border-ink-100 px-5 py-4 font-semibold text-ink-900">Daftar Transaksi ({{ $laporan['transaksi']->count() }})</h2>
    @if ($laporan['transaksi']->isEmpty())
        <x-empty-state icon="list" title="Tidak ada transaksi" text="Belum ada transaksi pada periode yang dipilih." />
    @else
        <div class="max-h-[32rem] overflow-auto"><table class="data-table">
            <thead class="sticky top-0"><tr><th>Pesanan</th><th>Pelanggan</th><th>Tanggal</th><th class="text-right">Total</th><th>Status</th></tr></thead>
            <tbody>@foreach ($laporan['transaksi'] as $o)
                <tr><td><a href="{{ route('superadmin.orders.show', $o) }}" class="font-semibold text-brand-600 hover:underline">{{ $o->kode }}</a></td><td>{{ $o->user->nama }}</td><td class="whitespace-nowrap text-ink-600">{{ tanggal_id($o->tanggal_order, true) }}</td><td class="whitespace-nowrap text-right font-semibold">{{ rupiah($o->total_pembayaran) }}</td><td><x-badge :status="$o->status" type="order" /></td></tr>
            @endforeach</tbody>
        </table></div>
    @endif
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => window.grafikPenjualan('grafikLaporan', @js($laporan['grafik'])));
</script>
@endpush
