<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $kode }} - {{ $judul }} | {{ $site['site_name'] ?? 'HITSBAH Transport' }}</title>
    <meta name="robots" content="noindex">
    <meta name="theme-color" content="#ffffff">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-white px-4">
    <main class="page-in w-full max-w-lg text-center">
        <a href="{{ url('/') }}" class="inline-block"><x-logo /></a>
        <div class="mx-auto mt-10 max-w-sm"><x-hero-car class="float-slower w-full" /></div>
        <p class="anim-up mt-6 text-7xl font-extrabold tracking-tight text-accent sm:text-8xl" style="--d: 100ms">{{ $kode }}</p>
        <h1 class="anim-up mt-3 text-2xl font-bold text-fg" style="--d: 180ms">{{ $judul }}</h1>
        <p class="anim-up mx-auto mt-2 max-w-md text-muted" style="--d: 240ms">{{ $pesan }}</p>
        @if (! empty($exception) && method_exists($exception, 'getMessage') && $exception->getMessage() && config('app.debug') && $kode === '403')
            <p class="mt-2 text-xs text-muted">{{ $exception->getMessage() }}</p>
        @endif
        <div class="anim-up mt-8 flex flex-wrap justify-center gap-3" style="--d: 320ms">
            <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Beranda</a>
            <button type="button" onclick="history.length > 1 ? history.back() : location.assign('/')" class="btn btn-outline">Halaman Sebelumnya</button>
        </div>
    </main>
</body>
</html>
