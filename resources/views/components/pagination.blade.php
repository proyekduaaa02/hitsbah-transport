@props(['paginator'])
@if ($paginator->hasPages())
    <div {{ $attributes->class(['mt-6']) }}>{{ $paginator->withQueryString()->links() }}</div>
@endif
