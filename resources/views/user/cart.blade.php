@extends('layouts.app')
@section('title', 'Keranjang Belanja')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    @include('user.partials.subnav')
    <x-checkout-steps :current="1" />
    <h1 class="text-2xl font-bold text-ink-900">Keranjang Belanja</h1>

    @if ($cart->items->isEmpty())
        <div class="card mt-6"><x-empty-state icon="cart" title="Keranjang masih kosong" text="Yuk, pilih aksesoris mobil favorit Anda."><a href="{{ route('produk.index') }}" class="btn btn-primary">Lihat Produk</a></x-empty-state></div>
    @else
        <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_22rem]">
            <div data-stagger="70" class="space-y-3">
                @foreach ($cart->items as $item)
                    @php $p = $item->produk; @endphp
                    <div class="card flex gap-4 p-4">
                        <a href="{{ route('produk.show', $p) }}" class="w-24 shrink-0 overflow-hidden rounded-lg sm:w-28"><x-product-image :produk="$p" ratio="aspect-square" /></a>
                        <div class="flex min-w-0 flex-1 flex-col">
                            <a href="{{ route('produk.show', $p) }}" class="line-clamp-2 font-semibold text-ink-900 hover:text-brand-600">{{ $p->nama }}</a>
                            <p class="text-sm text-ink-500">{{ rupiah($p->harga) }} · stok {{ $p->stok }}</p>
                            @if ($item->jumlah > $p->stok)<p class="mt-1 text-xs font-semibold text-accent">Jumlah melebihi stok tersedia.</p>@endif
                            <div class="mt-auto flex flex-wrap items-end justify-between gap-3 pt-3">
                                <form method="POST" action="{{ route('user.cart.update', $item->id_cart_item) }}" class="flex items-center gap-2">
                                    @csrf @method('PATCH')
                                    <label for="jumlah-{{ $item->id_cart_item }}" class="sr-only">Jumlah</label>
                                    <input id="jumlah-{{ $item->id_cart_item }}" type="number" name="jumlah" value="{{ $item->jumlah }}" min="1" max="{{ max(1, $p->stok) }}" class="form-input w-20 py-1.5 text-center" onchange="this.form.requestSubmit()">
                                    <button type="submit" class="btn btn-outline btn-sm" data-no-loading>Ubah</button>
                                </form>
                                <div class="flex items-center gap-3">
                                    <p class="font-bold text-ink-900">{{ rupiah($item->subtotal) }}</p>
                                    <form method="POST" action="{{ route('user.cart.remove', $item->id_cart_item) }}" data-confirm="Hapus {{ $p->nama }} dari keranjang?">@csrf @method('DELETE')
                                        <button type="submit" class="rounded-lg p-2 text-ink-500 hover:bg-accent-soft hover:text-accent" aria-label="Hapus {{ $p->nama }}" data-no-loading><x-icon name="trash" size="h-5 w-5" /></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <aside class="card h-fit p-5 lg:sticky lg:top-24">
                <h2 class="font-semibold text-ink-900">Ringkasan Belanja</h2>
                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-ink-600">Jumlah item</dt><dd class="font-medium">{{ $cart->items->sum('jumlah') }}</dd></div>
                    <div class="flex justify-between border-t border-ink-100 pt-3 text-base"><dt class="font-semibold">Total</dt><dd class="font-extrabold text-ink-900">{{ rupiah($cart->totalHarga()) }}</dd></div>
                </dl>
                <a href="{{ route('user.checkout.show') }}" class="btn btn-primary mt-5 w-full py-3">Checkout <x-icon name="arrow-right" size="h-4 w-4" /></a>
                <a href="{{ route('produk.index') }}" class="btn btn-ghost mt-2 w-full">Lanjut Belanja</a>
            </aside>
        </div>
    @endif
</div>
@endsection
