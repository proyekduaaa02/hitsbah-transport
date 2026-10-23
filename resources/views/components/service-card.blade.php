@props(['jasa'])
<article class="group card flex h-full flex-col p-5 transition duration-300 ease-out hover:-translate-y-1.5 hover:shadow-[0_22px_44px_-22px_rgba(17,17,17,0.28)]">
    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-accent-soft text-accent transition duration-300 group-hover:bg-accent group-hover:text-white">
        <x-icon name="wrench" size="h-6 w-6" class="transition duration-300 group-hover:-rotate-12 group-hover:scale-110" />
    </div>
    <h3 class="mt-4 text-base font-semibold text-fg"><a href="{{ route('jasa.show', $jasa) }}" class="transition-colors hover:text-accent">{{ $jasa->nama }}</a></h3>
    <p class="mt-2 line-clamp-3 flex-1 text-sm text-muted">{{ $jasa->deskripsi }}</p>
    <div class="mt-4 flex items-end justify-between gap-3 border-t border-line pt-4">
        <div>
            <p class="text-lg font-bold text-fg">{{ rupiah($jasa->harga) }}</p>
            <p class="flex items-center gap-1 text-xs text-muted"><x-icon name="clock" size="h-3.5 w-3.5" /> {{ $jasa->durasi_label }}</p>
        </div>
        <a href="{{ route('jasa.show', $jasa) }}" class="inline-flex items-center gap-1 text-sm font-semibold text-accent transition duration-300 md:translate-y-1 md:opacity-0 md:group-hover:translate-y-0 md:group-hover:opacity-100 md:group-focus-within:translate-y-0 md:group-focus-within:opacity-100">
            Booking <x-icon name="arrow-right" size="h-4 w-4" class="transition group-hover:translate-x-0.5" />
        </a>
    </div>
</article>
