@props(['label', 'value' => null, 'count' => null, 'format' => 'number', 'icon' => 'chart', 'tone' => 'soft', 'href' => null])
@php
    $chip = [
        'soft' => 'bg-accent-soft text-accent',
        'red' => 'bg-accent text-white',
        'dark' => 'bg-surface-alt text-fg',
    ][in_array($tone, ['soft', 'red', 'dark'], true) ? $tone : 'soft'];
    $tag = $href ? 'a' : 'div';
    $tampil = $count !== null ? ($format === 'rupiah' ? rupiah($count) : number_format((float) $count, 0, ',', '.')) : $value;
@endphp
<{{ $tag }} @if ($href) href="{{ $href }}" @endif {{ $attributes->class(['group card flex items-center gap-4 p-5 transition duration-300', 'hover:-translate-y-1 hover:shadow-lg' => $href]) }}>
    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl transition duration-300 group-hover:scale-105 {{ $chip }}"><x-icon :name="$icon" size="h-6 w-6" /></div>
    <div class="min-w-0">
        <p class="truncate text-xs font-medium uppercase tracking-wide text-muted">{{ $label }}</p>
        <p class="truncate text-2xl font-bold tabular-nums text-fg" @if ($count !== null) data-count="{{ $count }}" data-format="{{ $format }}" @endif>{{ $tampil }}</p>
    </div>
</{{ $tag }}>
