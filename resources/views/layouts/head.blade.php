<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Beranda') | {{ $site['site_name'] }}</title>
<meta name="description" content="@yield('meta_description', \Illuminate\Support\Str::limit($site['deskripsi'], 150))">
<meta name="theme-color" content="#ffffff">
<meta name="application-name" content="{{ $site['site_name'] }}">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="{{ $site['site_name'] }}">
<meta property="og:title" content="@yield('title', 'Beranda') | {{ $site['site_name'] }}">
<meta property="og:description" content="@yield('meta_description', \Illuminate\Support\Str::limit($site['deskripsi'], 150))">
<meta property="og:type" content="website">
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icons/icon-192.png') }}">
<link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
<script>document.documentElement.classList.add('js');</script>
@vite(['resources/css/app.css', 'resources/js/app.js'])
