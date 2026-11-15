<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Perpustakaan Digital') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="auth-wrap">
            <aside class="auth-side">
                <a href="/" class="auth-brand">📚 {{ config('app.name', 'Perpustakaan Digital') }}</a>

                <div class="auth-side__isi">
                    <h1>Ruang baca digital untuk semua.</h1>
                    <p>Telusuri katalog, ajukan peminjaman, dan pantau riwayat bacamu dari mana saja.</p>
                    <div class="auth-rak" aria-hidden="true">
                        <span style="--t: 70%; --c: #fbbf24"></span><span style="--t: 90%; --c: #34d399"></span>
                        <span style="--t: 60%; --c: #60a5fa"></span><span style="--t: 100%; --c: #f472b6"></span>
                        <span style="--t: 78%; --c: #a78bfa"></span><span style="--t: 88%; --c: #fb923c"></span>
                    </div>
                </div>

                <p class="auth-side__foot">&copy; {{ date('Y') }} {{ config('app.name', 'Perpustakaan Digital') }}</p>
            </aside>

            <main class="auth-main">
                <div class="auth-card">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>