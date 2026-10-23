@extends('layouts.app')

@section('title', 'Aksesoris Mobil Berkualitas & Jasa Pemasangan Profesional')
@section('meta_description', 'Beli aksesoris mobil berkualitas dan pesan jasa pemasangan profesional langsung di bengkel HITSBAH Transport, Lelea, Indramayu.')

@section('content')
{{-- ============ HERO ============ --}}
<section class="relative overflow-hidden bg-white">
    <div class="pointer-events-none absolute -right-40 -top-40 h-[34rem] w-[34rem] rounded-full bg-accent-soft" aria-hidden="true"></div>
    <div class="pointer-events-none absolute inset-0 opacity-[0.5]" aria-hidden="true" style="background-image: radial-gradient(var(--border) 1px, transparent 1px); background-size: 28px 28px; mask-image: linear-gradient(to bottom, #000, transparent 85%);"></div>

    <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 py-14 sm:px-6 lg:grid-cols-[1.05fr_1fr] lg:px-8 lg:py-24">
        <div>
            <span class="anim-up eyebrow inline-flex items-center gap-2 rounded-full border border-line bg-white px-3.5 py-1.5 shadow-sm" style="--d: 0ms"><span class="h-1.5 w-1.5 rounded-full bg-accent pulse-dot"></span> Toko &amp; Bengkel Aksesoris Mobil</span>
            <h1 class="h1 anim-up mt-5 text-fg" style="--d: 80ms">Aksesoris Mobil Berkualitas &amp; <span class="text-accent">Jasa Pemasangan</span> Profesional</h1>
            <p class="anim-up mt-5 max-w-xl text-base leading-relaxed text-muted sm:text-lg" style="--d: 180ms">Beli aksesoris mobil dan pesan jasa pemasangan langsung dari website. Pilih produk, tentukan jadwal pemasangan di bengkel kami, bayar secara digital, lalu pantau statusnya kapan saja.</p>
            <div class="anim-up mt-8 flex flex-wrap gap-3" style="--d: 280ms">
                <a href="{{ route('produk.index') }}" class="btn btn-primary px-6 py-3 text-base">Lihat Produk <x-icon name="arrow-right" size="h-5 w-5" /></a>
                <a href="{{ auth()->check() && auth()->user()->isUser() ? route('user.services.booking') : route('jasa.index') }}" class="btn btn-outline px-6 py-3 text-base"><x-icon name="wrench" size="h-5 w-5" /> Pesan Jasa</a>
            </div>
            <dl class="anim-up mt-12 grid max-w-md grid-cols-3 gap-6 border-t border-line pt-8" style="--d: 380ms">
                <div><dt class="caption uppercase tracking-wider">Produk</dt><dd class="mt-1 text-2xl font-bold text-fg">{{ $totalProduk }}+</dd></div>
                <div><dt class="caption uppercase tracking-wider">Layanan</dt><dd class="mt-1 text-2xl font-bold text-fg">{{ $totalJasa }}</dd></div>
                <div><dt class="caption uppercase tracking-wider">Pemasangan</dt><dd class="mt-1 text-2xl font-bold text-fg">Di Tempat</dd></div>
            </dl>
        </div>

        <div class="anim-right relative" style="--d: 200ms">
            <x-hero-car class="relative z-10 w-full drop-shadow-sm" />
            <div class="float-slow absolute -top-2 left-0 z-20 hidden items-center gap-3 rounded-2xl border border-line bg-white px-4 py-3 shadow-lg sm:flex">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-accent-soft text-accent"><x-icon name="wrench" size="h-5 w-5" /></span>
                <span class="text-sm"><span class="block font-semibold text-fg">Pemasangan di Tempat</span><span class="caption">Teknisi berpengalaman</span></span>
            </div>
            <div class="float-slower absolute -bottom-2 right-0 z-20 hidden items-center gap-3 rounded-2xl border border-line bg-white px-4 py-3 shadow-lg sm:flex">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-accent-soft text-accent"><x-icon name="calendar" size="h-5 w-5" /></span>
                <span class="text-sm"><span class="block font-semibold text-fg">Booking Jadwal Online</span><span class="caption">Pilih jam yang tersedia</span></span>
            </div>
        </div>
    </div>
</section>

