@extends($layout)
@section('title', 'Pesanan')

@section('content')
<form method="GET" class="card mb-5 grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_12rem_10rem_10rem_auto]">
    <div class="relative"><x-icon name="search" size="h-5 w-5" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-500" /><input type="search" name="q" value="{{ request('q') }}" placeholder="Cari no. pesanan / pelanggan..." class="form-input pl-10" aria-label="Cari pesanan"></div>
    <select name="status" class="form-input" aria-label="Filter status"><option value="">Semua status</option>@foreach (\App\Support\Status::options('order') as $k => $l)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>@endforeach</select>
    <input type="date" name="dari" value="{{ request('dari') }}" class="form-input" aria-label="Dari tanggal" title="Dari tanggal">
    <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-input" aria-label="Sampai tanggal" title="Sampai tanggal">
    <div class="flex gap-2"><button type="submit" class="btn btn-dark flex-1">Filter</button><a href="{{ route($prefix.'.orders.index') }}" class="btn btn-outline">Reset</a></div>
</form>

<div class="card overflow-hidden">
    @if ($orders->isEmpty())
        <x-empty-state icon="list" title="Pesanan tidak ditemukan" text="Ubah filter atau kata kunci pencarian." />
    @else
        <div class="overflow-x-auto"><table class="data-table">
            <thead><tr><th>Pesanan</th><th>Pelanggan</th><th>Tanggal</th><th>Isi</th><th class="text-right">Total</th><th>Status</th><th>Pembayaran</th><th></th></tr></thead>
            <tbody>@foreach ($orders as $o)
                <tr>
                    <td><a href="{{ route($prefix.'.orders.show', $o) }}" class="font-semibold text-brand-600 hover:underline">{{ $o->kode }}</a></td>
                    <td><p class="font-medium text-ink-900">{{ $o->user->nama }}</p><p class="text-xs text-ink-500">{{ $o->user->email }}</p></td>
                    <td class="whitespace-nowrap text-ink-600">{{ tanggal_id($o->tanggal_order, true) }}</td>
                    <td class="text-xs text-ink-600">{{ $o->items_count }} produk @if ($o->bookings->isNotEmpty())<br>+ {{ $o->bookings->count() }} jasa @endif</td>
                    <td class="whitespace-nowrap text-right font-semibold">{{ rupiah($o->total_pembayaran) }}</td>
                    <td><x-badge :status="$o->status" type="order" /></td>
                    <td><x-badge :status="$o->status_pembayaran" type="pembayaran" /></td>
                    <td class="text-right"><a href="{{ route($prefix.'.orders.show', $o) }}" class="btn btn-outline btn-sm"><x-icon name="eye" size="h-4 w-4" /> Detail</a></td>
                </tr>
            @endforeach</tbody>
        </table></div>
    @endif
</div>
<x-pagination :paginator="$orders" />
@endsection
