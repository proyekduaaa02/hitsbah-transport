{{-- Panel visual otomotif untuk halaman login / register --}}
<div class="relative hidden overflow-hidden border-r border-line bg-surface-alt lg:flex lg:flex-col lg:justify-between lg:p-12">
    <div class="pointer-events-none absolute -left-24 -top-24 h-80 w-80 rounded-full bg-accent-soft" aria-hidden="true"></div>
    <a href="{{ route('home') }}" class="relative z-10 w-fit"><x-logo /></a>
    <div class="relative z-10 anim-left" style="--d: 150ms">
        <x-hero-car class="w-full max-w-lg" />
    </div>
    <div class="relative z-10">
        <h2 class="anim-up text-2xl font-bold text-fg" style="--d: 300ms">{{ $judul }}</h2>
        <p class="anim-up mt-2 max-w-md text-sm leading-relaxed text-muted" style="--d: 380ms">{{ $teks }}</p>
        <ul class="anim-up mt-5 space-y-2 text-sm text-fg" style="--d: 460ms">
            @foreach (['Katalog aksesoris lengkap & stok real-time', 'Booking jadwal pemasangan di bengkel', 'Pantau status pesanan & pembayaran'] as $poin)
                <li class="flex items-center gap-2"><span class="flex h-5 w-5 items-center justify-center rounded-full bg-accent text-white"><x-icon name="check" size="h-3 w-3" /></span> {{ $poin }}</li>
            @endforeach
        </ul>
    </div>
</div>
