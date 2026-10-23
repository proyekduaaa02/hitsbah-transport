@props(['produk'])
@php $bisaBeli = ! auth()->check() || auth()->user()->isUser(); @endphp
<article class="group card flex h-full flex-col overflow-hidden transition duration-300 ease-out hover:-translate-y-1.5 hover:shadow-[0_22px_44px_-22px_rgba(17,17,17,0.28)]">
    <a href="{{ route('produk.show', $produk) }}" class="relative block" aria-label="Lihat detail {{ $produk->nama }}">
        <x-product-image :produk="$produk">
            <span class="pointer-events-none absolute inset-x-3 bottom-3 flex translate-y-2 items-center justify-center gap-1.5 rounded-lg bg-white/95 py-2 text-xs font-semibold text-fg opacity-0 shadow-sm backdrop-blur transition duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                <x-icon name="eye" size="h-4 w-4" /> Lihat Detail
            </span>
        </x-product-image>
    </a>
    <div class="flex flex-1 flex-col p-4">
        <a href="{{ route('kategori.show', $produk->id_kategori) }}" class="text-[11px] font-semibold uppercase tracking-wider text-accent hover:underline">{{ $produk->kategori->nama }}</a>
        <h3 class="mt-1 line-clamp-2 min-h-[2.75rem] text-sm font-semibold leading-snug text-fg">
            <a href="{{ route('produk.show', $produk) }}" class="transition-colors hover:text-accent">{{ $produk->nama }}</a>
        </h3>
        <x-rating-stars class="mt-2" :rating="$produk->rating_rata" :jumlah="$produk->jumlah_ulasan" />
        <div class="mt-3 flex items-end justify-between gap-2">
            <p class="text-lg font-bold text-fg">{{ rupiah($produk->harga) }}</p>
            @if ($produk->stok > 0)
                <span class="text-xs {{ $produk->stok <= 5 ? 'font-semibold text-accent' : 'text-muted' }}">Stok {{ $produk->stok }}</span>
            @else
                <span class="text-xs font-semibold text-accent">Stok habis</span>
            @endif
        </div>
        <div class="mt-4 grid grid-cols-2 gap-2">
            <a href="{{ route('produk.show', $produk) }}" class="btn btn-outline btn-sm">Detail</a>
            @if ($bisaBeli)
                @auth
                    <form method="POST" action="{{ route('user.cart.add') }}" data-cart-form>
                        @csrf
                        <input type="hidden" name="id_produk" value="{{ $produk->id_produk }}">
                        <input type="hidden" name="jumlah" value="1">
                        <button type="submit" data-cart-btn class="btn btn-primary btn-sm w-full" @disabled($produk->stok < 1)>
                            <span class="cart-default inline-flex items-center gap-1.5"><x-icon name="cart" size="h-4 w-4" /> Tambah</span>
                            <span class="cart-added"><x-icon name="check" size="h-4 w-4" /> Ditambahkan</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm"><x-icon name="cart" size="h-4 w-4" /> Tambah</a>
                @endauth
            @endif
        </div>
    </div>
</article>
