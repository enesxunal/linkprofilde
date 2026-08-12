@php
    $sections = null;
    foreach ($appSections as $item) {
        if ($item['name'] == 'UseCases') {
            $sections = $item;
            break;
        }
    }

    $defaults = [
        [
            'title' => 'İçerik Üreticileri',
            'description' => 'Tüm sosyal hesaplarını, videolarını ve iş birliği linklerini tek profilde topla.',
        ],
        [
            'title' => 'Freelancerlar',
            'description' => 'Portfolyo, iletişim ve proje linklerini müşterilerine tek bağlantıyla sun.',
        ],
        [
            'title' => 'İşletmeler',
            'description' => 'Marka profili, kampanya linkleri ve QR ile fiziksel-dijital trafiği birleştir.',
        ],
        [
            'title' => 'Sosyal Medya',
            'description' => 'Instagram, TikTok ve diğer platformlardan tek sabit linkle yönlendir.',
        ],
        [
            'title' => 'Etkinlik & Fuar',
            'description' => 'Kayıt, harita ve sponsor linklerini QR ile hızlıca paylaş.',
        ],
        [
            'title' => 'NFC Kart Kullanıcıları',
            'description' => 'NFC kartını LinkProfilde profil adresine yönlendir.',
        ],
    ];

    $list = $sections && $sections->section_list
        ? json_decode($sections->section_list, true)
        : [];

    $cases = [];
    foreach ($defaults as $index => $default) {
        $fromDb = is_array($list) && isset($list[$index]) ? $list[$index] : null;
        $content = $fromDb['content'] ?? null;
        if ($content && str_contains($content, '|||')) {
            [$q, $a] = array_pad(explode('|||', $content, 2), 2, '');
            $cases[] = ['title' => trim($q), 'description' => trim($a)];
        } elseif ($content) {
            $cases[] = ['title' => $content, 'description' => $default['description']];
        } else {
            $cases[] = $default;
        }
    }
@endphp

<section id="use-cases" class="py-20 sm:py-24 bg-slate-50">
    <div class="max-w-[1200px] w-full mx-auto px-4">
        <div class="@if($customize && $sections) home-edit @endif" data-aos="fade-up">
            @if ($customize && $sections)
                @include('components.icons.edit-pen', ['class' => 'w-8 h-8', 'dialog' => 'useCasesSectionList'])
            @endif

            <div class="max-w-2xl mx-auto text-center mb-12">
                <h2 class="home-section-title text-slate-900 font-bold tracking-tight">
                    {{ $sections->title ?? __('Kimler için?') }}
                </h2>
                <p class="mt-4 text-slate-600 text-base sm:text-lg">
                    {{ $sections->description ?? 'Kişisel markadan işletmeye, etkinlikten NFC karta kadar aynı altyapı.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
                @foreach ($cases as $case)
                    <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-slate-900">{{ $case['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $case['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </div>

    @if ($customize && $sections)
        @include('components.home-edit.edit_section_list', ['dialog' => 'useCasesSectionList'])
    @endif
</section>
