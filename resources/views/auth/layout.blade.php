<!DOCTYPE html>
@php
    $appTitle = $app->title ?? config('app.name', 'LinkProfilde');
    $appLogo = $app->logo ?? 'assets/icons/link-drop.png';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex,nofollow">

        <title>@yield('title', $appTitle)</title>
        <link rel="icon" type="image/png" href="{{ asset('assets/icons/link-drop.png') }}">

        @vite(['resources/css/app.css'])
    </head>

    <body class="auth-landing min-h-screen text-slate-800 antialiased bg-slate-50">
        <div class="pointer-events-none fixed inset-0 overflow-hidden" aria-hidden="true">
            <div class="absolute -top-24 right-0 h-[420px] w-[420px] rounded-full bg-blue-100 blur-3xl opacity-70"></div>
            <div class="absolute bottom-0 left-0 h-[320px] w-[320px] rounded-full bg-slate-200 blur-3xl opacity-80"></div>
        </div>

        <header class="relative z-10 border-b border-slate-200 bg-white/90 backdrop-blur-md">
            <div class="mx-auto flex max-w-[1200px] items-center justify-between px-4 py-3">
                <a href="/" class="flex min-w-0 items-center gap-2.5 rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                    <img
                        src="{{ asset($appLogo) }}"
                        alt="{{ $appTitle }} logo"
                        width="40"
                        height="40"
                        class="h-10 w-10 shrink-0 rounded-lg object-cover"
                    >
                    <span class="truncate text-base font-semibold text-slate-800 sm:text-lg">{{ $appTitle }}</span>
                </a>

                <a
                    href="/"
                    class="inline-flex items-center rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    {{ __('Ana Sayfa') }}
                </a>
            </div>
        </header>

        <main class="relative z-10 flex min-h-[calc(100vh-65px)] items-center justify-center px-4 py-10 sm:py-14">
            @yield('content')
        </main>
    </body>
</html>
