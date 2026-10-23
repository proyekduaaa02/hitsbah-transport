@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="grid min-h-[calc(100vh-4rem)] lg:grid-cols-2">
    @include('auth._visual', ['judul' => 'Selamat datang kembali', 'teks' => 'Masuk untuk melanjutkan belanja aksesoris dan memantau jadwal pemasangan Anda.'])

    <div class="flex items-center justify-center px-4 py-12 sm:px-10">
        <div class="w-full max-w-md">
            <div class="anim-up" style="--d: 0ms">
                <h1 class="text-3xl font-extrabold text-fg">Masuk ke Akun</h1>
                <p class="mt-1 text-sm text-muted">Selamat datang kembali di {{ $site['site_name'] }}.</p>
            </div>
            <form method="POST" action="{{ route('login.attempt') }}" class="load-stagger mt-8 space-y-4" novalidate>
                @csrf
                <x-field name="email" type="email" label="Email" :value="old('email')" placeholder="nama@email.com" autocomplete="email" required autofocus />
                <x-field name="password" type="password" label="Password" placeholder="Password Anda" autocomplete="current-password" required />
                <label class="flex items-center gap-2 text-sm text-muted"><input type="checkbox" name="remember" value="1" class="rounded border-line text-accent focus:ring-accent"> Ingat saya</label>
                <button type="submit" class="btn btn-primary w-full py-3">Masuk</button>
            </form>
            <p class="anim-up mt-6 text-center text-sm text-muted" style="--d: 600ms">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-accent hover:underline">Daftar sekarang</a></p>
        </div>
    </div>
</div>
@endsection
