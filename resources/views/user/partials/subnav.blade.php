@php
    $tabs = [
        ['Dashboard', 'user.dashboard', 'squares', 'user.dashboard'],
        ['Keranjang', 'user.cart.index', 'cart', 'user.cart.*'],
        ['Pesanan', 'user.orders.index', 'list', 'user.orders.*'],
        ['Jadwal Pemasangan', 'user.services.index', 'calendar', ['user.services.index', 'user.services.booking']],
        ['Riwayat Pemasangan', 'user.services.history', 'clock', 'user.services.history'],
        ['Ulasan & Rating', 'user.reviews.index', 'chat', 'user.reviews.*'],
        ['Profil', 'user.profile.edit', 'user', 'user.profile.*'],
    ];
@endphp
<nav class="-mx-4 mb-6 overflow-x-auto px-4 sm:mx-0 sm:px-0" aria-label="Menu akun">
    <ul class="flex min-w-max gap-1 rounded-xl border border-ink-100 bg-white p-1.5 shadow-sm">
        @foreach ($tabs as [$label, $rute, $ikon, $pola])
            @php $aktif = request()->routeIs($pola); @endphp
            <li><a href="{{ route($rute) }}" class="flex items-center gap-2 whitespace-nowrap rounded-xl px-3.5 py-2 text-sm font-medium transition-colors duration-200 {{ $aktif ? 'bg-accent-soft text-accent' : 'text-muted hover:bg-surface-alt hover:text-fg' }}" @if ($aktif) aria-current="page" @endif><x-icon :name="$ikon" size="h-4 w-4" /> {{ $label }}</a></li>
        @endforeach
    </ul>
</nav>
