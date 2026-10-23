@extends('layouts.app')
@section('title', 'Ulasan & Rating')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    @include('user.partials.subnav')
    <h1 class="text-2xl font-bold text-ink-900">Ulasan &amp; Rating</h1>
    <p class="mt-1 text-sm text-ink-500">Berikan penilaian untuk produk dari pesanan Anda yang sudah selesai.</p>

    @if ($produk->isEmpty())
        <div class="card mt-6"><x-empty-state icon="chat" title="Belum ada produk untuk diulas" text="Produk dari pesanan yang sudah selesai akan muncul di sini."><a href="{{ route('produk.index') }}" class="btn btn-dark">Lihat Produk</a></x-empty-state></div>
    @else
        <div class="mt-6 space-y-5">
            @foreach ($produk as $p)
                @php $u = $ulasan->get($p->id_produk); $ada = old('id_produk') == $p->id_produk; @endphp
                <article class="card p-5" x-data="{ rating: {{ $ada ? (int) old('rating') : ($u->rating ?? 0) }}, hover: 0 }">
                    <div class="flex items-center gap-4">
                        <div class="w-16 shrink-0 overflow-hidden rounded-lg"><x-product-image :produk="$p" ratio="aspect-square" /></div>
                        <div class="min-w-0 flex-1"><a href="{{ route('produk.show', $p) }}" class="line-clamp-1 font-semibold text-ink-900 hover:text-brand-600">{{ $p->nama }}</a>@if ($u)<p class="text-xs text-muted">Anda sudah mengulas — dapat diperbarui.</p>@endif</div>
                    </div>
                    <form method="POST" action="{{ route('user.reviews.store') }}" class="mt-4 space-y-3" novalidate>
                        @csrf
                        <input type="hidden" name="id_produk" value="{{ $p->id_produk }}">
                        <input type="hidden" name="rating" :value="rating">
                        <div>
                            <span class="form-label">Rating</span>
                            <div class="flex gap-1" role="radiogroup" aria-label="Rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" @click="rating = {{ $i }}" @mouseenter="hover = {{ $i }}" @mouseleave="hover = 0" class="p-0.5" aria-label="{{ $i }} bintang" role="radio" :aria-checked="rating === {{ $i }}">
                                        <svg class="h-8 w-8 transition" :class="(hover || rating) >= {{ $i }} ? 'text-fg' : 'text-ink-200'" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd"/></svg>
                                    </button>
                                @endfor
                            </div>
                            @if ($ada)@error('rating')<p class="form-error">Rating wajib dipilih (1–5).</p>@enderror @endif
                        </div>
                        <div>
                            <label class="form-label" for="komen-{{ $p->id_produk }}">Komentar</label>
                            <textarea id="komen-{{ $p->id_produk }}" name="komen" rows="3" class="form-input" placeholder="Ceritakan pengalaman Anda...">{{ $ada ? old('komen') : ($u->komen ?? '') }}</textarea>
                            @if ($ada)@error('komen')<p class="form-error">{{ $message }}</p>@enderror @endif
                        </div>
                        @if ($u?->balasan_admin)<div class="rounded-lg border-l-4 border-brand-600 bg-ink-50 p-3 text-sm"><p class="text-xs font-semibold text-brand-700">Balasan admin</p><p class="mt-1 text-ink-700">{{ $u->balasan_admin }}</p></div>@endif
                        <button type="submit" class="btn btn-dark">{{ $u ? 'Perbarui Ulasan' : 'Kirim Ulasan' }}</button>
                    </form>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection
