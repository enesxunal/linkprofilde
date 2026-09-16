@extends('blog.layout')

@section('title', $post->meta_title ?: $post->title . ' | LinkProfilde')
@section('meta_description', $post->meta_description ?: $post->excerpt)
@section('canonical', url('/blog/' . $post->slug))
@section('meta')
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $post->meta_title ?: $post->title }}">
<meta property="og:description" content="{{ $post->meta_description ?: $post->excerpt }}">
<meta property="og:url" content="{{ url('/blog/' . $post->slug) }}">
<meta property="og:image" content="{{ asset('assets/icons/link-drop.png') }}">
<meta property="article:published_time" content="{{ optional($post->published_at)->toAtomString() }}">
<meta property="article:modified_time" content="{{ optional($post->updated_at)->toAtomString() }}">
<meta name="twitter:card" content="summary_large_image">
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $post->title,
    'description' => $post->meta_description ?: $post->excerpt,
    'datePublished' => optional($post->published_at)->toAtomString(),
    'dateModified' => optional($post->updated_at)->toAtomString(),
    'mainEntityOfPage' => url('/blog/' . $post->slug),
    'author' => ['@type' => 'Organization', 'name' => $post->author_name ?: 'LinkProfilde Editör Ekibi'],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'LinkProfilde',
        'url' => url('/'),
        'logo' => ['@type' => 'ImageObject', 'url' => asset('assets/icons/link-drop.png')],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana Sayfa', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => url('/blog')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title, 'item' => url('/blog/' . $post->slug)],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@section('content')
<main>
    <article class="mx-auto max-w-4xl px-4 py-12 sm:px-6 sm:py-16">
        <nav class="text-sm text-slate-500" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:text-slate-900">Ana Sayfa</a>
            <span class="mx-2">/</span>
            <a href="{{ url('/blog') }}" class="hover:text-slate-900">Blog</a>
        </nav>

        <header class="mt-8 border-b border-slate-200 pb-8">
            @if($post->focus_keyword)
                <p class="text-sm font-semibold text-blue-600">{{ $post->focus_keyword }}</p>
            @endif
            <h1 class="mt-3 text-4xl font-bold tracking-tight text-slate-950 sm:text-5xl">{{ $post->title }}</h1>
            <p class="mt-5 text-lg leading-8 text-slate-600">{{ $post->excerpt }}</p>
            <div class="mt-6 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-500">
                <span>{{ $post->author_name ?: 'LinkProfilde Editör Ekibi' }}</span>
                <span>{{ optional($post->published_at)->format('d.m.Y') }}</span>
                @if($post->updated_at && $post->published_at && $post->updated_at->gt($post->published_at))
                    <span>Güncellendi: {{ $post->updated_at->format('d.m.Y') }}</span>
                @endif
            </div>
        </header>

        <div class="blog-prose mt-10">
            {!! $safeContent !!}
        </div>

        <aside class="mt-12 rounded-2xl border border-blue-100 bg-blue-50 p-6">
            <h2 class="text-xl font-bold text-slate-950">Linklerini tek panelden yönetmek ister misin?</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600">Bio link profilini oluştur, QR kodlarını paylaş, kısa linklerini yönet ve etkileşimleri tek panelden takip et.</p>
            <a href="{{ url('/register') }}" class="mt-5 inline-flex rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Ücretsiz Başla</a>
        </aside>

        @if($related->count())
            <section class="mt-14 border-t border-slate-200 pt-10">
                <h2 class="text-2xl font-bold text-slate-950">İlgili rehberler</h2>
                <div class="mt-6 grid gap-4 sm:grid-cols-3">
                    @foreach($related as $item)
                        <a href="{{ url('/blog/' . $item->slug) }}" class="rounded-xl border border-slate-200 bg-white p-4 hover:border-blue-300">
                            <p class="text-sm font-semibold leading-6 text-slate-900">{{ $item->title }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </article>
</main>
@endsection
