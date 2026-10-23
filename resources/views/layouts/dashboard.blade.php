@php
    $panel = request()->is('super-admin*') ? 'superadmin' : 'admin';
    $pengguna = auth()->user();
    $pembayaranPending = \App\Models\Pembayaran::where('status', 'pending')->count();
    $ordersBaru = \App\Models\Order::where('status', 'pending')->count();

    // [label, nama route, ikon, pola route aktif, badge]
    $menuAdmin = [
        ['Dashboard', 'admin.dashboard', 'squares', 'admin.dashboard', null],
        ['Pesanan', 'admin.orders.index', 'list', 'admin.orders.*', $ordersBaru],
        ['Pembayaran', 'admin.payments.index', 'credit-card', 'admin.payments.*', $pembayaranPending],
        ['Jadwal Pemasangan', 'admin.bookings.index', 'calendar', 'admin.bookings.*', null],
        ['Jasa Pemasangan', 'admin.services.index', 'wrench', 'admin.services.*', null],
        ['Ulasan', 'admin.reviews.index', 'chat', 'admin.reviews.*', null],
        ['Profil', 'admin.profile.edit', 'user', 'admin.profile.*', null],
    ];
    $menuSuper = [
        ['Dashboard', 'superadmin.dashboard', 'squares', 'superadmin.dashboard', null],
        ['Produk', 'superadmin.products.index', 'cube', 'superadmin.products.*', null],
        ['Kategori', 'superadmin.categories.index', 'tag', 'superadmin.categories.*', null],
        ['Pesanan', 'superadmin.orders.index', 'list', 'superadmin.orders.*', $ordersBaru],
        ['Pembayaran', 'superadmin.payments.index', 'credit-card', 'superadmin.payments.*', $pembayaranPending],
        ['Jasa Pemasangan', 'superadmin.services.index', 'wrench', 'superadmin.services.*', null],
        ['Jadwal Pemasangan', 'superadmin.bookings.index', 'calendar', 'superadmin.bookings.*', null],
        ['User', 'superadmin.users.index', 'users', 'superadmin.users.*', null],
        ['Admin', 'superadmin.admins.index', 'shield', 'superadmin.admins.*', null],
        ['Ulasan', 'superadmin.reviews.index', 'chat', 'superadmin.reviews.*', null],
        ['Laporan Penjualan', 'superadmin.reports.index', 'chart', 'superadmin.reports.*', null],
        ['Pengaturan', 'superadmin.settings.edit', 'cog', 'superadmin.settings.*', null],
        ['Profil', 'superadmin.profile.edit', 'user', 'superadmin.profile.*', null],
    ];
    $menu = $panel === 'superadmin' ? $menuSuper : $menuAdmin;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.head')
    <meta name="robots" content="noindex, nofollow">
    @stack('head')
</head>
<body x-data="{ sidebar: false }" class="bg-surface-alt">
    <div class="flex min-h-screen">
        {{-- Overlay mobile --}}
        <div x-show="sidebar" x-cloak
             x-transition:enter="transition duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="sidebar = false" class="fixed inset-0 z-40 bg-primary/40 backdrop-blur-sm lg:hidden"></div>

        {{-- Sidebar (putih, border tipis) --}}
        <aside :class="sidebar ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-line bg-white transition-transform duration-300 ease-out lg:sticky lg:top-0 lg:h-screen lg:translate-x-0" aria-label="Sidebar">
            <div class="flex h-16 shrink-0 items-center justify-between px-5">
                <a href="{{ $pengguna->dashboardUrl() }}"><x-logo /></a>
                <button type="button" class="rounded-lg p-1 text-muted hover:bg-surface-alt lg:hidden" @click="sidebar = false" aria-label="Tutup sidebar"><x-icon name="x" /></button>
            </div>
            <p class="px-5 pb-2 pt-3 text-[11px] font-semibold uppercase tracking-widest text-muted">{{ $panel === 'superadmin' ? 'Super Admin' : 'Admin' }}</p>
            <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 pb-4">
                @foreach ($menu as $i => [$label, $rute, $ikon, $pola, $badge])
                    @php $aktif = request()->routeIs($pola); @endphp
                    <a href="{{ route($rute) }}" @click="sidebar = false" style="--i: {{ $i }}"
                       class="stagger-item group relative flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors duration-200 {{ $aktif ? 'bg-accent-soft text-accent' : 'text-muted hover:bg-surface-alt hover:text-fg' }}" @if ($aktif) aria-current="page" @endif>
                        @if ($aktif)<span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-accent"></span>@endif
                        <x-icon :name="$ikon" size="h-5 w-5" class="transition duration-200 group-hover:scale-110" />
                        <span class="flex-1">{{ $label }}</span>
                        @if ($badge)<span class="rounded-full bg-accent px-2 py-0.5 text-[11px] font-bold text-white">{{ $badge }}</span>@endif
                    </a>
                @endforeach
            </nav>
            <div class="border-t border-line p-3">
                <a href="{{ route('home') }}" class="mb-1 flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-muted transition hover:bg-surface-alt hover:text-fg"><x-icon name="home" /> Lihat Website</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" data-no-loading class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-accent transition hover:bg-accent-soft"><x-icon name="logout" /> Logout</button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="nav-in sticky top-0 z-30 flex h-16 items-center justify-between gap-3 border-b border-line bg-white/90 px-4 backdrop-blur sm:px-6">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" class="rounded-xl p-2 text-fg transition hover:bg-surface-alt lg:hidden" @click="sidebar = true" aria-label="Buka sidebar"><x-icon name="menu" size="h-6 w-6" /></button>
                    <h1 class="truncate text-lg font-bold text-fg">@yield('title', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-semibold leading-tight text-fg">{{ $pengguna->nama }}</p>
                        <p class="text-xs text-muted">{{ $pengguna->roleLabel() }}</p>
                    </div>
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary text-sm font-bold uppercase text-white">{{ mb_substr($pengguna->nama, 0, 1) }}</span>
                </div>
            </header>

            <x-toast />

            <main class="page-in flex-1 p-4 sm:p-6 lg:p-8" id="konten">
                @yield('content')
            </main>
            <footer class="border-t border-line bg-white px-6 py-4 text-center text-xs text-muted">&copy; {{ date('Y') }} {{ $site['nama_usaha'] }}</footer>
        </div>
    </div>
    <x-confirm-dialog />
    @stack('scripts')
</body>
</html>
