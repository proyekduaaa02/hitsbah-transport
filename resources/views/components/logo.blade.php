<span {{ $attributes->class(['inline-flex items-center gap-2.5']) }}>
    @if (! empty($site['logo']))
        <img src="{{ asset('storage/'.$site['logo']) }}" alt="Logo {{ $site['nama_usaha'] }}" class="h-9 w-9 rounded-lg object-contain">
    @else
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-accent text-white"><x-icon name="bolt" size="h-5 w-5" /></span>
    @endif
    <span class="leading-tight">
        <span class="block text-[15px] font-extrabold uppercase tracking-wider text-fg">{{ $site['site_name'] }}</span>
        <span class="block text-[10px] font-medium uppercase tracking-[0.2em] text-muted">Aksesoris &amp; Pemasangan</span>
    </span>
</span>
