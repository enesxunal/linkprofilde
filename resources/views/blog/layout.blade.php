<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <meta name="description" content="@yield('meta_description')">
    <meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
    <link rel="canonical" href="@yield('canonical')">
    <link rel="icon" href="{{ asset('assets/icons/link-drop.png') }}" type="image/png">
    @yield('meta')
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 font-semibold text-slate-900">
            <img src="{{ asset($app->logo ?? 'assets/icons/link-drop.png') }}" alt="LinkProfilde logo" class="h-9 w-9 rounded-lg object-cover" width="36" height="36">
            <span>LinkProfilde</span>
        </a>
        <nav class="flex items-center gap-2 text-sm sm:gap-5">
            <a href="{{ url('/') }}" class="hidden text-slate-600 hover:text-slate-900 sm:inline">Ana Sayfa</a>
            <a href="{{ url('/blog') }}" class="font-semibold text-blue-600">Blog</a>
            <a href="{{ url('/login') }}" class="hidden text-slate-600 hover:text-slate-900 sm:inline">Giriş Yap</a>
            <a href="{{ url('/register') }}" class="rounded-lg bg-blue-600 px-3.5 py-2 font-semibold text-white hover:bg-blue-700">Ücretsiz Başla</a>
        </nav>
    </div>
</header>

@yield('content')

<footer class="mt-20 border-t border-slate-200 bg-white">
    <div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-2">
        <div>
            <p class="font-semibold text-slate-900">LinkProfilde</p>
            <p class="mt-2 max-w-xl text-sm leading-6 text-slate-600">Bio link, dijital profil, kısa link, QR kod ve analitik araçlarını tek panelde yönetin.</p>
        </div>
        <div class="md:text-right">
            <a href="{{ url('/blog') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">Rehberler</a>
            <p class="mt-3 text-xs text-slate-500">© {{ date('Y') }} LinkProfilde. Tüm hakları saklıdır.</p>
        </div>
    </div>
</footer>
</body>
</html>
