@php
    $sections = null;
    foreach ($appSections as $item) {
        if ($item['name'] == 'QR Codes') {
            $sections = $item;
            break;
        }
    }

    $title = $sections->title ?? 'Markana uygun QR kodlar oluştur';
    $description = $sections->description
        ?? 'Renk, boyut, köşe stili ve logo ile QR kodunu özelleştir; indir ve projelerin altında düzenle.';

    $defaultFeatures = [
        'QR oluşturma ve indirme',
        'Renk, boyut ve köşe/stil özelleştirme',
        'Logo ekleme',
        'Proje bazlı organizasyon',
        'Bio link ve kısa link için QR',
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

<section id="qr" class="py-20 sm:py-24 bg-slate-50">
    <div class="max-w-[1200px] w-full mx-auto px-4">
        <div class="@if($customize) home-edit @endif">
            @if ($customize && $sections)
                @include('components.icons.edit-pen', ['class' => 'w-8 h-8', 'dialog' => 'qrCodeSection'])
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div class="relative order-2 lg:order-1" data-aos="fade-up">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm max-w-md mx-auto">
                        <div class="flex items-start justify-between gap-4 mb-6">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">QR önizleme</p>
                                <p class="text-xs text-slate-500 mt-1">Demo görsel</p>
                            </div>
                            <span class="text-xs rounded-full bg-slate-100 text-slate-600 px-2.5 py-1">PNG / SVG</span>
                        </div>

                        <div class="aspect-square rounded-2xl bg-slate-50 border border-slate-200 grid place-items-center p-8">
                            <img
                                src="{{ asset($sections->thumbnail ?? 'assets/qr-code.svg') }}"
                                alt="QR kod demo görseli"
                                class="w-full max-w-[220px] h-auto"
                            >
                        </div>

                        <div class="mt-5 grid grid-cols-3 gap-2 text-center text-xs text-slate-600" aria-hidden="true">
                            <div class="rounded-lg border border-slate-200 py-2">Renk</div>
                            <div class="rounded-lg border border-slate-200 py-2">Köşe</div>
                            <div class="rounded-lg border border-slate-200 py-2">Logo</div>
                        </div>
                    </div>
                </div>

                <div class="order-1 lg:order-2" data-aos="fade-up" data-aos-delay="80">
                    <p class="text-sm font-semibold text-blue-600 mb-3">QR Kod</p>
                    <h2 class="home-section-title text-slate-900 font-bold tracking-tight">
                        {{ $title }}
                    </h2>
                    <p class="mt-4 text-slate-600 text-base sm:text-lg leading-relaxed">
                        {{ $description }}
                    </p>

                    <div class="mt-8 @if($customize) home-edit @endif">
                        @if ($customize && $sections)
                            @include('components.icons.edit-pen', ['class' => 'w-[18px] h-[18px]', 'dialog' => 'qrCodeSectionList'])
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
                            href="{{ auth()->check() ? '/qrcodes/create' : '/register' }}"
                            class="inline-flex justify-center items-center w-full sm:w-auto py-3 px-6 rounded-lg bg-blue-600 font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 transition-colors"
                        >
                            {{ __('QR Kod Oluştur') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($customize && $sections)
        @include('components.home-edit.edit_section', ['dialog' => 'qrCodeSection'])
        @include('components.home-edit.edit_section_list', ['dialog' => 'qrCodeSectionList'])
    @endif
</section>
