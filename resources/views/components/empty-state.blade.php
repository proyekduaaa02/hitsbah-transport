@props(['icon' => 'cube', 'title' => 'Belum ada data', 'text' => null])
<div {{ $attributes->class(['anim-up flex flex-col items-center justify-center px-6 py-14 text-center']) }}>
    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-surface-alt text-muted"><x-icon :name="$icon" size="h-8 w-8" /></div>
    <h3 class="mt-4 text-base font-semibold text-fg">{{ $title }}</h3>
    @if ($text)<p class="mt-1 max-w-sm text-sm text-muted">{{ $text }}</p>@endif
    @if (trim((string) $slot) !== '')<div class="mt-5">{{ $slot }}</div>@endif
</div>
