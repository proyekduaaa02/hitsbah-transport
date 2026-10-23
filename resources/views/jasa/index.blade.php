@extends('layouts.app')

@section('title', 'Jasa Pemasangan Aksesoris Mobil')
@section('meta_description', 'Daftar jasa pemasangan aksesoris mobil profesional di bengkel '.$site['site_name'].'. Lihat harga dan durasi pemasangan, lalu booking jadwal.')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    <x-breadcrumb :items="['Jasa Pemasangan' => null]" />
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div><h1 class="section-title">Jasa Pemasangan</h1><p class="mt-1 max-w-2xl text-sm text-ink-500">Seluruh pemasangan dilakukan di bengkel kami ({{ $site['alamat'] }}). Pilih jasa lalu tentukan jadwal yang tersedia.</p></div>
        <form method="GET" class="relative w-full sm:w-72">
            <x-icon name="search" size="h-5 w-5" class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-ink-500" />
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari jasa..." class="form-input pl-10" aria-label="Cari jasa">
        </form>
    </div>

    @if ($jasa->isEmpty())
        <div class="card mt-6"><x-empty-state icon="wrench" title="Jasa tidak ditemukan" text="Coba kata kunci lain." /></div>
    @else
        <div data-stagger="80" class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">@foreach ($jasa as $j)<x-service-card :jasa="$j" />@endforeach</div>
        <x-pagination :paginator="$jasa" />
    @endif
</div>
@endsection
