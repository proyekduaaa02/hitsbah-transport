@extends('layouts.super-admin')
@php $edit = $produk->exists; @endphp
@section('title', $edit ? 'Edit Produk' : 'Tambah Produk')

@section('content')
<a href="{{ route('superadmin.products.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-medium text-ink-600 hover:text-brand-600"><x-icon name="arrow-left" size="h-4 w-4" /> Kembali ke daftar produk</a>
<form method="POST" action="{{ $edit ? route('superadmin.products.update', $produk) : route('superadmin.products.store') }}" enctype="multipart/form-data" novalidate class="grid gap-6 lg:grid-cols-3"
      x-data="{ preview: @js($produk->gambar_url), pilih(e) { const f = e.target.files[0]; if (f) this.preview = URL.createObjectURL(f); } }">
    @csrf
    @if ($edit)@method('PUT')@endif
    <section class="card space-y-4 p-5 lg:col-span-2">
        <x-field name="nama" label="Nama Produk" :value="old('nama', $produk->nama)" required />
        <x-field name="id_kategori" type="select" label="Kategori" required>
            <option value="">Pilih kategori</option>
            @foreach ($kategori as $k)<option value="{{ $k->id_kategori }}" @selected(old('id_kategori', $produk->id_kategori) == $k->id_kategori)>{{ $k->nama }}</option>@endforeach
        </x-field>
        <x-field name="deskripsi" type="textarea" rows="6" label="Deskripsi" :value="old('deskripsi', $produk->deskripsi)" />
        <div class="grid gap-4 sm:grid-cols-2">
            <x-field name="harga" type="number" min="0" step="1000" label="Harga (Rp)" :value="old('harga', $edit ? (int) $produk->harga : '')" required />
            <x-field name="stok" type="number" min="0" step="1" label="Stok" :value="old('stok', $produk->stok)" required />
        </div>
    </section>
    <aside class="space-y-4">
        <section class="card p-5">
            <h2 class="font-semibold text-ink-900">Gambar Produk</h2>
            <div class="mt-3 overflow-hidden rounded-xl border border-ink-100 bg-ink-50">
                <template x-if="preview"><img :src="preview" alt="Pratinjau gambar produk" class="aspect-square w-full object-cover"></template>
                <div x-show="!preview" class="flex aspect-square w-full flex-col items-center justify-center gap-2 text-ink-500"><x-icon name="photo" size="h-10 w-10" /><span class="text-xs">Belum ada gambar</span></div>
            </div>
            <input type="file" name="gambar" accept="image/png,image/jpeg,image/webp" @change="pilih($event)" class="mt-3 block w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-ink-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-ink-700">
            <p class="mt-1 text-xs text-ink-500">JPG/PNG/WEBP, maksimal 2 MB.</p>
            @error('gambar')<p class="form-error">{{ $message }}</p>@enderror
        </section>
        <div class="flex gap-2"><a href="{{ route('superadmin.products.index') }}" class="btn btn-outline flex-1">Batal</a><button type="submit" class="btn btn-primary flex-1">{{ $edit ? 'Simpan Perubahan' : 'Simpan Produk' }}</button></div>
    </aside>
</form>
@endsection
