@php
    $sections = null;
    foreach ($appSections as $item) {
        if ($item['name'] == 'Add Blocks') {
            $sections = $item;
            break;
        }
    }

    $title = $sections->title ?? 'Linklerini daha sade, daha yönetilebilir hale getir.';
    $description = $sections->description
        ?? 'Kısa link oluştur, düzenle, ara ve yönlendir. Ziyaret performansını analytics ile takip et.';

    $defaultFeatures = [
        'Kısa link oluşturma',
        'Düzenleme ve arama',
        'Harici URL yönlendirme',
        'Ziyaret analytics (ülke, cihaz, tarayıcı, referrer)',
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

<section id="short-links" class="py-20 sm:py-24 bg-white">
    <div class="max-w-[1200px] w-full mx-auto px-4">
        <div class="@if($customize) home-edit @endif">
            @if ($customize && $sections)
                @include('components.icons.edit-pen', ['class' => 'w-8 h-8', 'dialog' => 'blocksSection'])
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div data-aos="fade-up">
                    <p class="text-sm font-semibold text-blue-600 mb-3">Kısa Link</p>
                    <h2 class="home-section-title text-slate-900 font-bold tracking-tight">
                        {{ $title }}
                    </h2>
                    <p class="mt-4 text-slate-600 text-base sm:text-lg leading-relaxed">
                        {{ $description }}
                    </p>

                    <div class="mt-8 @if($customize) home-edit @endif">
                        @if ($customize && $sections)
                            @include('components.icons.edit-pen', ['class' => 'w-[18px] h-[18px]', 'dialog' => 'blocksSectionList'])
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
                            href="{{ auth()->check() ? '/short-links' : '/register' }}"
                            class="inline-flex justify-center items-center w-full sm:w-auto py-3 px-6 rounded-lg bg-blue-600 font-semibold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 transition-colors"
                        >
                            {{ __('Kısa Link Oluştur') }}
                        </a>
                    </div>
                </div>

                <div data-aos="fade-up" data-aos-delay="80" aria-hidden="true">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:p-5 shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-sm font-semibold text-slate-800">Kısa linkler</p>
                            <span class="text-xs text-slate-500">Demo</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm min-w-[420px]">
                                <thead>
                                    <tr class="text-xs uppercase tracking-wide text-slate-500 border-b border-slate-200">
                                        <th class="py-2 pr-3 font-medium">Link</th>
                                        <th class="py-2 pr-3 font-medium">Hedef</th>
                                        <th class="py-2 font-medium">Görüntülenme</th>
                                    </tr>
                                </thead>
                                <tbody class="text-slate-700">
                                    <tr class="border-b border-slate-200/80">
                                        <td class="py-3 pr-3 font-medium text-blue-600">/kampanya</td>
                                        <td class="py-3 pr-3 truncate max-w-[140px]">ornek.com/urun</td>
                                        <td class="py-3">842</td>
                                    </tr>
                                    <tr class="border-b border-slate-200/80">
                                        <td class="py-3 pr-3 font-medium text-blue-600">/bio</td>
                                        <td class="py-3 pr-3 truncate max-w-[140px]">profil sayfası</td>
                                        <td class="py-3">1.204</td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 pr-3 font-medium text-blue-600">/event</td>
                                        <td class="py-3 pr-3 truncate max-w-[140px]">kayit.ornek.com</td>
                                        <td class="py-3">316</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($customize && $sections)
        @include('components.home-edit.edit_section', ['dialog' => 'blocksSection'])
        @include('components.home-edit.edit_section_list', ['dialog' => 'blocksSectionList'])
    @endif
</section>
