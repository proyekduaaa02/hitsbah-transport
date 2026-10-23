@extends('layouts.app')
@section('title', 'Booking Jasa Pemasangan')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
    @include('user.partials.subnav')
    <h1 class="text-2xl font-bold text-ink-900">Booking Jasa Pemasangan</h1>
    <p class="mt-1 text-sm text-ink-500">Pilih jasa dan jadwal yang tersedia. Setelah booking, lanjutkan pembayaran agar jadwal dikonfirmasi admin.</p>

    <form method="POST" action="{{ route('user.services.booking.store') }}" novalidate class="card mt-6 p-5 sm:p-6"
          x-data="jadwalPicker({ url: @js(route('user.services.slots')), idServices: @js(old('id_services', $dipilih)), tanggal: @js(old('tanggal', '')), jam: @js(old('jam', '')), harga: @js($jasa->pluck('harga', 'id_services')) })">
        @csrf
        @include('user.partials.schedule-picker', ['jasa' => $jasa, 'opsional' => false])
        <div class="mt-6 flex items-center justify-between gap-3 border-t border-ink-100 pt-5">
            <p class="text-sm text-ink-600" x-show="hargaJasa > 0" x-cloak>Biaya jasa: <strong class="text-ink-900" x-text="rupiah(hargaJasa)"></strong></p>
            <button type="submit" class="btn btn-primary ml-auto px-6 py-3" :disabled="!idServices || !tanggal || !jam">Buat Booking</button>
        </div>
    </form>
</div>
@endsection
