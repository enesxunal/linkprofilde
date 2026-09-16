@extends('blog.layout')

@section('title', 'LinkProfilde Blog — Bio Link, QR Kod, Kısa Link ve Dijital Kartvizit Rehberleri')
@section('meta_description', 'Bio link, dijital kartvizit, kısa link, QR kod, sosyal medya bağlantıları ve analitik hakkında uygulamalı rehberler.')
@section('canonical', url('/blog'))
@section('meta')
<meta property="og:type" content="website">
<meta property="og:title" content="LinkProfilde Blog — Dijital Profil ve Bağlantı Rehberleri">
<meta property="og:description" content="Bio link, dijital kartvizit, kısa link, QR kod ve analitik hakkında uygulamalı rehberler.">
<meta property="og:url" content="{{ url('/blog') }}">
<meta property="og:image" content="{{ asset('assets/icons/link-drop.png') }}">
<meta name="twitter:card" content="summary_large_image">
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Blog',
    'name' => 'LinkProfilde Blog',
    'url' => url('/blog'),
    'description' => 'Bio link, dijital kartvizit, kısa link, QR kod ve analitik rehberleri.',
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'LinkProfilde',
        'url' => url('/'),
        'logo' => ['@type' => 'ImageObject', 'url' => asset('assets/icons/link-drop.png')],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endsection

@section('content')
<main>
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-20">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-blue-600">LinkProfilde Rehberleri</p>
            <h1 class="mt-4 max-w-4xl text-4xl font-bold tracking-tight text-slate-950 sm:text-5xl">Bio link, QR kod, kısa link ve dijital kartvizit için net rehberler.</h1>
            <p class="mt-5 max-w-3xl text-lg leading-8 text-slate-600">Sosyal medya bağlantılarını tek yerde toplamak, QR kodlarla fiziksel trafiği ölçmek, dijital kartvizit hazırlamak ve link performansını analiz etmek için uygulamalı içerikler.</p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
        @if($posts->count())
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($posts as $post)
                    <article class="flex h-full flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">{{ $post->focus_keyword ?: 'LinkProfilde Rehberi' }}</p>
                        <h2 class="mt-3 text-xl font-bold leading-7 text-slate-950">
                            <a href="{{ url('/blog/' . $post->slug) }}" class="hover:text-blue-600">{{ $post->title }}</a>
                        </h2>
                        <p class="mt-3 flex-1 text-sm leading-6 text-slate-600">{{ $post->excerpt }}</p>
                        <div class="mt-6 flex items-center justify-between gap-3 text-xs text-slate-500">
                            <span>{{ optional($post->published_at)->format('d.m.Y') }}</span>
                            <a href="{{ url('/blog/' . $post->slug) }}" class="font-semibold text-blue-600 hover:text-blue-700">Rehberi oku →</a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-10">{{ $posts->links() }}</div>
        @else
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-600">Henüz yayınlanmış blog yazısı yok.</div>
        @endif
    </section>
</main>
@endsection
