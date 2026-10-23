@extends('layouts.app')
@section('title', 'Jadwal Pemasangan')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    @include('user.partials.subnav')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div><h1 class="text-2xl font-bold text-ink-900">Jadwal Pemasangan</h1><p class="text-sm text-ink-500">Pemasangan dilakukan di tempat: {{ $site['alamat'] }}</p></div>
        <a href="{{ route('user.services.booking') }}" class="btn btn-primary"><x-icon name="plus" size="h-4 w-4" /> Booking Baru</a>
    </div>

    @if ($bookings->isEmpty())
        <div class="card mt-6"><x-empty-state icon="calendar" title="Belum ada jadwal pemasangan" text="Booking jasa pemasangan untuk aksesoris mobil Anda."><a href="{{ route('user.services.booking') }}" class="btn btn-dark">Booking Pemasangan</a></x-empty-state></div>
    @else
        <div data-stagger="80" class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($bookings as $b)
                <article class="card overflow-hidden">
                    <div class="border-b border-line bg-surface-alt px-5 py-4"><p class="text-xs font-semibold uppercase tracking-wider text-accent">{{ tanggal_id($b->jadwal_pemasangan) }}</p><p class="text-2xl font-bold text-fg">{{ $b->jadwal_pemasangan->format('H:i') }} WIB</p></div>
                    <div class="space-y-3 p-5">
                        <p class="font-semibold text-ink-900">{{ $b->service->nama }}</p>
                        <div class="flex flex-wrap gap-2"><x-badge :status="$b->status_pemasangan" type="pemasangan" /><x-badge :status="$b->order->status_pembayaran" type="pembayaran" /></div>
                        <p class="text-xs text-ink-500">Durasi ± {{ $b->service->durasi_label }} · Pesanan {{ $b->order->kode }}</p>
                        <div class="flex gap-2 pt-1"><a href="{{ route('user.orders.show', $b->id_order) }}" class="btn btn-outline btn-sm flex-1">Detail Pesanan</a>@if (! $b->order->pembayaran && in_array($b->order->status, ['pending', 'diterima']))<a href="{{ route('user.payment.show', $b->id_order) }}" class="btn btn-primary btn-sm flex-1">Bayar</a>@endif</div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection
