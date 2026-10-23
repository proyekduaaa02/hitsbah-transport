@extends('layouts.super-admin')
@section('title', 'Pengaturan Sistem')

@section('content')
<form method="POST" action="{{ route('superadmin.settings.update') }}" enctype="multipart/form-data" novalidate class="mx-auto max-w-4xl space-y-6">
    @csrf @method('PUT')
    <section class="card space-y-4 p-5 sm:p-6">
        <div><h2 class="font-semibold text-ink-900">Identitas Usaha</h2><p class="text-xs text-ink-500">Ditampilkan di navbar, footer, dan judul halaman website.</p></div>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-field name="site_name" label="Nama Website" :value="old('site_name', $setting['site_name'] ?? '')" required />
            <x-field name="nama_usaha" label="Nama Usaha" :value="old('nama_usaha', $setting['nama_usaha'] ?? '')" required />
        </div>
        <div>
            <span class="form-label">Logo</span>
            <div class="flex items-center gap-4" x-data="{ prev: @js(! empty($setting['logo']) ? asset('storage/'.$setting['logo']) : null) }">
                <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-xl border border-ink-100 bg-ink-50"><template x-if="prev"><img :src="prev" alt="Logo" class="h-full w-full object-contain"></template><x-icon name="photo" size="h-6 w-6" class="text-ink-300" x-show="!prev" /></div>
                <input type="file" name="logo" accept="image/*" @change="const f = $event.target.files[0]; if (f) prev = URL.createObjectURL(f)" class="block w-full text-sm file:mr-3 file:rounded-lg file:border-0 file:bg-ink-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-ink-700">
            </div>
            @error('logo')<p class="form-error">{{ $message }}</p>@enderror
        </div>
        <x-field name="deskripsi" type="textarea" rows="3" label="Deskripsi Usaha" :value="old('deskripsi', $setting['deskripsi'] ?? '')" required />
    </section>

    <section class="card space-y-4 p-5 sm:p-6">
        <h2 class="font-semibold text-ink-900">Kontak &amp; Alamat</h2>
        <x-field name="alamat" type="textarea" rows="2" label="Alamat Usaha" :value="old('alamat', $setting['alamat'] ?? '')" required />
        <div class="grid gap-4 sm:grid-cols-2">
            <x-field name="kontak" label="Nomor Kontak" :value="old('kontak', $setting['kontak'] ?? '')" required />
            <x-field name="email" type="email" label="Email" :value="old('email', $setting['email'] ?? '')" required />
        </div>
    </section>

    <section class="card space-y-4 p-5 sm:p-6">
        <div><h2 class="font-semibold text-ink-900">Operasional Pemasangan &amp; Pembayaran</h2><p class="text-xs text-ink-500">Dipakai untuk menentukan jadwal pemasangan yang tersedia dan petunjuk pembayaran.</p></div>
        <div class="grid gap-4 sm:grid-cols-3">
            <x-field name="jam_buka" type="time" label="Jam Buka" :value="old('jam_buka', $setting['jam_buka'] ?? '08:00')" required />
            <x-field name="jam_tutup" type="time" label="Jam Tutup" :value="old('jam_tutup', $setting['jam_tutup'] ?? '16:00')" required />
            <x-field name="kapasitas_pemasangan" type="number" min="1" max="20" label="Kapasitas Bersamaan" hint="Jumlah pemasangan di jam yang sama." :value="old('kapasitas_pemasangan', $setting['kapasitas_pemasangan'] ?? 2)" required />
        </div>
        <x-field name="info_pembayaran" type="textarea" rows="3" label="Petunjuk Pembayaran (QRIS / e-wallet)" hint="Tampil di halaman pembayaran pelanggan." :value="old('info_pembayaran', $setting['info_pembayaran'] ?? '')" />
    </section>

    <div class="flex justify-end"><button type="submit" class="btn btn-primary px-8">Simpan Pengaturan</button></div>
</form>
@endsection