{{-- ============ TENTANG ============ --}}
<section id="tentang" class="scroll-mt-20 border-y border-line bg-surface-alt">
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8">
        <div data-reveal="left">
            <p class="eyebrow">Tentang Kami</p>
            <h2 class="h2 mt-2">{{ $site['nama_usaha'] }}</h2>
            <p class="mt-4 leading-relaxed text-muted">{{ $site['deskripsi'] }}</p>
            <p class="mt-3 leading-relaxed text-muted">Dengan website ini, pelanggan tidak perlu datang hanya untuk bertanya stok atau memesan lewat chat. Semuanya tercatat rapi: katalog, pesanan, pembayaran, hingga jadwal pemasangan.</p>
            <div class="mt-6 flex items-start gap-3 rounded-2xl border border-line bg-white p-4">
                <x-icon name="map-pin" class="mt-0.5 text-accent" size="h-6 w-6" />
                <div><p class="text-sm font-semibold text-fg">Lokasi Bengkel &amp; Toko</p><p class="text-sm text-muted">{{ $site['alamat'] }}</p><p class="caption mt-1">Layanan pemasangan hanya tersedia di tempat.</p></div>
            </div>
        </div>
        <div data-stagger="90" class="grid grid-cols-2 gap-4">
            @foreach ([['Katalog Lengkap', 'Interior, eksterior, audio, lampu, hingga keamanan.', 'cube'], ['Pemasangan Profesional', 'Teknisi berpengalaman, hasil rapi.', 'wrench'], ['Stok Transparan', 'Stok produk selalu diperbarui.', 'chart'], ['Riwayat Tercatat', 'Semua transaksi tersimpan rapi.', 'list']] as [$j, $t, $ik])
                <div class="card p-5 transition duration-300 hover:-translate-y-1 hover:shadow-lg"><x-icon :name="$ik" class="text-accent" size="h-7 w-7" /><p class="mt-3 font-semibold text-fg">{{ $j }}</p><p class="mt-1 text-sm text-muted">{{ $t }}</p></div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ PRODUK UNGGULAN ============ --}}
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div data-reveal class="flex flex-wrap items-end justify-between gap-4">
        <div><p class="eyebrow">Pilihan Terbaik</p><h2 class="h2 mt-2">Produk Unggulan</h2></div>
        <a href="{{ route('produk.index') }}" class="btn btn-outline">Semua Produk <x-icon name="arrow-right" size="h-4 w-4" /></a>
    </div>
    @if ($produkUnggulan->isEmpty())
        <x-empty-state class="mt-8" icon="cube" title="Produk belum tersedia" text="Katalog produk akan segera ditampilkan." />
    @else
        <div data-stagger="80" class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($produkUnggulan as $produk)<x-product-card :produk="$produk" />@endforeach
        </div>
    @endif
</section>

{{-- ============ KATEGORI ============ --}}
<section class="border-y border-line bg-surface-alt">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div data-reveal><p class="eyebrow">Telusuri</p><h2 class="h2 mt-2">Kategori Produk</h2></div>
        <div data-stagger="70" class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5">
            @foreach ($kategori as $k)
                <a href="{{ route('kategori.show', $k) }}" class="group card flex flex-col items-center p-6 text-center transition duration-300 hover:-translate-y-1.5 hover:border-accent/40 hover:shadow-lg">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-accent-soft text-accent transition duration-300 group-hover:scale-110 group-hover:bg-accent group-hover:text-white"><x-icon name="tag" size="h-7 w-7" /></span>
                    <span class="mt-4 text-sm font-semibold text-fg">{{ $k->nama }}</span>
                    <span class="caption mt-1">{{ $k->produk_count }} produk</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ JASA PEMASANGAN ============ --}}
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div data-reveal class="flex flex-wrap items-end justify-between gap-4">
        <div><p class="eyebrow">Layanan Kami</p><h2 class="h2 mt-2">Jasa Pemasangan Profesional</h2><p class="mt-2 max-w-xl text-sm text-muted">Pemasangan dilakukan di bengkel HITSBAH Transport. Pilih jasa, tentukan jadwal, lalu datang sesuai waktu yang dipilih.</p></div>
        <a href="{{ route('jasa.index') }}" class="btn btn-outline">Lihat Semua Jasa <x-icon name="arrow-right" size="h-4 w-4" /></a>
    </div>
    <div data-stagger="80" class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($jasa as $j)<x-service-card :jasa="$j" />@endforeach
    </div>
