<footer class="mt-20 border-t border-line bg-surface-alt text-muted">
    <div data-stagger="90" class="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 md:grid-cols-4 lg:px-8">
        <div class="md:col-span-2">
            <x-logo />
            <p class="mt-4 max-w-md text-sm leading-relaxed">{{ $site['deskripsi'] }}</p>
        </div>
        <div>
            <h3 class="text-sm font-semibold uppercase tracking-wider text-fg">Menu</h3>
            <ul class="mt-4 space-y-2 text-sm">
                @foreach ([['Home', route('home')], ['Produk', route('produk.index')], ['Jasa', route('jasa.index')], ['Tentang', route('home').'#tentang']] as [$l, $u])
                    <li><a href="{{ $u }}" class="inline-block transition duration-200 hover:translate-x-0.5 hover:text-accent">{{ $l }}</a></li>
                @endforeach
                <li>@auth<a href="{{ auth()->user()->dashboardUrl() }}" class="inline-block transition duration-200 hover:translate-x-0.5 hover:text-accent">Dashboard</a>@else<a href="{{ route('login') }}" class="inline-block transition duration-200 hover:translate-x-0.5 hover:text-accent">Login</a>@endauth</li>
            </ul>
        </div>
        <div>
            <h3 class="text-sm font-semibold uppercase tracking-wider text-fg">Kontak</h3>
            <ul class="mt-4 space-y-3 text-sm">
                <li class="flex gap-2"><x-icon name="map-pin" size="h-5 w-5" class="mt-0.5 text-accent" /><span>{{ $site['alamat'] }}</span></li>
                <li class="flex gap-2"><x-icon name="phone" size="h-5 w-5" class="text-accent" /><span>{{ $site['kontak'] }}</span></li>
                <li class="flex gap-2"><x-icon name="mail" size="h-5 w-5" class="text-accent" /><span class="break-all">{{ $site['email'] }}</span></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-line py-5 text-center text-xs">&copy; {{ date('Y') }} {{ $site['nama_usaha'] }}. Seluruh hak cipta dilindungi.</div>
</footer>
