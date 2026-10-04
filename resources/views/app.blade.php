<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @php
            $isPublicProfile = ($page['component'] ?? null) === 'BioLinks/View' && !empty($page['props']['link']);
            $publicProfileSeo = null;

            if ($isPublicProfile) {
                $profile = $page['props']['link'];
                $appProps = $page['props']['app'] ?? [];
                $appTitle = is_array($appProps)
                    ? ($appProps['title'] ?? config('app.name', 'LinkProfilde'))
                    : ($appProps->title ?? config('app.name', 'LinkProfilde'));

                $profileName = trim((string) ($profile['link_name'] ?? ''));
                $slug = ltrim((string) ($profile['url_name'] ?? ''), '/');
                $bio = preg_replace('/\s+/u', ' ', trim((string) ($profile['short_bio'] ?? '')));
                $description = $bio !== ''
                    ? \Illuminate\Support\Str::limit($bio, 155, '...')
                    : $profileName . ' için sosyal medya, iletişim ve önemli bağlantılara tek sayfadan ulaşın.';

                $items = $profile['items'] ?? [];
                $thumbnail = trim((string) ($profile['thumbnail'] ?? ''));
                $socialsRaw = trim((string) ($profile['socials'] ?? ''));
                $hasSocials = !in_array($socialsRaw, ['', '[]', '{}', 'null'], true);
                $hasIndexableContent = !empty($items)
                    || mb_strlen($bio) >= 20
                    || $thumbnail !== ''
                    || $hasSocials;

                $canonical = url('/' . $slug);
                $image = $thumbnail !== '' ? asset(ltrim($thumbnail, '/')) : null;

                $publicProfileSeo = [
                    'title' => $profileName . ' | Bağlantılar ve İletişim | ' . $appTitle,
                    'description' => $description,
                    'canonical' => $canonical,
                    'robots' => $hasIndexableContent ? 'index,follow,max-image-preview:large' : 'noindex,follow',
                    'image' => $image,
                    'schema' => [
                        '@context' => 'https://schema.org',
                        '@type' => 'ProfilePage',
                        'name' => $profileName . ' | Bağlantılar ve İletişim | ' . $appTitle,
                        'description' => $description,
                        'url' => $canonical,
                        'mainEntity' => [
                            '@type' => 'Thing',
                            'name' => $profileName,
                        ],
                    ],
                ];

                if ($image) {
                    $publicProfileSeo['schema']['primaryImageOfPage'] = [
                        '@type' => 'ImageObject',
                        'url' => $image,
                    ];
                    $publicProfileSeo['schema']['mainEntity']['image'] = $image;
                }
            }
        @endphp

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @if($publicProfileSeo)
            <title inertia>{{ $publicProfileSeo['title'] }}</title>
            <meta name="description" content="{{ $publicProfileSeo['description'] }}">
            <meta name="robots" content="{{ $publicProfileSeo['robots'] }}">
            <link rel="canonical" href="{{ $publicProfileSeo['canonical'] }}">
            <meta property="og:type" content="profile">
            <meta property="og:title" content="{{ $publicProfileSeo['title'] }}">
            <meta property="og:description" content="{{ $publicProfileSeo['description'] }}">
            <meta property="og:url" content="{{ $publicProfileSeo['canonical'] }}">
            @if($publicProfileSeo['image'])
                <meta property="og:image" content="{{ $publicProfileSeo['image'] }}">
            @endif
            <meta name="twitter:card" content="{{ $publicProfileSeo['image'] ? 'summary_large_image' : 'summary' }}">
            <meta name="twitter:title" content="{{ $publicProfileSeo['title'] }}">
            <meta name="twitter:description" content="{{ $publicProfileSeo['description'] }}">
            @if($publicProfileSeo['image'])
                <meta name="twitter:image" content="{{ $publicProfileSeo['image'] }}">
            @endif
            <script type="application/ld+json">{!! json_encode($publicProfileSeo['schema'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
        @else
            <title inertia>{{ config('app.name', 'LinkProfilde') }}</title>
        @endif
        <link rel="icon" type="image/png" href="{{ asset('assets/icons/link-drop.png') }}">

        @routes
        @viteReactRefresh
        @vite(['resources/js/app.tsx', "resources/js/Pages/{$page['component']}.tsx"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia

        <script async src="https://www.tiktok.com/embed.js"></script>
    </body>
</html>
