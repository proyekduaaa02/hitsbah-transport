@extends('layouts.super-admin')
@section('title', 'Dashboard Super Admin')

@section('content')
<div data-stagger="80" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-stat-card label="Total Produk" :count="$stat['produk']" icon="cube" tone="dark" :href="route('superadmin.products.index')" />
    <x-stat-card label="Total Kategori" :count="$stat['kategori']" icon="tag" tone="dark" :href="route('superadmin.categories.index')" />
    <x-stat-card label="Total User" :count="$stat['user']" icon="users" tone="blue" :href="route('superadmin.users.index')" />
    <x-stat-card label="Total Pesanan" :count="$stat['pesanan']" icon="list" tone="blue" :href="route('superadmin.orders.index')" />
    <x-stat-card label="Total Pendapatan" :count="$stat['pendapatan']" format="rupiah" icon="money" tone="green" :href="route('superadmin.reports.index')" class="sm:col-span-2" />
    <x-stat-card label="Pembayaran Pending" :count="$stat['pembayaran_pending']" icon="credit-card" tone="amber" :href="route('superadmin.payments.index', ['status' => 'pending'])" />
    <x-stat-card label="Pemasangan Aktif" :count="$stat['pemasangan_aktif']" icon="wrench" tone="red" :href="route('superadmin.bookings.index')" />
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-3">
    <section class="card p-5 xl:col-span-2">
        <div class="flex items-center justify-between"><div><h2 class="font-semibold text-ink-900">Grafik Penjualan</h2><p class="text-xs text-ink-500">Pendapatan dari pesanan selesai, 12 bulan terakhir</p></div><a href="{{ route('superadmin.reports.index', ['periode' => 'tahun']) }}" class="text-sm font-semibold text-brand-600 hover:underline">Laporan lengkap</a></div>
        <div class="relative mt-4 h-72"><canvas id="grafikPenjualan" aria-label="Grafik penjualan 12 bulan terakhir" role="img"></canvas></div>
    </section>

    <section class="card">
        <div class="border-b border-ink-100 px-5 py-4"><h2 class="font-semibold text-ink-900">Stok Menipis</h2></div>
        @forelse ($stokMenipis as $p)
            <a href="{{ route('superadmin.products.edit', $p) }}" class="flex items-center justify-between gap-3 border-b border-ink-100 px-5 py-3 last:border-0 hover:bg-ink-50"><div class="min-w-0"><p class="truncate text-sm font-semibold text-ink-900">{{ $p->nama }}</p><p class="text-xs text-ink-500">{{ $p->kategori->nama }}</p></div><span class="rounded-full {{ $p->stok == 0 ? 'bg-accent-soft text-accent' : 'bg-accent-soft text-accent' }} px-2.5 py-0.5 text-xs font-bold">{{ $p->stok }}</span></a>
        @empty
            <x-empty-state icon="check" title="Stok aman" text="Tidak ada produk dengan stok menipis." class="py-8!" />
        @endforelse
    </section>
</div>

<section class="card mt-6">
    <div class="flex items-center justify-between border-b border-ink-100 px-5 py-4"><h2 class="font-semibold text-ink-900">Pesanan Terbaru</h2><a href="{{ route('superadmin.orders.index') }}" class="text-sm font-semibold text-brand-600 hover:underline">Lihat semua</a></div>
    @if ($pesananTerbaru->isEmpty())<x-empty-state icon="list" title="Belum ada pesanan" />@else
    <div class="overflow-x-auto"><table class="data-table">
        <thead><tr><th>Pesanan</th><th>Pelanggan</th><th>Tanggal</th><th class="text-right">Total</th><th>Status</th><th>Pembayaran</th></tr></thead>
        <tbody>@foreach ($pesananTerbaru as $o)
            <tr><td><a href="{{ route('superadmin.orders.show', $o) }}" class="font-semibold text-brand-600 hover:underline">{{ $o->kode }}</a></td><td>{{ $o->user->nama }}</td><td class="whitespace-nowrap text-ink-600">{{ tanggal_id($o->tanggal_order) }}</td><td class="whitespace-nowrap text-right font-semibold">{{ rupiah($o->total_pembayaran) }}</td><td><x-badge :status="$o->status" type="order" /></td><td><x-badge :status="$o->status_pembayaran" type="pembayaran" /></td></tr>
        @endforeach</tbody>
    </table></div>@endif
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => window.grafikPenjualan('grafikPenjualan', @js($grafik)));
</script>
@endpush
