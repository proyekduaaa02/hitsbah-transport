@props(['items' => []])
<nav aria-label="Breadcrumb" class="mb-4 text-sm">
    <ol class="flex flex-wrap items-center gap-1.5 text-ink-500">
        <li><a href="{{ route('home') }}" class="hover:text-brand-600">Beranda</a></li>
        @foreach ($items as $label => $url)
            <li class="flex items-center gap-1.5">
                <x-icon name="chevron-right" size="h-3.5 w-3.5" />
                @if ($url)<a href="{{ $url }}" class="hover:text-brand-600">{{ $label }}</a>@else<span class="font-medium text-ink-800" aria-current="page">{{ $label }}</span>@endif
            </li>
        @endforeach
    </ol>
</nav>
