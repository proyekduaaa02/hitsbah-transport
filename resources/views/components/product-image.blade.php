@props(['produk', 'ratio' => 'aspect-[4/3]'])
@php
    $slug = $produk->relationLoaded('kategori') && $produk->kategori ? \Illuminate\Support\Str::slug($produk->kategori->nama) : 'default';
    $berkas = file_exists(public_path("images/placeholders/{$slug}.svg")) ? $slug : 'default';
    $adaGambar = (bool) $produk->gambar_url;
@endphp
<div {{ $attributes->class(['img-zoom relative w-full overflow-hidden bg-surface-alt', $ratio, 'skeleton' => true]) }}>
    @if ($adaGambar)
        <img src="{{ $produk->gambar_url }}" alt="{{ $produk->nama }}" loading="lazy" decoding="async" class="h-full w-full object-cover">
    @else
        <img src="{{ asset("images/placeholders/{$berkas}.svg") }}" alt="Ilustrasi {{ $produk->nama }} (foto produk belum tersedia)" loading="lazy" decoding="async" class="h-full w-full object-cover">
    @endif
    {{ $slot }}
</div>
