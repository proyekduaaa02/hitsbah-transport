<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    @include('layouts.head')
    @stack('head')
</head>
<body class="flex min-h-screen flex-col bg-background">
    <a href="#konten" class="sr-only focus:not-sr-only focus:absolute focus:left-2 focus:top-2 focus:z-[200] focus:rounded-lg focus:bg-white focus:px-3 focus:py-2 focus:shadow">Lewati ke konten</a>
    <x-navbar />
    <x-toast />

    <main id="konten" class="page-in flex-1">
        @yield('content')
    </main>

    <x-footer />
    <x-confirm-dialog />
    @stack('scripts')
</body>
</html>
