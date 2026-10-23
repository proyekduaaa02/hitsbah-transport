@props(['type' => 'info'])
@php
    $kelas = [
        'info' => 'border-line bg-surface-alt text-fg',
        'success' => 'border-line bg-surface-alt text-fg',
        'warning' => 'border-line bg-surface-alt text-fg',
        'error' => 'border-accent/25 bg-accent-soft text-accent-hover',
    ][$type] ?? 'border-line bg-surface-alt text-fg';
@endphp
<div {{ $attributes->class(['anim-fade flex items-start gap-3 rounded-xl border px-4 py-3 text-sm', $kelas]) }} role="alert">
    <x-icon :name="$type === 'success' ? 'check' : ($type === 'info' ? 'clock' : 'warning')" size="h-5 w-5" class="mt-0.5" />
    <div class="flex-1">{{ $slot }}</div>
</div>
