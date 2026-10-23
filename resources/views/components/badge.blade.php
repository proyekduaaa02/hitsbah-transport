@props(['status' => null, 'type' => 'order'])
@php
    $tone = \App\Support\Status::tone($status);
    // Satu palet netral + aksen; hijau/kuning hanya sebagai penanda semantik kecil.
    $peta = [
        'amber' => ['bg-surface-alt text-fg ring-line', 'bg-accent-soft0'],
        'ink' => ['bg-white text-fg ring-line', 'bg-fg'],
        'green' => ['bg-emerald-50 text-emerald-700 ring-emerald-600/15', 'bg-emerald-500'],
        'red' => ['bg-accent-soft text-accent ring-accent/20', 'bg-accent'],
        'gray' => ['bg-surface-alt text-muted ring-line', 'bg-ink-300'],
    ][$tone] ?? ['bg-surface-alt text-muted ring-line', 'bg-ink-300'];
    // Hanya status yang sedang berjalan yang berdenyut halus.
    $berdenyut = in_array($status, ['diproses', 'pending', 'menunggu'], true) && $tone !== 'green';
@endphp
<span {{ $attributes->class(['inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset transition-colors duration-300', $peta[0]]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $peta[1] }} {{ $berdenyut ? 'pulse-dot' : '' }}"></span>{{ \App\Support\Status::label($type, $status) }}
</span>
