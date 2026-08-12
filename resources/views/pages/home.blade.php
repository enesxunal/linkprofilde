<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $app->description }}">

        <title>{{ $app->title }} — Tek link. Tüm dijital dünyan.</title>

        <link rel="stylesheet" href="{{ asset('style/aos.css') }}">
        <link rel="stylesheet" href="{{ asset('style/swiper-slider.css') }}">
        <link rel="stylesheet" href="{{ asset('style/toastify.css') }}">

        @routes
        @viteReactRefresh
        @vite(['resources/js/app.tsx'])

        <script src="{{ asset('script/aos.js') }}"></script>
        <script src="{{ asset('script/swiper-slider.js') }}"></script>
        <script src="{{ asset('script/smooth-scroll.js') }}"></script>
        <script src="{{ asset('script/toastify.js') }}"></script>
    </head>

    <body class="home-landing text-slate-800 antialiased bg-white">
        @include('components.home.navbar')

        <main class="overflow-x-hidden">
            @if (session('error'))
                @include('components.Toast', ['toastType' => 'error', 'message' => session('error')])
            @endif

            @include('components.home.Header')
            @include('components.home.ProductPillars')
            @include('components.home.CreateLink')
            @include('components.home.LinkManagement')
            @include('components.home.CreateQR')
            @include('components.home.Analytics')
            @include('components.home.UseCases')
            @include('components.home.Pricing')
            @include('components.home.Testimonials')
            @include('components.home.FAQ')
            @include('components.home.FinalCTA')
            @include('components.home.Footer')
        </main>

        <script>
            AOS.init({ once: true, duration: 700, easing: 'ease-out-cubic' });
        </script>
        <script src="{{ asset('script/index.js') }}"></script>
        <script src="{{ asset('script/collapse.js') }}"></script>
        <script src="{{ asset('script/ripple.js') }}"></script>
        <script src="{{ asset('script/dialog.js') }}"></script>
        <script src="{{ asset('script/scripts-tabs.js') }}"></script>
    </body>
</html>
