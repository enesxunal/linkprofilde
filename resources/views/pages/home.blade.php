<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $app->description }}">
        <meta name="robots" content="index,follow,max-image-preview:large">

        <title>{{ $app->title }} — Tek link. Tüm dijital dünyan.</title>
        <link rel="canonical" href="{{ url('/') }}">
        <link rel="icon" type="image/png" href="{{ asset('assets/icons/link-drop.png') }}">

        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ $app->title }}">
        <meta property="og:title" content="{{ $app->title }} — Tek link. Tüm dijital dünyan.">
        <meta property="og:description" content="{{ $app->description }}">
        <meta property="og:url" content="{{ url('/') }}">
        @if(!empty($app->logo))
            <meta property="og:image" content="{{ asset($app->logo) }}">
        @endif
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $app->title }} — Tek link. Tüm dijital dünyan.">
        <meta name="twitter:description" content="{{ $app->description }}">
        @if(!empty($app->logo))
            <meta name="twitter:image" content="{{ asset($app->logo) }}">
        @endif

        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'Organization',
                        '@id' => url('/') . '#organization',
                        'name' => $app->title,
                        'url' => url('/'),
                        'logo' => asset($app->logo ?? 'assets/icons/link-drop.png'),
                        'description' => $app->description,
                    ],
                    [
                        '@type' => 'WebSite',
                        '@id' => url('/') . '#website',
                        'name' => $app->title,
                        'url' => url('/'),
                        'publisher' => ['@id' => url('/') . '#organization'],
                        'inLanguage' => 'tr-TR',
                    ],
                    [
                        '@type' => 'SoftwareApplication',
                        'name' => $app->title,
                        'url' => url('/'),
                        'description' => $app->description,
                        'applicationCategory' => 'BusinessApplication',
                        'operatingSystem' => 'Web',
                        'publisher' => ['@id' => url('/') . '#organization'],
                        'offers' => [
                            '@type' => 'Offer',
                            'price' => '0',
                            'priceCurrency' => 'TRY',
                        ],
                    ],
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>

        <link rel="stylesheet" href="{{ asset('style/aos.css') }}">
        <link rel="stylesheet" href="{{ asset('style/swiper-slider.css') }}">
        <link rel="stylesheet" href="{{ asset('style/toastify.css') }}">

        @vite(['resources/css/app.css'])

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
