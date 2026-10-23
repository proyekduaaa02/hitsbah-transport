@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex flex-col items-center justify-between gap-3 sm:flex-row">
        @if (method_exists($paginator, 'total'))
            <p class="text-sm text-ink-500">Menampilkan <span class="font-semibold text-ink-800">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</span> dari <span class="font-semibold text-ink-800">{{ $paginator->total() }}</span> data</p>
        @endif
        <div class="flex flex-wrap items-center justify-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="rounded-lg border border-ink-100 bg-white px-3 py-1.5 text-sm text-ink-300">&laquo;</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="rounded-lg border border-ink-200 bg-white px-3 py-1.5 text-sm text-ink-700 hover:bg-ink-100" aria-label="Sebelumnya">&laquo;</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-sm text-ink-500">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="rounded-lg bg-brand-600 px-3 py-1.5 text-sm font-semibold text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="rounded-lg border border-ink-200 bg-white px-3 py-1.5 text-sm text-ink-700 hover:bg-ink-100" aria-label="Halaman {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="rounded-lg border border-ink-200 bg-white px-3 py-1.5 text-sm text-ink-700 hover:bg-ink-100" aria-label="Berikutnya">&raquo;</a>
            @else
                <span class="rounded-lg border border-ink-100 bg-white px-3 py-1.5 text-sm text-ink-300">&raquo;</span>
            @endif
        </div>
    </nav>
@endif
