@php
    $sections = null;
    foreach ($appSections as $item) {
        if ($item['name'] == 'Create Link') {
            $sections = $item;
            break;
        }
    }

    $title = $sections->title ?? 'Kendine ait dijital profilini oluştur.';
    $description = $sections->description ?? 'Temalar, sosyal bağlantılar ve içerik bloklarıyla profesyonel bir bio link sayfası hazırla.';

    $defaultFeatures = [
        '~40 hazır tema',
        'Sosyal medya ve iletişim bağlantıları',
        'Link, Heading, Paragraph, Image blokları',
        'YouTube, Spotify, Vimeo, TikTok, SoundCloud',
        'Sürükle-bırak sıralama',
        'Özel tema (plana göre)',
        'Telefon varsa rehbere ekleme / vCard',
        'NFC kartını LinkProfilde profilinle kullan',
    ];

    $list = $sections && $sections->section_list
        ? json_decode($sections->section_list, true)
        : [];

    $features = [];
    if (is_array($list) && count($list) > 0) {
        foreach ($list as $row) {
            if (!empty($row['content'])) {
                $features[] = $row['content'];
            }
        }
    }
    if (count($features) === 0) {
        $features = $defaultFeatures;
    }
@endphp

<section id="bio-link" class="py-20 sm:py-24 bg-slate-50">
    <div class="max-w-[1200px] w-full mx-auto px-4">
        <div class="@if($customize) home-edit @endif">
            @if ($customize && $sections)
                @include('components.icons.edit-pen', ['class' => 'w-8 h-8', 'dialog' => 'createLinkSection'])
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div class="relative order-2 lg:order-1" data-aos="fade-up">
                    <div class="relative max-w-md mx-auto">
                        @include('components.home.HeroPreview')

                        <div class="absolute -left-2 top-24 hidden md:block rounded-xl bg-white border border-slate-200 shadow-sm px-3 py-2 text-xs font-medium text-slate-700" aria-hidden="true">
                            40+ tema
                        </div>
                        <div class="absolute -right-2 top-48 hidden md:block rounded-xl bg-white border border-slate-200 shadow-sm px-3 py-2 text-xs font-medium text-slate-700" aria-hidden="true">
                            Sürükle & bırak
                        </div>
                        <div class="absolute left-4 bottom-8 hidden md:block rounded-xl bg-white border border-slate-200 shadow-sm px-3 py-2 text-xs font-medium text-slate-700" aria-hidden="true">
                            Sosyal + vCard
                        </div>
                    </div>
                </div>

                <div class="order-1 lg:order-2" data-aos="fade-up" data-aos-delay="80">
                    <p class="text-sm font-semibold text-blue-600 mb-3">Bio Link</p>
                    <h2 class="home-section-title text-slate-900 font-bold tracking-tight">
                        {{ $title }}
                    </h2>
                    <p class="mt-4 text-slate-600 text-base sm:text-lg leading-relaxed">
                        {{ $description }}
                    </p>

                    <div class="mt-8 @if($customize) home-edit @endif">
                        @if ($customize && $sections)
                            @include('components.icons.edit-pen', ['class' => 'w-[18px] h-[18px]', 'dialog' => 'createLinkSectionList'])
                        @endif

                        <ul class="space-y-3">
                            @foreach ($features as $feature)
                                <li class="flex items-start gap-3 text-slate-700">
                                    @include('components.icons.circle-check', ['class' => 'w-5 h-5 text-blue-600 mt-0.5 shrink-0'])
                                    <span class="text-sm sm:text-base">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-8">
                        <a
                            href="{{ auth()->check() ? '/bio-links' : '/register' }}"
                            class="inline-flex justify-center items-center w-full sm:w-auto py-3 px-6 rounded-lg bg-blue-600 font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 transition-colors"
                        >
                            {{ __('Profil Oluştur') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($customize && $sections)
        @include('components.home-edit.edit_section', ['dialog' => 'createLinkSection'])
        @include('components.home-edit.edit_section_list', ['dialog' => 'createLinkSectionList'])
    @endif
</section>
