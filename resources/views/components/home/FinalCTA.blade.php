@php
    $sections = null;
    foreach ($appSections as $item) {
        if ($item['name'] == 'FinalCTA') {
            $sections = $item;
            break;
        }
    }

    $title = $sections->title ?? 'Dijital dünyanı tek bağlantıda birleştir.';
    $description = $sections->description
        ?? 'LinkProfilde profilini dakikalar içinde oluştur ve paylaşmaya başla.';
@endphp

<section id="final-cta" class="py-20 sm:py-24 bg-slate-900">
    <div class="max-w-[800px] w-full mx-auto px-4 text-center @if($customize && $sections) home-edit @endif" data-aos="fade-up">
        @if ($customize && $sections)
            @include('components.icons.edit-pen', ['class' => 'w-8 h-8', 'dialog' => 'finalCtaSection'])
        @endif

        <h2 class="home-section-title text-white font-bold tracking-tight">
            {{ $title }}
        </h2>
        <p class="mt-4 text-slate-300 text-base sm:text-lg">
            {{ $description }}
        </p>

        <div class="mt-8">
            @if (auth()->check())
                <a
                    href="/dashboard"
                    class="inline-flex justify-center items-center w-full sm:w-auto py-3.5 px-8 rounded-lg bg-white font-semibold text-slate-900 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900 transition-colors"
                >
                    {{ __('Yönetim Paneli') }}
                </a>
            @else
                <a
                    href="/register"
                    class="inline-flex justify-center items-center w-full sm:w-auto py-3.5 px-8 rounded-lg bg-white font-semibold text-slate-900 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900 transition-colors"
                >
                    {{ __('Ücretsiz Başla') }}
                </a>
            @endif
        </div>
    </div>

    @if ($customize && $sections)
        @include('components.home-edit.edit_section', ['dialog' => 'finalCtaSection'])
    @endif
</section>
