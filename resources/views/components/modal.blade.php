@props(['name', 'title' => '', 'show' => false, 'maxWidth' => 'max-w-lg'])
<div x-data="{ open: {{ $show ? 'true' : 'false' }} }"
     x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
     x-on:close-modal.window="if (!$event.detail || $event.detail === '{{ $name }}') open = false"
     x-on:keydown.escape.window="open = false"
     x-show="open" x-cloak
     x-transition:enter="transition duration-250 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[90] flex items-end justify-center p-0 sm:items-center sm:p-4" role="dialog" aria-modal="true" aria-label="{{ $title }}">
    <div class="absolute inset-0 bg-primary/40 backdrop-blur-sm" @click="open = false"></div>
    <div x-show="open"
         x-transition:enter="transition duration-250 ease-out" x-transition:enter-start="scale-95 opacity-0 translate-y-3 sm:translate-y-0" x-transition:enter-end="scale-100 opacity-100 translate-y-0"
         x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0"
         class="relative max-h-[92vh] w-full {{ $maxWidth }} overflow-y-auto rounded-t-3xl bg-white shadow-2xl sm:rounded-2xl">
        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-line bg-white px-5 py-4">
            <h2 class="text-base font-semibold text-fg">{{ $title }}</h2>
            <button type="button" @click="open = false" class="rounded-lg p-1 text-muted transition hover:bg-surface-alt hover:text-fg" aria-label="Tutup"><x-icon name="x" /></button>
        </div>
        <div class="p-5">{{ $slot }}</div>
    </div>
</div>
