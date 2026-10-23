@extends('layouts.super-admin')
@section('title', 'Produk')

@section('content')
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-ink-500">{{ $products->total() }} produk terdaftar</p>
    <a href="{{ route('superadmin.products.create') }}" class="btn btn-primary"><x-icon name="plus" size="h-4 w-4" /> Tambah Produk</a>
</div>
<form method="GET" class="card mb-5 grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_13rem_11rem_11rem_auto]">
    <div class="relative"><x-icon name="search" size="h-5 w-5" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-500" /><input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama produk..." class="form-input pl-10" aria-label="Cari produk"></div>
    <select name="kategori" class="form-input" aria-label="Kategori"><option value="">Semua kategori</option>@foreach ($kategori as $k)<option value="{{ $k->id_kategori }}" @selected(request('kategori') == $k->id_kategori)>{{ $k->nama }}</option>@endforeach</select>
    <select name="stok" class="form-input" aria-label="Stok"><option value="">Semua stok</option><option value="menipis" @selected(request('stok') === 'menipis')>Stok menipis (≤5)</option></select>
    <select name="urut" class="form-input" aria-label="Urutkan"><option value="">Terbaru</option><option value="harga_asc" @selected(request('urut') === 'harga_asc')>Harga terendah</option><option value="harga_desc" @selected(request('urut') === 'harga_desc')>Harga tertinggi</option></select>
    <div class="flex gap-2"><button type="submit" class="btn btn-dark flex-1">Filter</button><a href="{{ route('superadmin.products.index') }}" class="btn btn-outline">Reset</a></div>
</form>

<div class="card overflow-hidden">
    @if ($products->isEmpty())
        <x-empty-state icon="cube" title="Produk tidak ditemukan" text="Tambahkan produk baru atau ubah filter."><a href="{{ route('superadmin.products.create') }}" class="btn btn-primary">Tambah Produk</a></x-empty-state>
    @else
        <div class="overflow-x-auto"><table class="data-table">
            <thead><tr><th>Produk</th><th>Kategori</th><th class="text-right">Harga</th><th class="text-center">Stok</th><th></th></tr></thead>
            <tbody>@foreach ($products as $p)
                <tr>
                    <td><div class="flex items-center gap-3"><div class="w-12 shrink-0 overflow-hidden rounded-lg"><x-product-image :produk="$p" ratio="aspect-square" /></div><span class="max-w-xs font-semibold text-ink-900">{{ $p->nama }}</span></div></td>
                    <td>{{ $p->kategori->nama }}</td>
                    <td class="whitespace-nowrap text-right font-semibold">{{ rupiah($p->harga) }}</td>
                    <td class="text-center"><span class="rounded-full px-2.5 py-0.5 text-xs font-bold {{ $p->stok == 0 ? 'bg-accent-soft text-accent' : ($p->stok <= 5 ? 'bg-accent-soft text-accent' : 'bg-ink-100 text-ink-700') }}">{{ $p->stok }}</span></td>
                    <td class="whitespace-nowrap text-right">
                        <a href="{{ route('superadmin.products.edit', $p) }}" class="btn btn-outline btn-sm"><x-icon name="pencil" size="h-4 w-4" /> Edit</a>
                        <form method="POST" action="{{ route('superadmin.products.destroy', $p) }}" class="inline" data-confirm="Hapus produk {{ $p->nama }}?">@csrf @method('DELETE')<button type="submit" class="btn btn-outline btn-sm text-accent! hover:bg-accent-soft!" data-no-loading aria-label="Hapus {{ $p->nama }}"><x-icon name="trash" size="h-4 w-4" /></button></form>
                    </td>
                </tr>
            @endforeach</tbody>
        </table></div>
    @endif
</div>
<x-pagination :paginator="$products" />
@endsection
