@extends('layouts.app')

@section('title', 'Daftar Akun')

@section('content')
<div class="grid min-h-[calc(100vh-4rem)] lg:grid-cols-2">
    @include('auth._visual', ['judul' => 'Bergabung dengan ' . $site['site_name'], 'teks' => 'Buat akun gratis untuk berbelanja aksesoris mobil dan memesan jasa pemasangan secara online.'])

    <div class="flex items-center justify-center px-4 py-12 sm:px-10">
        <div class="w-full max-w-md">
            <div class="anim-up" style="--d: 0ms">
                <h1 class="text-3xl font-extrabold text-fg">Buat Akun Baru</h1>
                <p class="mt-1 text-sm text-muted">Daftar untuk berbelanja aksesoris dan memesan jasa pemasangan.</p>
            </div>
            <form method="POST" action="{{ route('register.store') }}" class="load-stagger mt-8 space-y-4" novalidate>
                @csrf
                <x-field name="nama" label="Nama Lengkap" :value="old('nama')" placeholder="Nama lengkap Anda" autocomplete="name" required autofocus />
                <x-field name="email" type="email" label="Email" :value="old('email')" placeholder="nama@email.com" autocomplete="email" required />
                <x-field name="alamat" type="textarea" label="Alamat" :value="old('alamat')" rows="2" placeholder="Alamat lengkap" required />
                <div class="grid gap-4 sm:grid-cols-2">
                    <x-field name="password" type="password" label="Password" hint="Minimal 8 karakter" autocomplete="new-password" required />
                    <x-field name="password_confirmation" type="password" label="Konfirmasi Password" autocomplete="new-password" required />
                </div>
                <button type="submit" class="btn btn-primary w-full py-3">Daftar</button>
            </form>
            <p class="anim-up mt-6 text-center text-sm text-muted" style="--d: 650ms">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-accent hover:underline">Masuk</a></p>
        </div>
    </div>
</div>
@endsection
