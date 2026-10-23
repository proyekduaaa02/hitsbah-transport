@props(['current' => 1])
@php
    $langkah = ['Keranjang', 'Informasi', 'Pembayaran', 'Selesai'];
    $persen = (($current - 1) / (count($langkah) - 1)) * 100;
@endphp
<nav aria-label="Tahapan checkout" {{ $attributes->class(['mx-auto mb-8 max-w-2xl']) }}>
    <ol class="relative grid grid-cols-4">
        <span class="absolute left-[12.5%] right-[12.5%] top-4 h-0.5 rounded-full bg-line" aria-hidden="true">
            <span class="step-line block h-full rounded-full bg-accent" style="width: {{ $persen }}%"></span>
        </span>
        @foreach ($langkah as $i => $nama)
            @php $no = $i + 1; $selesai = $no < $current; $aktif = $no === $current; @endphp
            <li class="relative flex flex-col items-center gap-2 text-center" @if ($aktif) aria-current="step" @endif>
                <span class="flex h-8 w-8 items-center justify-center rounded-full border-2 text-xs font-bold transition duration-300 {{ $selesai ? 'border-accent bg-accent text-white' : ($aktif ? 'border-accent bg-white text-accent shadow-[0_0_0_5px_var(--accent-ring)]' : 'border-line bg-white text-muted') }}">
                    @if ($selesai)<x-icon name="check" size="h-4 w-4" class="anim-pop" />@else{{ $no }}@endif
                </span>
                <span class="text-xs font-medium sm:text-sm {{ $aktif || $selesai ? 'text-fg' : 'text-muted' }}">{{ $nama }}</span>
            </li>
        @endforeach
    </ol>
</nav>
