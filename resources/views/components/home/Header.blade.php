@php
    $sections = null;
    foreach ($appSections as $item) {
        if ($item['name'] == 'Header') {
            $sections = $item;
            break;
        }
    }
    $heroTitle = $sections?->title ?: 'Tek link. Tüm dijital dünyan.';
    $heroDescription = trim((string) ($sections?->description ?? '')) !== ''
        ? $sections->description
        : 'Profilini oluştur, linklerini yönet, QR kodlarını paylaş ve ziyaretçilerini tek panelden analiz et.';
@endphp

<section id="home" class="relative pt-[88px] sm:pt-[100px] pb-16 sm:pb-24 bg-slate-50 overflow-hidden">
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <div class="absolute -top-24 right-0 w-[420px] h-[420px] rounded-full bg-blue-100/50 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-[320px] h-[320px] rounded-full bg-slate-200/60 blur-3xl"></div>
    </div>

    <div class="relative max-w-[1200px] w-full mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
            <div class="@if($customize) home-edit @endif" data-aos="fade-up">
                @if ($customize)
                    @include('components.icons.edit-pen', ['class' => 'w-8 h-8', 'dialog' => 'homeSection'])
                @endif

                <p class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1 text-xs sm:text-sm font-medium text-slate-600 mb-5">
                    Bio Link · Kısa Link · QR · Analytics
                </p>

                <h1 class="home-hero-title text-slate-900 font-bold tracking-tight">
                    {{ $heroTitle }}
                </h1>

                <p class="mt-5 text-base sm:text-lg text-slate-600 max-w-xl leading-relaxed">
                    {{ $heroDescription }}
                </p>

                <div class="mt-8 flex flex-col sm:flex-row gap-3 sm:gap-4">
                    @if (auth()->user())
                        <a
                            href="/dashboard"
                            class="inline-flex justify-center items-center py-3 px-6 rounded-lg bg-blue-600 font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 transition-colors"
                        >
                            {{ __('Yönetim Paneli') }}
                        </a>
                    @else
                        <a
                            href="/register"
                            class="inline-flex justify-center items-center py-3 px-6 rounded-lg bg-blue-600 font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 transition-colors"
                        >
                            {{ __('Ücretsiz Başla') }}
                        </a>
                    @endif
                    <a
                        href="#features"
                        class="inline-flex justify-center items-center py-3 px-6 rounded-lg border border-slate-300 bg-white font-semibold text-slate-800 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 transition-colors"
                    >
                        {{ __('Özellikleri Keşfet') }}
                    </a>
                </div>

                <p class="mt-5 text-sm text-slate-500">
                    Dakikalar içinde profilini oluştur.
                </p>

                @if ($sections && $sections->section_list)
                    <div class="mt-8 @if($customize) home-edit @endif">
                        @if ($customize)
                            @include('components.icons.edit-pen', ['class' => 'w-[18px] h-[18px]', 'dialog' => 'homeSectionList'])
                        @endif
                        <div class="flex flex-wrap gap-2">
                            @foreach (json_decode($sections->section_list) as $list)
                                @php $item = (array) $list; @endphp
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600">
                                    @if (!empty($item['icon']))
                                        @include('components.icons.'.$item['icon'], ['class' => 'w-3.5 h-3.5 text-blue-600'])
                                    @endif
                                    {{ $item['content'] }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="relative flex justify-center lg:justify-end" data-aos="fade-up" data-aos-delay="100">
                @include('components.home.HeroPreview')
            </div>
        </div>
    </div>

    @if ($customize && $sections)
        @include('components.home-edit.edit_section', ['dialog' => 'homeSection'])
        @include('components.home-edit.edit_section_list', ['dialog' => 'homeSectionList'])
    @endif
</section>
