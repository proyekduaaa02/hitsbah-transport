@extends('layouts.app')
@section('title', 'Riwayat Pesanan')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    @include('user.partials.subnav')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-2xl font-bold text-ink-900">Riwayat Pesanan</h1>
        <form method="GET" class="flex gap-2">
            <select name="status" class="form-input py-2" aria-label="Filter status" onchange="this.form.submit()">
                <option value="">Semua status</option>
                @foreach (\App\Support\Status::options('order') as $k => $l)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>@endforeach
            </select>
        </form>
    </div>

    @if ($orders->isEmpty())
        <div class="card mt-6"><x-empty-state icon="list" title="Belum ada pesanan" text="Pesanan Anda akan muncul di sini."><a href="{{ route('produk.index') }}" class="btn btn-primary">Mulai Belanja</a></x-empty-state></div>
    @else
        <div class="mt-6 space-y-4">
            @foreach ($orders as $o)
                <article class="card p-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div><a href="{{ route('user.orders.show', $o->id_order) }}" class="text-lg font-bold text-ink-900 hover:text-brand-600">{{ $o->kode }}</a><p class="text-xs text-ink-500">{{ tanggal_id($o->tanggal_order, true) }}</p></div>
                        <div class="flex flex-wrap gap-2"><x-badge :status="$o->status" type="order" /><x-badge :status="$o->status_pembayaran" type="pembayaran" />@foreach ($o->bookings as $b)<x-badge :status="$b->status_pemasangan" type="pemasangan" />@endforeach</div>
                    </div>
                    <p class="mt-3 line-clamp-2 text-sm text-ink-600">
                        {{ $o->items->map(fn ($i) => $i->produk->nama.' ×'.$i->jumlah)->concat($o->bookings->map(fn ($b) => 'Jasa: '.$b->service->nama))->implode(', ') }}
                    </p>
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-ink-100 pt-4">
                        <p class="text-lg font-extrabold text-ink-900">{{ rupiah($o->total_pembayaran) }}</p>
                        <div class="flex gap-2">
                            @if (! $o->pembayaran && in_array($o->status, ['pending', 'diterima']))<a href="{{ route('user.payment.show', $o->id_order) }}" class="btn btn-primary btn-sm">Bayar Sekarang</a>@endif
                            <a href="{{ route('user.orders.show', $o->id_order) }}" class="btn btn-outline btn-sm">Detail</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <x-pagination :paginator="$orders" />
    @endif
</div>
@endsection
