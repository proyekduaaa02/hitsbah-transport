@extends('layouts.admin')
@section('title', 'Dashboard Admin')

@section('content')
<div data-stagger="80" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
    <x-stat-card label="Total Pesanan" :count="$stat['total']" icon="list" tone="dark" :href="route('admin.orders.index')" />
    <x-stat-card label="Pesanan Baru" :count="$stat['baru']" icon="bolt" tone="red" :href="route('admin.orders.index', ['status' => 'pending'])" />
    <x-stat-card label="Pesanan Diproses" :count="$stat['diproses']" icon="clock" tone="blue" :href="route('admin.orders.index', ['status' => 'diproses'])" />
    <x-stat-card label="Pesanan Selesai" :count="$stat['selesai']" icon="check" tone="green" :href="route('admin.orders.index', ['status' => 'selesai'])" />
    <x-stat-card label="Pembayaran Menunggu Verifikasi" :count="$stat['pembayaran_pending']" icon="credit-card" tone="amber" :href="route('admin.payments.index', ['status' => 'pending'])" />
    <x-stat-card label="Pemasangan Terjadwal" :count="$stat['terjadwal']" icon="calendar" tone="dark" :href="route('admin.bookings.index', ['status' => 'dijadwalkan'])" />
</div>

<div class="mt-6 grid gap-6 xl:grid-cols-3">
    <section class="card xl:col-span-2">
        <div class="flex items-center justify-between border-b border-ink-100 px-5 py-4"><h2 class="font-semibold text-ink-900">Pesanan Terbaru</h2><a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-brand-600 hover:underline">Lihat semua</a></div>
        @if ($pesananTerbaru->isEmpty())<x-empty-state icon="list" title="Belum ada pesanan" />@else
        <div class="overflow-x-auto"><table class="data-table">
            <thead><tr><th>Pesanan</th><th>Pelanggan</th><th>Total</th><th>Status</th><th>Pembayaran</th></tr></thead>
            <tbody>@foreach ($pesananTerbaru as $o)
                <tr><td><a href="{{ route('admin.orders.show', $o) }}" class="font-semibold text-brand-600 hover:underline">{{ $o->kode }}</a></td><td>{{ $o->user->nama }}</td><td class="whitespace-nowrap font-semibold">{{ rupiah($o->total_pembayaran) }}</td><td><x-badge :status="$o->status" type="order" /></td><td><x-badge :status="$o->status_pembayaran" type="pembayaran" /></td></tr>
            @endforeach</tbody>
        </table></div>@endif
    </section>

    <div class="space-y-6">
        <section class="card">
            <div class="border-b border-ink-100 px-5 py-4"><h2 class="font-semibold text-ink-900">Perlu Verifikasi Pembayaran</h2></div>
            @forelse ($pembayaranMenunggu as $p)
                <a href="{{ route('admin.payments.show', $p) }}" class="flex items-center justify-between gap-3 border-b border-ink-100 px-5 py-3 last:border-0 hover:bg-ink-50"><div class="min-w-0"><p class="truncate text-sm font-semibold text-ink-900">{{ $p->order->user->nama }}</p><p class="text-xs text-ink-500">{{ $p->order->kode }} · {{ $p->metode_label }}</p></div><p class="shrink-0 text-sm font-bold">{{ rupiah($p->total_pembayaran) }}</p></a>
            @empty
                <x-empty-state icon="check" title="Semua terverifikasi" class="py-8!" />
            @endforelse
        </section>
        <section class="card">
            <div class="border-b border-ink-100 px-5 py-4"><h2 class="font-semibold text-ink-900">Jadwal Pemasangan Terdekat</h2></div>
            @forelse ($jadwalTerdekat as $b)
                <div class="flex items-center justify-between gap-3 border-b border-ink-100 px-5 py-3 last:border-0"><div class="min-w-0"><p class="truncate text-sm font-semibold text-ink-900">{{ $b->service->nama }}</p><p class="text-xs text-ink-500">{{ $b->user->nama }} · {{ tanggal_id($b->jadwal_pemasangan) }} {{ $b->jadwal_pemasangan->format('H:i') }}</p></div><x-badge :status="$b->status_pemasangan" type="pemasangan" /></div>
            @empty
                <x-empty-state icon="calendar" title="Tidak ada jadwal" class="py-8!" />
            @endforelse
        </section>
    </div>
</div>
@endsection
