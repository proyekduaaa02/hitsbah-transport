@extends('layouts.app')
@section('title', 'Pembayaran '.$order->kode)

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <x-checkout-steps :current="3" />
    <x-breadcrumb :items="['Pesanan' => route('user.orders.index'), $order->kode => route('user.orders.show', $order->id_order), 'Pembayaran' => null]" />
    <h1 class="text-2xl font-bold text-ink-900">Pembayaran Pesanan {{ $order->kode }}</h1>

    @if ($order->pembayaran?->status === 'ditolak')
        <x-alert type="error" class="mt-4"><strong>Pembayaran sebelumnya ditolak.</strong> {{ $order->pembayaran->catatan }} Silakan kirim ulang bukti pembayaran yang valid.</x-alert>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_22rem]">
        <form method="POST" action="{{ route('user.payment.store', $order->id_order) }}" enctype="multipart/form-data" novalidate class="card space-y-6 p-5 sm:p-6"
              x-data="{ metode: @js(old('metode', '')), preview: null, pilih(e) { const f = e.target.files[0]; this.preview = f ? URL.createObjectURL(f) : null; } }">
            @csrf
            <fieldset>
                <legend class="form-label">Pilih Metode Pembayaran</legend>
                <div class="grid gap-2 sm:grid-cols-2">
                    @foreach ($metode as $kode => $nama)
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border p-3.5 transition" :class="metode === '{{ $kode }}' ? 'border-brand-600 bg-brand-50 ring-1 ring-brand-600' : 'border-ink-200 hover:border-ink-300'">
                            <input type="radio" name="metode" value="{{ $kode }}" x-model="metode" class="text-brand-600 focus:ring-brand-500">
                            <span class="text-sm font-semibold text-ink-900">{{ $nama }}</span>
                        </label>
                    @endforeach
                </div>
                @error('metode')<p class="form-error">{{ $message }}</p>@enderror
            </fieldset>

            <div x-show="metode && metode !== 'tunai'" x-cloak class="space-y-4">
                <div class="rounded-xl border border-dashed border-ink-200 bg-ink-50 p-4 text-sm">
                    <p class="font-semibold text-ink-900">Petunjuk Pembayaran</p>
                    <p class="mt-1 whitespace-pre-line text-ink-600">{{ $site['info_pembayaran'] ?? 'Hubungi admin untuk informasi pembayaran.' }}</p>
                    <p class="mt-2 text-xs text-ink-500">Transfer sejumlah <strong class="text-ink-900">{{ rupiah($order->total_pembayaran) }}</strong>, lalu unggah bukti pembayaran di bawah ini.</p>
                </div>
                <div>
                    <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran <span class="text-brand-600">*</span></label>
                    <input id="bukti_pembayaran" type="file" name="bukti_pembayaran" accept="image/png,image/jpeg,image/webp" @change="pilih($event)" class="block w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-ink-900 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-white hover:file:bg-ink-700">
                    <p class="mt-1 text-xs text-ink-500">Format JPG, PNG, atau WEBP. Maksimal 3 MB.</p>
                    @error('bukti_pembayaran')<p class="form-error">{{ $message }}</p>@enderror
                    <img x-show="preview" :src="preview" alt="Pratinjau bukti pembayaran" class="mt-3 max-h-64 rounded-lg border border-ink-100">
                </div>
            </div>

            <div x-show="metode === 'tunai'" x-cloak><x-alert type="info">Anda akan membayar langsung di <strong>{{ $site['alamat'] }}</strong>. Admin akan memverifikasi pembayaran setelah uang diterima.</x-alert></div>

            <button type="submit" class="btn btn-primary w-full py-3" :disabled="!metode">Kirim Pembayaran</button>
        </form>

        <aside class="card h-fit p-5">
            <h2 class="font-semibold text-ink-900">Rincian Pesanan</h2>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach ($order->items as $i)<li class="flex justify-between gap-3"><span class="text-ink-600">{{ $i->produk->nama }} × {{ $i->jumlah }}</span><span class="shrink-0 font-medium">{{ rupiah($i->subtotal) }}</span></li>@endforeach
                @foreach ($order->bookings as $b)<li class="flex justify-between gap-3"><span class="text-ink-600">Jasa: {{ $b->service->nama }}<br><span class="text-xs text-ink-500">{{ tanggal_id($b->jadwal_pemasangan, true) }}</span></span><span class="shrink-0 font-medium">{{ rupiah($b->harga) }}</span></li>@endforeach
            </ul>
            <div class="mt-4 flex justify-between border-t border-ink-100 pt-3"><span class="font-semibold">Total Pembayaran</span><span class="text-lg font-extrabold">{{ rupiah($order->total_pembayaran) }}</span></div>
        </aside>
    </div>
</div>
@endsection
