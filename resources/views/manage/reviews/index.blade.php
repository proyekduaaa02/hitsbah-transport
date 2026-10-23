@extends($layout)
@section('title', 'Ulasan Pelanggan')

@section('content')
<form method="GET" class="card mb-5 grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_10rem_12rem_auto]">
    <div class="relative"><x-icon name="search" size="h-5 w-5" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-500" /><input type="search" name="q" value="{{ request('q') }}" placeholder="Cari komentar / produk / pelanggan..." class="form-input pl-10" aria-label="Cari ulasan"></div>
    <select name="rating" class="form-input" aria-label="Filter rating"><option value="">Semua rating</option>@for ($i = 5; $i >= 1; $i--)<option value="{{ $i }}" @selected(request('rating') == $i)>{{ $i }} bintang</option>@endfor</select>
    <select name="balasan" class="form-input" aria-label="Filter balasan"><option value="">Semua ulasan</option><option value="belum" @selected(request('balasan') === 'belum')>Belum dibalas</option></select>
    <div class="flex gap-2"><button type="submit" class="btn btn-dark flex-1">Filter</button><a href="{{ route($prefix.'.reviews.index') }}" class="btn btn-outline">Reset</a></div>
</form>

@if ($reviews->isEmpty())
    <div class="card"><x-empty-state icon="chat" title="Belum ada ulasan" text="Ulasan pelanggan akan muncul di sini." /></div>
@else
    <div class="space-y-4">
        @foreach ($reviews as $r)
            @php $err = (int) old('_review') === $r->id_reviews; @endphp
            <article class="card p-5" x-data="{ balas: {{ $err ? 'true' : 'false' }} }">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div><p class="font-semibold text-ink-900">{{ $r->user->nama }}</p><a href="{{ route('produk.show', $r->produk) }}" target="_blank" class="text-xs text-brand-600 hover:underline">{{ $r->produk->nama }}</a></div>
                    <div class="text-right"><x-rating-stars :rating="$r->rating" /><p class="text-xs text-ink-500">{{ tanggal_id($r->created_at) }}</p></div>
                </div>
                <p class="mt-3 text-sm text-ink-700">{{ $r->komen }}</p>
                @if ($r->balasan_admin)<div class="mt-3 rounded-lg border-l-4 border-brand-600 bg-ink-50 p-3 text-sm"><p class="text-xs font-semibold text-brand-700">Balasan Anda · {{ tanggal_id($r->tanggal_balasan) }}</p><p class="mt-1 text-ink-700">{{ $r->balasan_admin }}</p></div>@endif
                <div class="mt-4 flex flex-wrap gap-2">
                    <button type="button" class="btn btn-outline btn-sm" @click="balas = !balas"><x-icon name="chat" size="h-4 w-4" /> {{ $r->balasan_admin ? 'Ubah Balasan' : 'Balas' }}</button>
                    <form method="POST" action="{{ route($prefix.'.reviews.destroy', $r) }}" data-confirm="Hapus ulasan ini secara permanen?">@csrf @method('DELETE')<button type="submit" class="btn btn-outline btn-sm text-accent! hover:bg-accent-soft!" data-no-loading><x-icon name="trash" size="h-4 w-4" /> Hapus</button></form>
                </div>
                <form x-show="balas" x-cloak method="POST" action="{{ route($prefix.'.reviews.reply', $r) }}" class="mt-4 space-y-2" novalidate>
                    @csrf @method('PATCH') <input type="hidden" name="_review" value="{{ $r->id_reviews }}">
                    <x-field name="balasan_admin" type="textarea" rows="2" label="Balasan" :value="$err ? old('balasan_admin') : $r->balasan_admin" :error="$err" placeholder="Tulis balasan untuk pelanggan..." />
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Balasan</button>
                </form>
            </article>
        @endforeach
    </div>
    <x-pagination :paginator="$reviews" />
@endif
@endsection