</section>

{{-- ============ KEUNGGULAN ============ --}}
<section class="border-y border-line bg-surface-alt">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div data-reveal class="mx-auto max-w-2xl text-center"><p class="eyebrow">Mengapa Kami</p><h2 class="h2 mt-2">Keunggulan {{ $site['site_name'] }}</h2></div>
        <div data-stagger="90" class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([['shield', 'Produk Berkualitas', 'Aksesoris dipilih dengan standar kualitas yang jelas.'], ['wrench', 'Pemasangan Profesional', 'Dikerjakan teknisi berpengalaman di bengkel kami.'], ['calendar', 'Booking Mudah', 'Pilih jadwal pemasangan yang tersedia secara online.'], ['credit-card', 'Pembayaran Praktis', 'QRIS & e-wallet dengan verifikasi cepat dari admin.']] as [$ik, $j, $t])
                <div class="group text-center"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-line bg-white text-accent shadow-sm transition duration-300 group-hover:-translate-y-1 group-hover:shadow-md"><x-icon :name="$ik" size="h-7 w-7" /></span><h3 class="h3 mt-4">{{ $j }}</h3><p class="mt-1 text-sm text-muted">{{ $t }}</p></div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ PROSES PEMESANAN ============ --}}
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div data-reveal class="mx-auto max-w-2xl text-center"><p class="eyebrow">Cara Pemesanan</p><h2 class="h2 mt-2">Pesan dalam 5 Langkah</h2></div>
    <ol data-stagger="90" class="mt-10 grid gap-4 md:grid-cols-5">
        @foreach (['Daftar / Login', 'Pilih Produk atau Jasa', 'Checkout & Pilih Jadwal', 'Bayar & Unggah Bukti', 'Diverifikasi & Diproses'] as $i => $langkah)
            <li class="card p-5 text-center transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                <span class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-accent text-sm font-bold text-white">{{ $i + 1 }}</span>
                <p class="mt-3 text-sm font-semibold text-fg">{{ $langkah }}</p>
            </li>
        @endforeach
    </ol>
</section>

{{-- ============ TESTIMONI ============ --}}
@if ($testimoni->isNotEmpty())
<section class="border-y border-line bg-surface-alt">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div data-reveal class="mx-auto max-w-2xl text-center"><p class="eyebrow">Testimoni</p><h2 class="h2 mt-2">Kata Pelanggan Kami</h2></div>
        <div data-stagger="100" class="mt-10 grid gap-6 md:grid-cols-3">
            @foreach ($testimoni as $t)
                <figure class="card flex flex-col p-6 transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <x-rating-stars :rating="$t->rating" />
                    <blockquote class="mt-3 flex-1 text-sm leading-relaxed text-fg">“{{ $t->komen }}”</blockquote>
                    <figcaption class="mt-4 flex items-center gap-3 border-t border-line pt-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-sm font-bold uppercase text-white">{{ mb_substr($t->user->nama, 0, 1) }}</span>
                        <span><span class="block text-sm font-semibold text-fg">{{ $t->user->nama }}</span><span class="caption block">Membeli {{ $t->produk->nama }}</span></span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ============ CTA ============ --}}
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div data-reveal="zoom" class="relative overflow-hidden rounded-3xl bg-primary px-6 py-14 text-center text-white sm:px-12">
        <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full bg-accent/25" aria-hidden="true"></div>
        <h2 class="relative text-2xl font-bold sm:text-3xl">Siap Upgrade Mobil Anda?</h2>
        <p class="relative mx-auto mt-3 max-w-xl text-sm text-white/75 sm:text-base">Pilih aksesoris favorit dan booking jadwal pemasangan sekarang juga.</p>
        <div class="relative mt-7 flex flex-wrap justify-center gap-3">
            <a href="{{ route('produk.index') }}" class="btn btn-primary px-6 py-3">Belanja Sekarang</a>
            @guest<a href="{{ route('register') }}" class="btn border border-white/30 bg-transparent px-6 py-3 text-white hover:bg-white/10">Buat Akun Gratis</a>@endguest
        </div>
    </div>
</section>
@endsection
