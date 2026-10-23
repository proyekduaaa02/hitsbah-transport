@extends('layouts.app')

@section('title', $kategoriAktif ? 'Kategori '.$kategoriAktif->nama : 'Katalog Produk')
@section('meta_description', 'Katalog aksesoris mobil '.($kategoriAktif ? 'kategori '.$kategoriAktif->nama.' ' : '').'di '.$site['site_name'].'. Cari, filter, dan urutkan sesuai kebutuhan Anda.')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <x-breadcrumb :items="$kategoriAktif ? ['Produk' => route('produk.index'), $kategoriAktif->nama => null] : ['Produk' => null]" />

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="section-title">{{ $kategoriAktif ? $kategoriAktif->nama : 'Katalog Produk' }}</h1>
            <p class="mt-1 text-sm text-ink-500">{{ $kategoriAktif?->deskripsi ?? 'Temukan aksesoris mobil terbaik untuk kendaraan Anda.' }}</p>
        </div>
        <p class="text-sm text-ink-500">{{ $produk->total() }} produk ditemukan</p>
    </div>

    <form method="GET" class="card mt-6 grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_14rem_12rem_auto]">
        <div class="relative">
            <x-icon name="search" size="h-5 w-5" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-500" />
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama produk..." class="form-input pl-10" aria-label="Cari produk">
        </div>
        @if ($kategoriAktif)
            <a href="{{ route('produk.index') }}" class="form-input flex items-center justify-between text-ink-600 hover:bg-ink-50">{{ $kategoriAktif->nama }} <x-icon name="x" size="h-4 w-4" /></a>
        @else
            <select name="kategori" class="form-input" aria-label="Filter kategori">
                <option value="">Semua kategori</option>
                @foreach ($daftarKategori as $k)
                    <option value="{{ $k->id_kategori }}" @selected(request('kategori') == $k->id_kategori)>{{ $k->nama }}</option>
                @endforeach
            </select>
        @endif
        <select name="urut" class="form-input" aria-label="Urutkan">
            <option value="">Terbaru</option>
            <option value="harga_asc" @selected(request('urut') === 'harga_asc')>Harga terendah</option>
            <option value="harga_desc" @selected(request('urut') === 'harga_desc')>Harga tertinggi</option>
        </select>
        <button type="submit" class="btn btn-dark"><x-icon name="filter" size="h-4 w-4" /> Terapkan</button>
    </form>

    @if ($produk->isEmpty())
        <div class="card mt-6">
            <x-empty-state icon="search" title="Produk tidak ditemukan" text="Coba ubah kata kunci atau filter pencarian Anda.">
                <a href="{{ route('produk.index') }}" class="btn btn-outline">Reset Pencarian</a>
            </x-empty-state>
        </div>
    @else
        <div data-stagger="70" class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($produk as $item)
                <x-product-card :produk="$item" />
            @endforeach
        </div>
        <x-pagination :paginator="$produk" />
    @endif
</div>
@endsection
