@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <x-checkout-steps :current="2" />
    <x-breadcrumb :items="['Keranjang' => route('user.cart.index'), 'Checkout' => null]" />
    <h1 class="text-2xl font-bold text-ink-900">Checkout</h1>

    @error('keranjang')<x-alert type="error" class="mt-4">{{ $message }}</x-alert>@enderror

    <form method="POST" action="{{ route('user.checkout.store') }}" novalidate
          x-data="jadwalPicker({ url: @js(route('user.services.slots')), idServices: @js(old('id_services')), tanggal: @js(old('tanggal', '')), jam: @js(old('jam', '')), harga: @js($jasa->pluck('harga', 'id_services')) })"
          class="mt-6 grid gap-6 lg:grid-cols-[1fr_22rem]">
        @csrf
        <div class="space-y-6">
            <section class="card">
                <h2 class="border-b border-ink-100 px-5 py-4 font-semibold text-ink-900">Produk yang Dipesan</h2>
                <ul class="divide-y divide-ink-100">
                    @foreach ($cart->items as $item)
                        <li class="flex items-center gap-4 p-4">
                            <div class="w-16 shrink-0 overflow-hidden rounded-lg"><x-product-image :produk="$item->produk" ratio="aspect-square" /></div>
                            <div class="min-w-0 flex-1"><p class="truncate font-medium text-ink-900">{{ $item->produk->nama }}</p><p class="text-sm text-ink-500">{{ $item->jumlah }} × {{ rupiah($item->produk->harga) }}</p></div>
                            <p class="font-semibold">{{ rupiah($item->subtotal) }}</p>
                        </li>
                    @endforeach
                </ul>
            </section>

            <section class="card p-5">
                <h2 class="font-semibold text-ink-900">Tambah Jasa Pemasangan <span class="text-sm font-normal text-ink-500">(opsional)</span></h2>
                <p class="mb-4 mt-1 text-sm text-ink-500">Pasangkan produk Anda langsung di bengkel kami. Pilih jasa dan jadwal yang tersedia.</p>
                @include('user.partials.schedule-picker', ['jasa' => $jasa, 'opsional' => true])
            </section>

            <section class="card p-5 text-sm text-ink-600">
                <h2 class="font-semibold text-ink-900">Alamat Akun</h2>
                <p class="mt-2">{{ auth()->user()->nama }} · {{ auth()->user()->alamat }}</p>
                <p class="mt-2 text-xs text-ink-500">Produk diambil / dipasang di {{ $site['alamat'] }}.</p>
            </section>
        </div>

        <aside class="card h-fit p-5 lg:sticky lg:top-24">
            <h2 class="font-semibold text-ink-900">Ringkasan Pesanan</h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-ink-600">Subtotal produk</dt><dd class="font-medium">{{ rupiah($cart->totalHarga()) }}</dd></div>
                <div class="flex justify-between" x-show="hargaJasa > 0" x-cloak><dt class="text-ink-600">Jasa pemasangan</dt><dd class="font-medium" x-text="rupiah(hargaJasa)"></dd></div>
                <div class="flex justify-between border-t border-ink-100 pt-3 text-base"><dt class="font-semibold">Total</dt><dd class="font-extrabold text-ink-900" x-text="rupiah({{ $cart->totalHarga() }} + hargaJasa)">{{ rupiah($cart->totalHarga()) }}</dd></div>
            </dl>
            <button type="submit" class="btn btn-primary mt-5 w-full py-3">Buat Pesanan</button>
            <p class="mt-3 text-xs text-ink-500">Harga final dihitung ulang oleh sistem saat pesanan dibuat. Pembayaran dilakukan setelah ini.</p>
        </aside>
    </form>
</div>
@endsection
