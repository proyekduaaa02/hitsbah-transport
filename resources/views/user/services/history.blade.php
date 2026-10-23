@extends('layouts.app')
@section('title', 'Riwayat Pemasangan')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    @include('user.partials.subnav')
    <h1 class="text-2xl font-bold text-ink-900">Riwayat Pemasangan</h1>
    @if ($bookings->isEmpty())
        <div class="card mt-6"><x-empty-state icon="clock" title="Belum ada riwayat pemasangan" text="Pemasangan yang sudah selesai atau dibatalkan akan tampil di sini." /></div>
    @else
        <div class="card mt-6 overflow-x-auto">
            <table class="data-table">
                <thead><tr><th>Jasa</th><th>Jadwal</th><th>Pesanan</th><th class="text-right">Biaya</th><th>Status</th></tr></thead>
                <tbody>@foreach ($bookings as $b)
                    <tr><td class="font-medium text-ink-900">{{ $b->service->nama }}</td><td class="whitespace-nowrap">{{ tanggal_id($b->jadwal_pemasangan, true) }}</td><td><a href="{{ route('user.orders.show', $b->id_order) }}" class="font-semibold text-brand-600 hover:underline">{{ $b->order->kode }}</a></td><td class="whitespace-nowrap text-right font-semibold">{{ rupiah($b->harga) }}</td><td><x-badge :status="$b->status_pemasangan" type="pemasangan" /></td></tr>
                @endforeach</tbody>
            </table>
        </div>
        <x-pagination :paginator="$bookings" />
    @endif
</div>
@endsection
