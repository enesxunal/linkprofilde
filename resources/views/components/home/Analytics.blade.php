@php
    $sections = null;
    foreach ($appSections as $item) {
        if ($item['name'] == 'Analytics') {
            $sections = $item;
            break;
        }
    }

    $title = $sections->title ?? 'Ne kadar paylaştığını değil, ne kadar etki yarattığını gör.';
    $description = $sections->description
        ?? 'Dashboard özetleri ve kısa link analytics ile ziyaretçilerini net şekilde takip et.';

    $defaultFeatures = [
        'Toplam link, page view, proje ve QR özetleri',
        'Aylık ziyaretçi grafiği',
        'Haftalık page view',
        'Kısa link: ülke, cihaz, işletim sistemi, tarayıcı, dil, referrer',
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

<section id="analytics" class="py-20 sm:py-24 bg-white">
    <div class="max-w-[1200px] w-full mx-auto px-4">
        <div class="@if($customize && $sections) home-edit @endif">
            @if ($customize && $sections)
                @include('components.icons.edit-pen', ['class' => 'w-8 h-8', 'dialog' => 'analyticsSection'])
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div data-aos="fade-up">
                    <p class="text-sm font-semibold text-blue-600 mb-3">Analytics</p>
                    <h2 class="home-section-title text-slate-900 font-bold tracking-tight">
                        {{ $title }}
                    </h2>
                    <p class="mt-4 text-slate-600 text-base sm:text-lg leading-relaxed">
                        {{ $description }}
                    </p>

                    <div class="mt-8 @if($customize && $sections) home-edit @endif">
                        @if ($customize && $sections)
                            @include('components.icons.edit-pen', ['class' => 'w-[18px] h-[18px]', 'dialog' => 'analyticsSectionList'])
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
                </div>

                <div data-aos="fade-up" data-aos-delay="80" aria-hidden="true">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 sm:p-6 shadow-sm">
                        <div class="grid grid-cols-2 gap-3 mb-5">
                            <div class="rounded-xl bg-white border border-slate-200 p-4">
                                <p class="text-xs text-slate-500">Toplam link</p>
                                <p class="mt-1 text-2xl font-bold text-slate-900">24</p>
                            </div>
                            <div class="rounded-xl bg-white border border-slate-200 p-4">
                                <p class="text-xs text-slate-500">Page view</p>
                                <p class="mt-1 text-2xl font-bold text-slate-900">8.4k</p>
                            </div>
                            <div class="rounded-xl bg-white border border-slate-200 p-4">
                                <p class="text-xs text-slate-500">Projeler</p>
                                <p class="mt-1 text-2xl font-bold text-slate-900">6</p>
                            </div>
                            <div class="rounded-xl bg-white border border-slate-200 p-4">
                                <p class="text-xs text-slate-500">QR kod</p>
                                <p class="mt-1 text-2xl font-bold text-slate-900">12</p>
                            </div>
                        </div>

                        <div class="rounded-xl bg-white border border-slate-200 p-4">
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-sm font-semibold text-slate-800">Aylık ziyaretçiler</p>
                                <span class="text-xs text-slate-500">Demo grafik</span>
                            </div>
                            <div class="flex items-end gap-1.5 h-28">
                                @foreach ([28, 40, 36, 52, 48, 64, 58, 72, 68, 80, 76, 90] as $h)
                                    <div class="flex-1 rounded-t bg-blue-500/80" style="height: {{ $h }}%"></div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($customize && $sections)
        @include('components.home-edit.edit_section', ['dialog' => 'analyticsSection'])
        @include('components.home-edit.edit_section_list', ['dialog' => 'analyticsSectionList'])
    @endif
</section>
