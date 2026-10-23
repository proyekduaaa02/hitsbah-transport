@extends('layouts.app')

@section('title', $produk->nama)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($produk->deskripsi ?? $produk->nama), 155))

@section('content')
@php
    $bisaBeli = ! auth()->check() || auth()->user()->isUser();
    $rata = round((float) $produk->reviews_avg_rating, 1);
@endphp
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <x-breadcrumb :items="['Produk' => route('produk.index'), $produk->kategori->nama => route('kategori.show', $produk->kategori), $produk->nama => null]" />

    <div class="grid gap-8 lg:grid-cols-2">
        <div class="card anim-left overflow-hidden"><x-product-image :produk="$produk" ratio="aspect-square" /></div>

        <div>
            <a href="{{ route('kategori.show', $produk->kategori) }}" class="text-xs font-semibold uppercase tracking-wider text-brand-600 hover:underline">{{ $produk->kategori->nama }}</a>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-ink-900">{{ $produk->nama }}</h1>
            <x-rating-stars class="mt-3" :rating="$rata" :jumlah="$produk->reviews_count" />
            <p class="mt-5 text-3xl font-extrabold text-ink-900">{{ rupiah($produk->harga) }}</p>

            <div class="mt-3">
                @if ($produk->stok > 0)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-surface-alt px-3 py-1 text-xs font-semibold text-fg ring-1 ring-inset ring-line"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Stok tersedia: {{ $produk->stok }}</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-accent-soft px-3 py-1 text-xs font-semibold text-accent ring-1 ring-inset ring-accent/20">Stok habis</span>
                @endif
            </div>

            <div class="mt-6 text-sm leading-relaxed text-ink-600">{!! nl2br(e($produk->deskripsi ?? 'Belum ada deskripsi untuk produk ini.')) !!}</div>

            @if ($bisaBeli)
                <div class="mt-8 rounded-xl border border-ink-100 bg-white p-5">
                    @auth
                        <form method="POST" action="{{ route('user.cart.add') }}" x-data="{ jumlah: 1, maks: {{ max(1, $produk->stok) }} }" data-cart-form class="flex flex-wrap items-end gap-4">
                            @csrf
                            <input type="hidden" name="id_produk" value="{{ $produk->id_produk }}">
                            <div>
                                <span class="form-label">Jumlah</span>
                                <div class="inline-flex items-center rounded-lg border border-ink-200 bg-white">
                                    <button type="button" class="px-3 py-2.5 text-ink-700 hover:bg-ink-50" @click="jumlah = Math.max(1, jumlah - 1)" aria-label="Kurangi"><x-icon name="minus" size="h-4 w-4" /></button>
                                    <input type="number" name="jumlah" x-model.number="jumlah" min="1" :max="maks" class="w-14 border-0 p-0 text-center text-sm font-semibold focus:ring-0" aria-label="Jumlah">
                                    <button type="button" class="px-3 py-2.5 text-ink-700 hover:bg-ink-50" @click="jumlah = Math.min(maks, jumlah + 1)" aria-label="Tambah"><x-icon name="plus" size="h-4 w-4" /></button>
                                </div>
                            </div>
                            <button type="submit" data-cart-btn class="btn btn-primary px-6 py-3" @disabled($produk->stok < 1)><span class="cart-default inline-flex items-center gap-2"><x-icon name="cart" size="h-5 w-5" /> Tambah ke Keranjang</span><span class="cart-added"><x-icon name="check" size="h-5 w-5" /> Ditambahkan</span></button>
                        </form>
                        @error('jumlah')<p class="form-error">{{ $message }}</p>@enderror
                    @else
                        <p class="text-sm text-ink-600">Silakan masuk untuk menambahkan produk ke keranjang.</p>
                        <div class="mt-3 flex gap-2"><a href="{{ route('login') }}" class="btn btn-primary">Masuk</a><a href="{{ route('register') }}" class="btn btn-outline">Daftar</a></div>
                    @endauth
                </div>
            @endif
        </div>
    </div>

    {{-- Ulasan --}}
    <section class="mt-14" aria-labelledby="judul-ulasan">
        <h2 id="judul-ulasan" class="text-xl font-bold text-ink-900">Ulasan Pelanggan <span class="text-ink-500">({{ $produk->reviews_count }})</span></h2>
        @if ($ulasan->isEmpty())
            <div class="card mt-4"><x-empty-state icon="chat" title="Belum ada ulasan" text="Jadilah yang pertama memberikan ulasan setelah pesanan Anda selesai." /></div>
        @else
            <div data-stagger="80" class="mt-4 space-y-4">
                @foreach ($ulasan as $u)
                    <article class="card p-5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-full bg-ink-900 text-sm font-bold uppercase text-white">{{ mb_substr($u->user->nama, 0, 1) }}</span><div><p class="text-sm font-semibold text-ink-900">{{ $u->user->nama }}</p><p class="text-xs text-ink-500">{{ tanggal_id($u->created_at) }}</p></div></div>
                            <x-rating-stars :rating="$u->rating" />
                        </div>
                        <p class="mt-3 text-sm text-ink-700">{{ $u->komen }}</p>
                        @if ($u->balasan_admin)
                            <div class="mt-3 rounded-lg border-l-4 border-brand-600 bg-ink-50 p-3 text-sm"><p class="text-xs font-semibold text-brand-700">Balasan {{ $site['nama_usaha'] }}</p><p class="mt-1 text-ink-700">{{ $u->balasan_admin }}</p></div>
                        @endif
                    </article>
                @endforeach
            </div>
            <x-pagination :paginator="$ulasan" />
        @endif
    </section>

    @if ($terkait->isNotEmpty())
        <section class="mt-14">
            <h2 class="text-xl font-bold text-ink-900">Produk Terkait</h2>
            <div data-stagger="80" class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach ($terkait as $t)<x-product-card :produk="$t" />@endforeach</div>
        </section>
    @endif
</div>
@endsection
