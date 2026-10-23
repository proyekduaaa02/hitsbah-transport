@php
    $menu = [
        ['Home', route('home'), request()->routeIs('home')],
        ['Produk', route('produk.index'), request()->routeIs('produk.*', 'kategori.*')],
        ['Jasa Pemasangan', route('jasa.index'), request()->routeIs('jasa.*')],
        ['Tentang', route('home').'#tentang', false],
    ];
    $user = auth()->user();
@endphp
<header x-data="navbar" class="nav-in sticky top-0 z-40 border-b transition-all duration-300"
        :class="scrolled ? 'border-line bg-white/95 shadow-[0_8px_30px_-14px_rgba(17,17,17,0.22)] backdrop-blur-md' : 'border-transparent bg-white/80 backdrop-blur-md'">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" aria-label="Beranda {{ $site['site_name'] }}" class="transition-opacity hover:opacity-80"><x-logo /></a>

        <nav class="hidden items-center gap-1 md:flex" aria-label="Menu utama">
            @foreach ($menu as [$label, $url, $aktif])
                <a href="{{ $url }}" class="nav-link {{ $aktif ? 'is-active' : '' }}">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            @if ($user && $user->isUser())
                <a href="{{ route('user.cart.index') }}" class="group relative rounded-xl p-2 text-fg transition hover:bg-surface-alt" aria-label="Keranjang belanja">
                    <x-icon name="cart" size="h-6 w-6" class="transition duration-300 group-hover:-rotate-6 group-hover:scale-110" />
                    <span data-cart-badge class="{{ ($jumlahKeranjang ?? 0) > 0 ? '' : 'hidden' }} absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-accent px-1 text-[11px] font-bold text-white">{{ $jumlahKeranjang ?? 0 }}</span>
                </a>
            @endif

            @guest
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm hidden sm:inline-flex">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm hidden sm:inline-flex">Daftar</a>
            @else
                <div class="relative hidden md:block" @click.outside="akun = false" @keydown.escape.window="akun = false">
                    <button type="button" @click="akun = !akun" class="flex items-center gap-2 rounded-xl px-2 py-1.5 transition hover:bg-surface-alt" aria-haspopup="true" :aria-expanded="akun">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-sm font-bold uppercase text-white">{{ mb_substr($user->nama, 0, 1) }}</span>
                        <span class="max-w-[8rem] truncate text-sm font-medium">{{ $user->nama }}</span>
                        <x-icon name="chevron-down" size="h-4 w-4" class="transition duration-200" ::class="akun ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="akun" x-cloak
                         x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1"
                         class="absolute right-0 mt-2 w-60 overflow-hidden rounded-2xl border border-line bg-white py-1 shadow-xl">
                        <div class="border-b border-line px-4 py-2.5"><p class="truncate text-sm font-semibold">{{ $user->nama }}</p><p class="truncate text-xs text-muted">{{ $user->email }} · {{ $user->roleLabel() }}</p></div>
                        <a href="{{ $user->dashboardUrl() }}" class="flex items-center gap-2 px-4 py-2 text-sm transition hover:bg-surface-alt"><x-icon name="squares" size="h-4 w-4" /> Dashboard</a>
                        @if ($user->isUser())
                            <a href="{{ route('user.orders.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm transition hover:bg-surface-alt"><x-icon name="list" size="h-4 w-4" /> Pesanan Saya</a>
                            <a href="{{ route('user.profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm transition hover:bg-surface-alt"><x-icon name="user" size="h-4 w-4" /> Profil Saya</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button type="submit" data-no-loading class="flex w-full items-center gap-2 px-4 py-2 text-sm text-accent transition hover:bg-accent-soft"><x-icon name="logout" size="h-4 w-4" /> Keluar</button>
                        </form>
                    </div>
                </div>
            @endguest

            <button type="button" @click="buka = !buka" class="rounded-xl p-2 text-fg transition hover:bg-surface-alt md:hidden" aria-label="Buka menu" :aria-expanded="buka">
                <x-icon name="menu" size="h-6 w-6" x-show="!buka" /><x-icon name="x" size="h-6 w-6" x-show="buka" x-cloak />
            </button>
        </div>
    </div>

    {{-- Menu mobile: slide-down, item muncul berurutan --}}
    <div x-show="buka" x-cloak
         x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0 -translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-3"
         class="border-t border-line bg-white md:hidden">
        <nav class="space-y-1 px-4 py-3" aria-label="Menu mobile">
            @foreach ($menu as $i => [$label, $url, $aktif])
                <a href="{{ $url }}" style="--i: {{ $i }}" class="stagger-item block rounded-xl px-3 py-2.5 text-sm font-medium transition {{ $aktif ? 'bg-accent-soft text-accent' : 'text-fg hover:bg-surface-alt' }}">{{ $label }}</a>
            @endforeach
            @guest
                <div class="stagger-item grid grid-cols-2 gap-2 pt-2" style="--i: 4">
                    <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
                </div>
            @else
                <a href="{{ $user->dashboardUrl() }}" style="--i: 4" class="stagger-item block rounded-xl px-3 py-2.5 text-sm font-medium text-fg hover:bg-surface-alt">Dashboard</a>
                @if ($user->isUser())
                    <a href="{{ route('user.orders.index') }}" style="--i: 5" class="stagger-item block rounded-xl px-3 py-2.5 text-sm font-medium text-fg hover:bg-surface-alt">Pesanan Saya</a>
                    <a href="{{ route('user.profile.edit') }}" style="--i: 6" class="stagger-item block rounded-xl px-3 py-2.5 text-sm font-medium text-fg hover:bg-surface-alt">Profil Saya</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="stagger-item" style="--i: 7">@csrf
                    <button type="submit" data-no-loading class="block w-full rounded-xl px-3 py-2.5 text-left text-sm font-medium text-accent hover:bg-accent-soft">Keluar</button>
                </form>
            @endguest
        </nav>
    </div>
</header>
