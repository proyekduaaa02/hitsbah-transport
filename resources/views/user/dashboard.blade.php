@extends('layouts.app')
@section('title', 'Dashboard Saya')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    @include('user.partials.subnav')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-ink-900">Halo, {{ auth()->user()->nama }} 👋</h1>
        <p class="text-sm text-ink-500">Pantau pesanan, pembayaran, dan jadwal pemasangan Anda di sini.</p>
    </div>

    <div data-stagger="80" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Pesanan Aktif" :count="$pesananAktif" icon="list" tone="blue" :href="route('user.orders.index')" />
        <x-stat-card label="Pesanan Selesai" :count="$pesananSelesai" icon="check" tone="green" :href="route('user.orders.index', ['status' => 'selesai'])" />
        <x-stat-card label="Booking Pemasangan" :count="$bookingAktif" icon="calendar" tone="dark" :href="route('user.services.index')" />
        <x-stat-card label="Menunggu Pembayaran" :count="$menungguBayar" icon="credit-card" tone="amber" :href="route('user.orders.index')" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <section class="card lg:col-span-2">
            <div class="flex items-center justify-between border-b border-ink-100 px-5 py-4"><h2 class="font-semibold text-ink-900">Pesanan Terbaru</h2><a href="{{ route('user.orders.index') }}" class="text-sm font-semibold text-brand-600 hover:underline">Lihat semua</a></div>
            @if ($pesananTerbaru->isEmpty())
                <x-empty-state icon="list" title="Belum ada pesanan" text="Mulai belanja aksesoris atau booking jasa pemasangan."><a href="{{ route('produk.index') }}" class="btn btn-primary">Belanja Sekarang</a></x-empty-state>
            @else
                <div class="overflow-x-auto"><table class="data-table">
                    <thead><tr><th>Pesanan</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Pembayaran</th></tr></thead>
                    <tbody>@foreach ($pesananTerbaru as $o)
                        <tr><td><a href="{{ route('user.orders.show', $o->id_order) }}" class="font-semibold text-brand-600 hover:underline">{{ $o->kode }}</a></td><td class="whitespace-nowrap text-ink-600">{{ tanggal_id($o->tanggal_order) }}</td><td class="whitespace-nowrap font-semibold">{{ rupiah($o->total_pembayaran) }}</td><td><x-badge :status="$o->status" type="order" /></td><td><x-badge :status="$o->status_pembayaran" type="pembayaran" /></td></tr>
                    @endforeach</tbody>
                </table></div>
            @endif
        </section>

        <section class="card p-5">
            <h2 class="font-semibold text-ink-900">Jadwal Pemasangan Berikutnya</h2>
            @if ($jadwalBerikut)
                <div class="mt-4 rounded-xl border border-accent/20 bg-accent-soft p-4 text-fg">
                    <p class="text-xs font-semibold uppercase tracking-wider text-accent">{{ $jadwalBerikut->service->nama }}</p>
                    <p class="mt-1 text-xl font-bold">{{ tanggal_id($jadwalBerikut->jadwal_pemasangan) }}</p>
                    <p class="flex items-center gap-1 text-sm text-muted"><x-icon name="clock" size="h-4 w-4" /> Pukul {{ $jadwalBerikut->jadwal_pemasangan->format('H:i') }}</p>
                    <div class="mt-3"><x-badge :status="$jadwalBerikut->status_pemasangan" type="pemasangan" /></div>
                </div>
                <p class="mt-3 flex gap-2 text-xs text-ink-500"><x-icon name="map-pin" size="h-4 w-4" class="text-brand-600" /> {{ $site['alamat'] }}</p>
            @else
                <x-empty-state icon="calendar" title="Belum ada jadwal" text="Booking jasa pemasangan untuk melihat jadwal di sini."><a href="{{ route('user.services.booking') }}" class="btn btn-dark btn-sm">Booking Pemasangan</a></x-empty-state>
            @endif
        </section>
    </div>

    <section class="mt-10">
        <h2 class="text-lg font-bold text-ink-900">Produk Terbaru</h2>
        <div data-stagger="80" class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach ($produkTerbaru as $p)<x-product-card :produk="$p" />@endforeach</div>
    </section>
</div>
@endsection
