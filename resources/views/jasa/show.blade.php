@extends('layouts.app')

@section('title', $jasa->nama)
@section('meta_description', \Illuminate\Support\Str::limit($jasa->deskripsi ?? $jasa->nama, 155))

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <x-breadcrumb :items="['Jasa Pemasangan' => route('jasa.index'), $jasa->nama => null]" />
    <div class="grid gap-8 lg:grid-cols-[1fr_20rem]">
        <div class="card p-6 sm:p-8">
            <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-accent-soft text-accent"><x-icon name="wrench" size="h-7 w-7" /></span>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-ink-900">{{ $jasa->nama }}</h1>
            <div class="mt-4 text-sm leading-relaxed text-ink-600">{!! nl2br(e($jasa->deskripsi ?? 'Belum ada deskripsi.')) !!}</div>
            <ul class="mt-6 space-y-2 text-sm text-ink-700">
                <li class="flex gap-2"><x-icon name="map-pin" class="text-brand-600" /> Pemasangan di tempat: {{ $site['alamat'] }}</li>
                <li class="flex gap-2"><x-icon name="clock" class="text-brand-600" /> Durasi pemasangan ± {{ $jasa->durasi_label }}</li>
                <li class="flex gap-2"><x-icon name="calendar" class="text-brand-600" /> Pilih jadwal yang tersedia saat booking</li>
            </ul>
        </div>
        <aside class="card h-fit p-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-ink-500">Biaya Jasa</p>
            <p class="mt-1 text-3xl font-extrabold text-ink-900">{{ rupiah($jasa->harga) }}</p>
            <p class="mt-1 text-xs text-ink-500">Di luar harga produk (jika membeli produk)</p>
            @if (! auth()->check())
                <a href="{{ route('login') }}" class="btn btn-primary mt-5 w-full">Masuk untuk Booking</a>
            @elseif (auth()->user()->isUser())
                <a href="{{ route('user.services.booking', ['jasa' => $jasa->id_services]) }}" class="btn btn-primary mt-5 w-full"><x-icon name="calendar" size="h-4 w-4" /> Booking Sekarang</a>
            @else
                <p class="mt-4 text-xs text-ink-500">Booking hanya tersedia untuk akun pelanggan.</p>
            @endif
        </aside>
    </div>

    @if ($lainnya->isNotEmpty())
        <h2 class="mt-12 text-xl font-bold text-ink-900">Jasa Lainnya</h2>
        <div class="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">@foreach ($lainnya as $j)<x-service-card :jasa="$j" />@endforeach</div>
    @endif
</div>
@endsection
