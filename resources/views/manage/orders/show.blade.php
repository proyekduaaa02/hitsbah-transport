@extends($layout)
@section('title', 'Detail Pesanan '.$order->kode)

@section('content')
@php $bayar = $order->pembayaran; @endphp
<a href="{{ route($prefix.'.orders.index') }}" class="mb-4 inline-flex items-center gap-1 text-sm font-medium text-ink-600 hover:text-brand-600"><x-icon name="arrow-left" size="h-4 w-4" /> Kembali ke daftar pesanan</a>

<div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
        <section class="card p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div><h2 class="text-xl font-bold text-ink-900">{{ $order->kode }}</h2><p class="text-sm text-ink-500">{{ tanggal_id($order->tanggal_order, true) }}</p></div>
                <div class="flex flex-wrap gap-2"><x-badge :status="$order->status" type="order" /><x-badge :status="$order->status_pembayaran" type="pembayaran" /></div>
            </div>
        </section>

        @if ($order->items->isNotEmpty())
        <section class="card overflow-hidden"><h2 class="border-b border-ink-100 px-5 py-4 font-semibold text-ink-900">Produk Dipesan</h2>
            <div class="overflow-x-auto"><table class="data-table"><thead><tr><th>Produk</th><th class="text-right">Harga</th><th class="text-center">Qty</th><th class="text-right">Subtotal</th></tr></thead>
            <tbody>@foreach ($order->items as $i)<tr><td class="font-medium text-ink-900">{{ $i->produk->nama }}</td><td class="whitespace-nowrap text-right">{{ rupiah($i->harga_satuan) }}</td><td class="text-center">{{ $i->jumlah }}</td><td class="whitespace-nowrap text-right font-semibold">{{ rupiah($i->subtotal) }}</td></tr>@endforeach</tbody></table></div>
        </section>
        @endif

        @if ($order->bookings->isNotEmpty())
        <section class="card overflow-hidden"><h2 class="border-b border-ink-100 px-5 py-4 font-semibold text-ink-900">Jasa Pemasangan (di tempat)</h2>
            <ul class="divide-y divide-ink-100">@foreach ($order->bookings as $b)<li class="flex flex-wrap items-center justify-between gap-3 p-4"><div><p class="font-semibold text-ink-900">{{ $b->service->nama }}</p><p class="text-sm text-ink-600">{{ tanggal_id($b->jadwal_pemasangan, true) }} · ± {{ $b->service->durasi_label }}</p></div><div class="text-right"><x-badge :status="$b->status_pemasangan" type="pemasangan" /><p class="mt-1 font-semibold">{{ rupiah($b->harga) }}</p></div></li>@endforeach</ul>
        </section>
        @endif

        <section class="card flex items-center justify-between p-5"><span class="font-semibold text-ink-900">Total Pembayaran</span><span class="text-2xl font-extrabold">{{ rupiah($order->total_pembayaran) }}</span></section>
    </div>

    <div class="space-y-6">
        <section class="card p-5">
            <h2 class="font-semibold text-ink-900">Data Pelanggan</h2>
            <dl class="mt-3 space-y-2 text-sm"><div><dt class="text-xs text-ink-500">Nama</dt><dd class="font-medium">{{ $order->user->nama }}</dd></div><div><dt class="text-xs text-ink-500">Email</dt><dd class="break-all font-medium">{{ $order->user->email }}</dd></div><div><dt class="text-xs text-ink-500">Alamat</dt><dd>{{ $order->user->alamat ?? '-' }}</dd></div></dl>
        </section>

        <section class="card p-5">
            <h2 class="font-semibold text-ink-900">Pembayaran</h2>
            @if ($bayar)
                <dl class="mt-3 space-y-2 text-sm"><div class="flex justify-between"><dt class="text-ink-500">Status</dt><dd><x-badge :status="$bayar->status" type="pembayaran" /></dd></div><div class="flex justify-between"><dt class="text-ink-500">Metode</dt><dd class="font-medium">{{ $bayar->metode_label }}</dd></div></dl>
                <a href="{{ route($prefix.'.payments.show', $bayar) }}" class="btn btn-outline btn-sm mt-3 w-full">Lihat Detail Pembayaran</a>
            @else
                <p class="mt-2 text-sm text-ink-500">Pelanggan belum mengirim pembayaran.</p>
            @endif
        </section>

        <section class="card p-5">
            <h2 class="font-semibold text-ink-900">Proses Pesanan</h2>
            @if (empty($transisi))
                <p class="mt-2 text-sm text-ink-500">Pesanan sudah final, tidak ada tindakan lanjutan.</p>
            @else
                <p class="mt-1 text-xs text-ink-500">Ubah status pesanan ke tahap berikutnya.</p>
                <div class="mt-3 space-y-2">
                    @foreach ($transisi as $s)
                        <form method="POST" action="{{ route($prefix.'.orders.status', $order) }}" @if ($s === 'ditolak') data-confirm="Tolak pesanan ini? Stok akan dikembalikan dan jadwal pemasangan dibatalkan." @endif>
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $s }}">
                            <button type="submit" class="btn w-full {{ $s === 'ditolak' ? 'btn-outline border-accent/40! text-accent! hover:bg-accent-soft!' : 'btn-primary' }}">
                                {{ ['diterima' => 'Terima Pesanan', 'diproses' => 'Proses Pesanan', 'selesai' => 'Tandai Selesai', 'ditolak' => 'Tolak Pesanan'][$s] }}
                            </button>
                        </form>
                    @endforeach
                </div>
                @if (in_array('diproses', $transisi) && ($bayar?->status !== 'diverifikasi'))<p class="mt-3 text-xs text-accent">Verifikasi pembayaran terlebih dahulu agar pesanan dapat diproses.</p>@endif
            @endif
        </section>
    </div>
</div>
@endsection
