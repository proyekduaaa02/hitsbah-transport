@extends('layouts.app')
@section('title', 'Pesanan '.$order->kode)

@section('content')
@php
    $bayar = $order->pembayaran;
    $bisaBayar = in_array($order->status, ['pending', 'diterima']) && (! $bayar || $bayar->status === 'ditolak');
    $tahap = ['pending' => 1, 'diterima' => 2, 'diproses' => 3, 'selesai' => 4];
    $posisi = $tahap[$order->status] ?? 0;
@endphp
<div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
    @php $langkahCheckout = $baru ? 3 : (session('checkout_selesai') ? 4 : null); @endphp
    @if ($langkahCheckout)<x-checkout-steps :current="$langkahCheckout" />@endif
    <x-breadcrumb :items="['Pesanan' => route('user.orders.index'), $order->kode => null]" />

    @if ($baru)
        <div class="mb-6 rounded-2xl border border-line bg-surface-alt p-6 text-center anim-up">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-accent text-white"><x-icon name="check" size="h-7 w-7" /></span>
            <h2 class="mt-3 text-xl font-bold text-fg">Pesanan Berhasil Dibuat!</h2>
            <p class="mt-1 text-sm text-muted">Nomor pesanan Anda <strong>{{ $order->kode }}</strong>. Selesaikan pembayaran agar pesanan dapat diproses.</p>
            @if ($bisaBayar)<a href="{{ route('user.payment.show', $order->id_order) }}" class="btn btn-primary mt-4">Lanjut ke Pembayaran <x-icon name="arrow-right" size="h-4 w-4" /></a>@endif
        </div>
    @endif

    <div class="flex flex-wrap items-start justify-between gap-3">
        <div><h1 class="text-2xl font-bold text-ink-900">Pesanan {{ $order->kode }}</h1><p class="text-sm text-ink-500">Dibuat {{ tanggal_id($order->tanggal_order, true) }}</p></div>
        <div class="flex flex-wrap gap-2"><x-badge :status="$order->status" type="order" class="text-sm!" /><x-badge :status="$order->status_pembayaran" type="pembayaran" class="text-sm!" /></div>
    </div>

    {{-- Tahapan status --}}
    @if ($order->status === 'ditolak')
        <x-alert type="error" class="mt-5">Pesanan ini ditolak oleh admin. Stok produk telah dikembalikan dan jadwal pemasangan (jika ada) dibatalkan.</x-alert>
    @else
        <ol class="mt-6 grid grid-cols-4 gap-2 text-center text-xs sm:text-sm" aria-label="Tahapan pesanan">
            @foreach (['Pending', 'Diterima', 'Diproses', 'Selesai'] as $i => $nama)
                <li><div class="mx-auto flex h-9 w-9 items-center justify-center rounded-full text-sm font-bold {{ $posisi >= $i + 1 ? 'bg-accent text-white' : 'bg-surface-alt text-muted ring-1 ring-line' }}">{{ $posisi > $i + 1 || $order->status === 'selesai' ? '✓' : $i + 1 }}</div><p class="mt-1.5 font-medium {{ $posisi >= $i + 1 ? 'text-ink-900' : 'text-ink-500' }}">{{ $nama }}</p></li>
            @endforeach
        </ol>
    @endif

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            @if ($order->items->isNotEmpty())
                <section class="card"><h2 class="border-b border-ink-100 px-5 py-4 font-semibold text-ink-900">Produk</h2>
                    <ul class="divide-y divide-ink-100">@foreach ($order->items as $i)
                        <li class="flex items-center gap-4 p-4"><div class="w-16 shrink-0 overflow-hidden rounded-lg"><x-product-image :produk="$i->produk" ratio="aspect-square" /></div><div class="min-w-0 flex-1"><a href="{{ route('produk.show', $i->produk) }}" class="line-clamp-2 font-medium text-ink-900 hover:text-brand-600">{{ $i->produk->nama }}</a><p class="text-sm text-ink-500">{{ $i->jumlah }} × {{ rupiah($i->harga_satuan) }}</p></div><p class="font-semibold">{{ rupiah($i->subtotal) }}</p></li>
                    @endforeach</ul>
                </section>
            @endif

            @if ($order->bookings->isNotEmpty())
                <section class="card"><h2 class="border-b border-ink-100 px-5 py-4 font-semibold text-ink-900">Jasa Pemasangan</h2>
                    <ul class="divide-y divide-ink-100">@foreach ($order->bookings as $b)
                        <li class="p-4"><div class="flex flex-wrap items-start justify-between gap-2"><div><p class="font-semibold text-ink-900">{{ $b->service->nama }}</p><p class="mt-1 flex items-center gap-1.5 text-sm text-ink-600"><x-icon name="calendar" size="h-4 w-4" class="text-brand-600" /> {{ tanggal_id($b->jadwal_pemasangan) }}, pukul {{ $b->jadwal_pemasangan->format('H:i') }}</p><p class="mt-1 flex items-center gap-1.5 text-xs text-ink-500"><x-icon name="map-pin" size="h-4 w-4" /> Di tempat: {{ $site['alamat'] }}</p></div><div class="text-right"><x-badge :status="$b->status_pemasangan" type="pemasangan" /><p class="mt-2 font-semibold">{{ rupiah($b->harga) }}</p></div></div></li>
                    @endforeach</ul>
                </section>
            @endif
        </div>

        <div class="space-y-6">
            <section class="card p-5">
                <h2 class="font-semibold text-ink-900">Ringkasan</h2>
                <div class="mt-3 flex justify-between border-t border-ink-100 pt-3"><span class="font-semibold">Total</span><span class="text-xl font-extrabold">{{ rupiah($order->total_pembayaran) }}</span></div>
            </section>

            <section class="card p-5">
                <h2 class="font-semibold text-ink-900">Pembayaran</h2>
                @if ($bayar)
                    <dl class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between gap-3"><dt class="text-ink-500">Status</dt><dd><x-badge :status="$bayar->status" type="pembayaran" /></dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-ink-500">Metode</dt><dd class="font-medium">{{ $bayar->metode_label }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-ink-500">Dikirim</dt><dd class="font-medium">{{ tanggal_id($bayar->tanggal_bayar, true) }}</dd></div>
                    </dl>
                    @if ($bayar->bukti_url)<a href="{{ $bayar->bukti_url }}" target="_blank" rel="noopener" class="mt-3 block"><img src="{{ $bayar->bukti_url }}" alt="Bukti pembayaran" loading="lazy" class="max-h-48 w-full rounded-lg border border-ink-100 object-cover"></a>@endif
                    @if ($bayar->status === 'ditolak' && $bayar->catatan)<x-alert type="error" class="mt-3">{{ $bayar->catatan }}</x-alert>@endif
                    @if ($bayar->status === 'pending')<p class="mt-3 text-xs text-ink-500">Pembayaran Anda sedang diperiksa admin.</p>@endif
                @else
                    <p class="mt-2 text-sm text-ink-600">Pembayaran belum dilakukan.</p>
                @endif
                @if ($bisaBayar)<a href="{{ route('user.payment.show', $order->id_order) }}" class="btn btn-primary mt-4 w-full">{{ $bayar ? 'Kirim Ulang Pembayaran' : 'Bayar Sekarang' }}</a>@endif
            </section>
        </div>
    </div>
</div>
@endsection
