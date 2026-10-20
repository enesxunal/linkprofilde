<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $page['title'] }}</title>
    <meta name="description" content="{{ $page['description'] }}">
    <meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
    <link rel="canonical" href="{{ url('/' . $page['slug']) }}">
    <link rel="icon" href="{{ asset('assets/icons/link-drop.png') }}" type="image/png">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $app->title ?? 'LinkProfilde' }}">
    <meta property="og:title" content="{{ $page['title'] }}">
    <meta property="og:description" content="{{ $page['description'] }}">
    <meta property="og:url" content="{{ url('/' . $page['slug']) }}">
    @if(!empty($app->logo))
        <meta property="og:image" content="{{ asset($app->logo) }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $page['title'] }}">
    <meta name="twitter:description" content="{{ $page['description'] }}">
    @if(!empty($app->logo))
        <meta name="twitter:image" content="{{ asset($app->logo) }}">
    @endif

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebPage',
                    '@id' => url('/' . $page['slug']) . '#webpage',
                    'url' => url('/' . $page['slug']),
                    'name' => $page['title'],
                    'description' => $page['description'],
                    'inLanguage' => 'tr-TR',
                    'isPartOf' => [
                        '@type' => 'WebSite',
                        '@id' => url('/') . '#website',
                        'url' => url('/'),
                        'name' => $app->title ?? 'LinkProfilde',
                    ],
                ],
                [
                    '@type' => 'SoftwareApplication',
                    'name' => $app->title ?? 'LinkProfilde',
                    'url' => url('/' . $page['slug']),
                    'description' => $page['description'],
                    'applicationCategory' => 'BusinessApplication',
                    'operatingSystem' => 'Web',
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => '0',
                        'priceCurrency' => 'TRY',
                    ],
                ],
                [
                    '@type' => 'FAQPage',
                    'mainEntity' => collect($page['faq'])->map(fn ($item) => [
                        '@type' => 'Question',
                        'name' => $item['q'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $item['a'],
                        ],
                    ])->values()->all(),
                ],
                [
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        [
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => 'Ana Sayfa',
                            'item' => url('/'),
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => $page['eyebrow'],
                            'item' => url('/' . $page['slug']),
                        ],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>

    @vite(['resources/css/app.css'])
</head>
<body class="bg-white text-slate-900 antialiased">
<header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 font-semibold">
            <img src="{{ asset($app->logo ?? 'assets/icons/link-drop.png') }}" alt="LinkProfilde logo" class="h-9 w-9 rounded-lg object-cover" width="36" height="36">
            <span>LinkProfilde</span>
        </a>
        <nav class="flex items-center gap-2 text-sm sm:gap-5">
            <a href="{{ url('/bio-link') }}" class="hidden text-slate-600 hover:text-slate-900 md:inline">Bio Link</a>
            <a href="{{ url('/dijital-kartvizit') }}" class="hidden text-slate-600 hover:text-slate-900 md:inline">Dijital Kartvizit</a>
            <a href="{{ url('/qr-kod') }}" class="hidden text-slate-600 hover:text-slate-900 md:inline">QR Kod</a>
            <a href="{{ url('/kisa-link') }}" class="hidden text-slate-600 hover:text-slate-900 md:inline">Kısa Link</a>
            <a href="{{ url('/blog') }}" class="hidden text-slate-600 hover:text-slate-900 sm:inline">Blog</a>
            <a href="{{ url('/register') }}" class="rounded-lg bg-blue-600 px-3.5 py-2 font-semibold text-white hover:bg-blue-700">Ücretsiz Başla</a>
        </nav>
    </div>
</header>

<main>
    <section class="border-b border-slate-100 bg-gradient-to-b from-slate-50 to-white">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-24">
            <div class="max-w-3xl">
                <div class="mb-5 inline-flex rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-sm font-semibold text-blue-700">{{ $page['eyebrow'] }}</div>
                <h1 class="text-4xl font-bold tracking-tight text-slate-950 sm:text-5xl">{{ $page['h1'] }}</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">{{ $page['lead'] }}</p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ url('/register') }}" class="rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white shadow-sm hover:bg-blue-700">Ücretsiz Başla</a>
                    <a href="#nasil-calisir" class="rounded-xl border border-slate-300 px-5 py-3 font-semibold text-slate-800 hover:bg-slate-50">Nasıl çalışır?</a>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-20">
        <div class="grid gap-5 md:grid-cols-3">
            @foreach($page['benefits'] as $benefit)
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-slate-950">{{ $benefit['title'] }}</h2>
                    <p class="mt-3 leading-7 text-slate-600">{{ $benefit['text'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section id="nasil-calisir" class="bg-slate-50">
        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-2">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-blue-700">Nasıl çalışır?</p>
                <h2 class="mt-3 text-3xl font-bold tracking-tight">Dört adımda kullanmaya başla</h2>
                <div class="mt-8 space-y-5">
                    @foreach($page['steps'] as $index => $step)
                        <div class="flex gap-4">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">{{ $index + 1 }}</div>
                            <p class="pt-1 text-slate-700">{{ $step }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">Kimler için?</p>
                <h2 class="mt-3 text-2xl font-bold">Farklı kullanım senaryolarına uygun</h2>
                <ul class="mt-6 space-y-4">
                    @foreach($page['use_cases'] as $useCase)
                        <li class="flex gap-3 text-slate-700">
                            <span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-blue-600"></span>
                            <span>{{ $useCase }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    @if($posts->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-20">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-blue-700">Rehberler</p>
                    <h2 class="mt-3 text-3xl font-bold tracking-tight">{{ $page['eyebrow'] }} hakkında daha fazla öğren</h2>
                </div>
                <a href="{{ url('/blog') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-800">Tüm rehberler →</a>
            </div>
            <div class="mt-8 grid gap-5 md:grid-cols-2">
                @foreach($posts as $post)
                    <a href="{{ url('/blog/' . $post->slug) }}" class="rounded-2xl border border-slate-200 p-6 transition hover:border-blue-200 hover:shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-950">{{ $post->title }}</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $post->excerpt }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="border-y border-slate-200 bg-slate-50">
        <div class="mx-auto max-w-4xl px-4 py-16 sm:px-6 sm:py-20">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-700">Sık sorulan sorular</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight">{{ $page['eyebrow'] }} hakkında merak edilenler</h2>
            <div class="mt-8 divide-y divide-slate-200 rounded-2xl border border-slate-200 bg-white px-6">
                @foreach($page['faq'] as $item)
                    <article class="py-6">
                        <h3 class="font-semibold text-slate-950">{{ $item['q'] }}</h3>
                        <p class="mt-2 leading-7 text-slate-600">{{ $item['a'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-20">
        <div class="rounded-3xl bg-slate-950 px-6 py-10 text-white sm:px-10">
            <h2 class="text-3xl font-bold">LinkProfilde ile ücretsiz başla</h2>
            <p class="mt-3 max-w-2xl text-slate-300">{{ $page['eyebrow'] }} çözümünü diğer LinkProfilde araçlarıyla aynı panelde yönet.</p>
            <a href="{{ url('/register') }}" class="mt-6 inline-flex rounded-xl bg-white px-5 py-3 font-semibold text-slate-950 hover:bg-slate-100">Hesap Oluştur</a>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-16 sm:px-6 sm:pb-20">
        <h2 class="text-2xl font-bold tracking-tight">Diğer LinkProfilde araçları</h2>
        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            @foreach($related as $item)
                <a href="{{ url('/' . $item['slug']) }}" class="rounded-2xl border border-slate-200 p-5 hover:border-blue-200 hover:bg-slate-50">
                    <p class="font-semibold text-slate-950">{{ $item['eyebrow'] }}</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item['description'] }}</p>
                </a>
            @endforeach
        </div>
    </section>
</main>

<footer class="border-t border-slate-200 bg-white">
    <div class="mx-auto flex max-w-6xl flex-col justify-between gap-4 px-4 py-8 text-sm text-slate-600 sm:px-6 md:flex-row">
        <p>© {{ date('Y') }} LinkProfilde. Tüm hakları saklıdır.</p>
        <div class="flex flex-wrap gap-x-5 gap-y-2">
            <a href="{{ url('/bio-link') }}" class="hover:text-slate-900">Bio Link</a>
            <a href="{{ url('/dijital-kartvizit') }}" class="hover:text-slate-900">Dijital Kartvizit</a>
            <a href="{{ url('/qr-kod') }}" class="hover:text-slate-900">QR Kod</a>
            <a href="{{ url('/kisa-link') }}" class="hover:text-slate-900">Kısa Link</a>
            <a href="{{ url('/blog') }}" class="hover:text-slate-900">Blog</a>
        </div>
    </div>
</footer>
<script src="{{ asset('3kare/signature.js') }}" defer></script>
</body>
</html>
