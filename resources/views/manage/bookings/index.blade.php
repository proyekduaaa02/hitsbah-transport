@extends($layout)
@section('title', 'Jadwal Pemasangan')

@section('content')
<form method="GET" class="card mb-5 grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_12rem_11rem_auto]">
    <div class="relative"><x-icon name="search" size="h-5 w-5" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-500" /><input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama pelanggan..." class="form-input pl-10" aria-label="Cari"></div>
    <select name="status" class="form-input" aria-label="Filter status"><option value="">Semua status</option>@foreach (\App\Support\Status::options('pemasangan') as $k => $l)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>@endforeach</select>
    <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-input" aria-label="Tanggal pemasangan">
    <div class="flex gap-2"><button type="submit" class="btn btn-dark flex-1">Filter</button><a href="{{ route($prefix.'.bookings.index') }}" class="btn btn-outline">Reset</a></div>
</form>
<p class="mb-3 text-xs text-ink-500">Jam operasional pemasangan: {{ $jamBuka }} – {{ $jamTutup }} WIB (di tempat). Jadwal baru hanya dapat dipilih pada jam operasional dan selama kapasitas masih ada.</p>

<div class="card overflow-hidden">
    @if ($bookings->isEmpty())
        <x-empty-state icon="calendar" title="Belum ada jadwal pemasangan" />
    @else
        <div class="overflow-x-auto"><table class="data-table">
            <thead><tr><th>Jadwal</th><th>Jasa</th><th>Pelanggan</th><th>Pesanan</th><th>Pembayaran</th><th>Status</th><th></th></tr></thead>
            <tbody>@foreach ($bookings as $b)
                <tr>
                    <td class="whitespace-nowrap"><p class="font-semibold text-ink-900">{{ tanggal_id($b->jadwal_pemasangan) }}</p><p class="text-xs text-ink-500">{{ $b->jadwal_pemasangan->format('H:i') }} WIB</p></td>
                    <td><p class="font-medium">{{ $b->service->nama }}</p><p class="text-xs text-ink-500">± {{ $b->service->durasi_label }}</p></td>
                    <td><p>{{ $b->user->nama }}</p><p class="text-xs text-ink-500">{{ $b->user->email }}</p></td>
                    <td><a href="{{ route($prefix.'.orders.show', $b->order) }}" class="font-semibold text-brand-600 hover:underline">{{ $b->order->kode }}</a></td>
                    <td><x-badge :status="$b->order->status_pembayaran" type="pembayaran" /></td>
                    <td><x-badge :status="$b->status_pemasangan" type="pemasangan" /></td>
                    <td class="text-right"><button type="button" class="btn btn-outline btn-sm" @click="$dispatch('open-modal', 'booking-{{ $b->id_services_booking }}')"><x-icon name="pencil" size="h-4 w-4" /> Atur</button></td>
                </tr>
            @endforeach</tbody>
        </table></div>
    @endif
</div>
<x-pagination :paginator="$bookings" />

@foreach ($bookings as $b)
    <x-modal :name="'booking-'.$b->id_services_booking" title="Atur Jadwal Pemasangan" :show="$errors->any() && (int) old('_booking') === $b->id_services_booking">
        <form method="POST" action="{{ route($prefix.'.bookings.update', $b) }}" class="space-y-4" novalidate>
            @csrf @method('PATCH') <input type="hidden" name="_booking" value="{{ $b->id_services_booking }}">
            <div class="rounded-lg bg-ink-50 p-3 text-sm"><p class="font-semibold text-ink-900">{{ $b->service->nama }}</p><p class="text-ink-600">{{ $b->user->nama }} · {{ $b->order->kode }}</p></div>
            <div>
                <label class="form-label" for="status-{{ $b->id_services_booking }}">Status Pemasangan</label>
                <select id="status-{{ $b->id_services_booking }}" name="status_pemasangan" class="form-input">@foreach (\App\Support\Status::options('pemasangan') as $k => $l)<option value="{{ $k }}" @selected($b->status_pemasangan === $k)>{{ $l }}</option>@endforeach</select>
            </div>
            <div>
                <label class="form-label" for="jadwal-{{ $b->id_services_booking }}">Jadwal</label>
                <input id="jadwal-{{ $b->id_services_booking }}" type="datetime-local" name="jadwal_pemasangan" value="{{ $b->jadwal_pemasangan->format('Y-m-d\TH:i') }}" class="form-input">
                @if ((int) old('_booking') === $b->id_services_booking)@error('jadwal_pemasangan')<p class="form-error">{{ $message }}</p>@enderror @endif
            </div>
            <div class="flex justify-end gap-2"><button type="button" class="btn btn-outline" @click="$dispatch('close-modal', 'booking-{{ $b->id_services_booking }}')">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
        </form>
    </x-modal>
@endforeach
@endsection
