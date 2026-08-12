@php
    $sections = null;
    foreach ($appSections as $item) {
        if ($item['name'] == 'Support') {
            $sections = $item;
            break;
        }
    }

    $faqDefaults = [
        [
            'q' => 'LinkProfilde nedir?',
            'a' => 'LinkProfilde; bio link profili, kısa link yönetimi, QR kod oluşturma ve analytics özelliklerini bir araya getiren bir dijital profil platformudur.',
        ],
        [
            'q' => 'Bio Link nedir?',
            'a' => 'Bio Link, tüm önemli bağlantılarını, sosyal hesaplarını ve içeriklerini tek bir kişisel profil sayfasında toplamanı sağlar.',
        ],
        [
            'q' => 'QR kod oluşturabilir miyim?',
            'a' => 'Evet. Renk, boyut, köşe stili ve logo ile QR kod oluşturup indirebilir; projelerin altında düzenleyebilirsin.',
        ],
        [
            'q' => 'Kısa linklerimin performansını görebilir miyim?',
            'a' => 'Evet. Kısa linklerin için ülke, cihaz, işletim sistemi, tarayıcı, dil ve referrer gibi analytics verilerini görebilirsin.',
        ],
        [
            'q' => 'Profilimi özelleştirebilir miyim?',
            'a' => 'Evet. Hazır temalar, sosyal bağlantılar, içerik blokları ve plana göre özel tema ile profilini özelleştirebilirsin.',
        ],
        [
            'q' => 'NFC kartımla kullanabilir miyim?',
            'a' => 'Evet. LinkProfilde public profil URL’sini NFC kartına yazarak kartını dijital profiline yönlendirebilirsin. NFC donanımı ayrıca satılmaz veya entegre edilmez.',
        ],
    ];

    $list = $sections && $sections->section_list
        ? json_decode($sections->section_list, true)
        : [];

    $faqs = [];
    if (is_array($list) && count($list) > 0) {
        foreach ($list as $index => $row) {
            $content = $row['content'] ?? '';
            if ($content && str_contains($content, '|||')) {
                [$q, $a] = array_pad(explode('|||', $content, 2), 2, '');
                $faqs[] = ['q' => trim($q), 'a' => trim($a)];
            } elseif ($content) {
                $faqs[] = [
                    'q' => $content,
                    'a' => $faqDefaults[$index]['a'] ?? 'Detaylar için destek ekibimizle iletişime geçebilirsin.',
                ];
            }
        }
    }
    if (count($faqs) === 0) {
        $faqs = $faqDefaults;
    }
@endphp

<section id="faq" class="py-20 sm:py-24 bg-white">
    <div class="max-w-[800px] w-full mx-auto px-4">
        <div class="@if($customize && $sections) home-edit @endif" data-aos="fade-up">
            @if ($customize && $sections)
                @include('components.icons.edit-pen', ['class' => 'w-8 h-8', 'dialog' => 'faqSectionList'])
            @endif

            <div class="text-center mb-10">
                <h2 class="home-section-title text-slate-900 font-bold tracking-tight">
                    {{ $sections->title ?? __('Sık sorulan sorular') }}
                </h2>
                <p class="mt-4 text-slate-600">
                    {{ $sections->description ?? 'LinkProfilde hakkında en çok merak edilenler.' }}
                </p>
            </div>

            <div class="space-y-3">
                @foreach ($faqs as $index => $faq)
                    <details class="group rounded-xl border border-slate-200 bg-slate-50 open:bg-white open:shadow-sm px-5 py-4">
                        <summary class="cursor-pointer list-none flex items-center justify-between gap-4 font-semibold text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded">
                            <span>{{ $faq['q'] }}</span>
                            <span class="text-slate-400 group-open:rotate-45 transition-transform text-xl leading-none" aria-hidden="true">+</span>
                        </summary>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600">
                            {{ $faq['a'] }}
                        </p>
                    </details>
                @endforeach
            </div>
        </div>
    </div>

    @if ($customize && $sections)
        @include('components.home-edit.edit_section_list', ['dialog' => 'faqSectionList'])
    @endif
</section>
