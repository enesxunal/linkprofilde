@php
    $sections = null;
    foreach ($appSections as $item) {
        if ($item['name'] == 'Features') {
            $sections = $item;
            break;
        }
    }

    $pillarDefaults = [
        [
            'title' => 'Bio Link',
            'description' => 'Profilini, sosyal hesaplarını ve tüm önemli bağlantılarını tek sayfada paylaş.',
            'icon' => 'link',
            'href' => '#bio-link',
        ],
        [
            'title' => 'Kısa Link',
            'description' => 'Uzun bağlantılarını sadeleştir, yönet ve performanslarını takip et.',
            'icon' => 'link-slash',
            'href' => '#short-links',
        ],
        [
            'title' => 'QR Kod',
            'description' => 'Markana uygun QR kodlar oluştur, özelleştir ve indir.',
            'icon' => 'qrcode',
            'href' => '#qr',
        ],
        [
            'title' => 'Analytics',
            'description' => 'Ziyaretçi, cihaz, tarayıcı ve yönlendirme kaynaklarını analiz et.',
            'icon' => 'chart-line-up',
            'href' => '#analytics',
        ],
    ];

    $list = $sections && $sections->section_list
        ? json_decode($sections->section_list, true)
        : [];

    $pillars = [];
    foreach ($pillarDefaults as $index => $default) {
        $fromDb = is_array($list) && isset($list[$index]) ? $list[$index] : null;
        $pillars[] = [
            'title' => $fromDb['content'] ?? $default['title'],
            'description' => $default['description'],
            'icon' => $fromDb['icon'] ?? $default['icon'],
            'href' => $default['href'],
        ];
    }
@endphp

<section id="features" class="py-20 sm:py-24 bg-white">
    <div class="max-w-[1200px] w-full mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center mb-12 sm:mb-14" data-aos="fade-up">
            <h2 class="home-section-title text-slate-900 font-bold tracking-tight">
                {{ __('Dört temel ürün. Tek platform.') }}
            </h2>
            <p class="mt-4 text-slate-600 text-base sm:text-lg">
                LinkProfilde; kişisel dijital profil, link yönetimi, QR ve analytics ihtiyaçlarını aynı yerde toplar.
            </p>
        </div>

        <div class="@if($customize) home-edit @endif" data-aos="fade-up">
            @if ($customize && $sections)
                @include('components.icons.edit-pen', ['class' => 'w-8 h-8', 'dialog' => 'featuresSectionList'])
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6">
                @foreach ($pillars as $pillar)
                    <a
                        href="{{ $pillar['href'] }}"
                        class="group rounded-2xl border border-slate-200 bg-slate-50/80 p-6 hover:border-blue-200 hover:bg-white hover:shadow-md transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    >
                        <div class="h-11 w-11 rounded-xl bg-white border border-slate-200 text-blue-600 grid place-items-center mb-5 group-hover:border-blue-200">
                            @include('components.icons.'.$pillar['icon'], ['class' => 'w-5 h-5'])
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ $pillar['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $pillar['description'] }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @if ($customize && $sections)
        @include('components.home-edit.edit_section_list', ['dialog' => 'featuresSectionList'])
    @endif
</section>
