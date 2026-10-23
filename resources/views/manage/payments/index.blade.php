@extends($layout)
@section('title', 'Pembayaran')

@section('content')
<form method="GET" class="card mb-5 grid gap-3 p-4 sm:grid-cols-[1fr_14rem_auto]">
    <div class="relative"><x-icon name="search" size="h-5 w-5" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-500" /><input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama pelanggan..." class="form-input pl-10" aria-label="Cari"></div>
    <select name="status" class="form-input" aria-label="Filter status"><option value="">Semua status</option>@foreach (\App\Support\Status::options('pembayaran') as $k => $l)@continue($k === 'belum')<option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>@endforeach</select>
    <div class="flex gap-2"><button type="submit" class="btn btn-dark flex-1">Filter</button><a href="{{ route($prefix.'.payments.index') }}" class="btn btn-outline">Reset</a></div>
</form>

<div class="card overflow-hidden">
    @if ($payments->isEmpty())
        <x-empty-state icon="credit-card" title="Belum ada pembayaran" text="Pembayaran yang dikirim pelanggan akan muncul di sini." />
    @else
        <div class="overflow-x-auto"><table class="data-table">
            <thead><tr><th>Pesanan</th><th>Pelanggan</th><th>Metode</th><th>Tanggal Bayar</th><th class="text-right">Total</th><th>Status</th><th></th></tr></thead>
            <tbody>@foreach ($payments as $p)
                <tr>
                    <td><a href="{{ route($prefix.'.orders.show', $p->order) }}" class="font-semibold text-brand-600 hover:underline">{{ $p->order->kode }}</a></td>
                    <td>{{ $p->order->user->nama }}</td><td>{{ $p->metode_label }}</td>
                    <td class="whitespace-nowrap text-ink-600">{{ tanggal_id($p->tanggal_bayar, true) }}</td>
                    <td class="whitespace-nowrap text-right font-semibold">{{ rupiah($p->total_pembayaran) }}</td>
                    <td><x-badge :status="$p->status" type="pembayaran" /></td>
                    <td class="text-right"><a href="{{ route($prefix.'.payments.show', $p) }}" class="btn {{ $p->status === 'pending' ? 'btn-primary' : 'btn-outline' }} btn-sm">{{ $p->status === 'pending' ? 'Verifikasi' : 'Detail' }}</a></td>
                </tr>
            @endforeach</tbody>
        </table></div>
    @endif
</div>
<x-pagination :paginator="$payments" />
@endsection
