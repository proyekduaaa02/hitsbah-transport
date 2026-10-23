@extends($layout)
@section('title', 'Detail Pembayaran')

@section('content')
@php $order = $payment->order; @endphp
<a href="{{ route($prefix.'.payments.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-medium text-ink-600 hover:text-brand-600"><x-icon name="arrow-left" size="h-4 w-4" /> Kembali ke daftar pembayaran</a>
<div class="grid gap-6 lg:grid-cols-2">
    <section class="card p-5">
        <div class="flex items-start justify-between gap-3"><div><h2 class="text-xl font-bold text-ink-900">Pesanan {{ $order->kode }}</h2><p class="text-sm text-ink-500">{{ $order->user->nama }} · {{ $order->user->email }}</p></div><x-badge :status="$payment->status" type="pembayaran" class="text-sm!" /></div>
        <dl class="mt-5 space-y-3 text-sm">
            <div class="flex justify-between"><dt class="text-ink-500">Metode</dt><dd class="font-medium">{{ $payment->metode_label }}</dd></div>
            <div class="flex justify-between"><dt class="text-ink-500">Dikirim</dt><dd class="font-medium">{{ tanggal_id($payment->tanggal_bayar, true) }}</dd></div>
            <div class="flex justify-between"><dt class="text-ink-500">Total dibayar</dt><dd class="text-lg font-extrabold">{{ rupiah($payment->total_pembayaran) }}</dd></div>
            <div class="flex justify-between"><dt class="text-ink-500">Total pesanan</dt><dd class="font-semibold {{ (float) $payment->total_pembayaran !== (float) $order->total_pembayaran ? 'text-accent' : '' }}">{{ rupiah($order->total_pembayaran) }}</dd></div>
        </dl>
        @if ($payment->catatan)<x-alert :type="$payment->status === 'ditolak' ? 'error' : 'info'" class="mt-4">{{ $payment->catatan }}</x-alert>@endif

        @if ($payment->status === 'pending')
            <div class="mt-6 space-y-3 border-t border-ink-100 pt-5">
                <form method="POST" action="{{ route($prefix.'.payments.verify', $payment) }}" data-confirm="Verifikasi pembayaran ini? Pesanan akan masuk tahap diproses.">@csrf @method('PATCH')<button type="submit" class="btn btn-primary w-full"><x-icon name="check" size="h-4 w-4" /> Verifikasi Pembayaran</button></form>
                <form method="POST" action="{{ route($prefix.'.payments.reject', $payment) }}" class="space-y-2">@csrf @method('PATCH')
                    <x-field name="catatan" type="textarea" rows="2" label="Alasan penolakan (opsional)" placeholder="Contoh: bukti pembayaran tidak terbaca" />
                    <button type="submit" class="btn btn-outline w-full border-accent/40! text-accent! hover:bg-accent-soft!">Tolak Pembayaran</button>
                </form>
            </div>
        @endif
    </section>

    <div class="space-y-6">
        <section class="card p-5">
            <h2 class="font-semibold text-ink-900">Bukti Pembayaran</h2>
            @if ($payment->bukti_url)
                <a href="{{ $payment->bukti_url }}" target="_blank" rel="noopener" class="mt-3 block"><img src="{{ $payment->bukti_url }}" alt="Bukti pembayaran {{ $order->kode }}" loading="lazy" class="max-h-[28rem] w-full rounded-lg border border-ink-100 object-contain"></a>
            @else
                <p class="mt-3 rounded-lg bg-ink-50 p-4 text-sm text-ink-600">{{ $payment->metode === 'tunai' ? 'Pembayaran tunai di tempat — tidak ada bukti unggahan. Verifikasi setelah uang diterima.' : 'Tidak ada bukti pembayaran.' }}</p>
            @endif
        </section>
        <section class="card p-5"><h2 class="font-semibold text-ink-900">Isi Pesanan</h2>
            <ul class="mt-3 space-y-1.5 text-sm">@foreach ($order->items as $i)<li class="flex justify-between gap-3"><span class="text-ink-600">{{ $i->produk->nama }} × {{ $i->jumlah }}</span><span class="font-medium">{{ rupiah($i->subtotal) }}</span></li>@endforeach @foreach ($order->bookings as $b)<li class="flex justify-between gap-3"><span class="text-ink-600">Jasa: {{ $b->service->nama }} ({{ tanggal_id($b->jadwal_pemasangan, true) }})</span><span class="font-medium">{{ rupiah($b->harga) }}</span></li>@endforeach</ul>
            <a href="{{ route($prefix.'.orders.show', $order) }}" class="btn btn-ghost btn-sm mt-3">Buka detail pesanan <x-icon name="arrow-right" size="h-4 w-4" /></a>
        </section>
    </div>
</div>
@endsection
