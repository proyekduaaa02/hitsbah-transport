@props(['rating' => 0, 'jumlah' => null, 'size' => 'h-4 w-4'])
<span {{ $attributes->class(['inline-flex items-center gap-1']) }} title="Rating {{ number_format((float) $rating, 1) }} dari 5">
    <span class="inline-flex" aria-hidden="true">
        @for ($i = 1; $i <= 5; $i++)
            <svg class="{{ $size }} {{ $i <= round($rating) ? 'text-fg' : 'text-ink-200' }}" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd"/></svg>
        @endfor
    </span>
    @if (! is_null($jumlah))
        <span class="text-xs text-ink-500">{{ number_format((float) $rating, 1) }} ({{ $jumlah }})</span>
    @endif
</span>
