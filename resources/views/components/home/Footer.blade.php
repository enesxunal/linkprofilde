@php
    if (!function_exists('homeGetSection')) {
        function homeGetSection($appSections, $name)
        {
            foreach ($appSections as $item) {
                if ($item['name'] == $name) {
                    return $item;
                }
            }
            return null;
        }
    }
@endphp

<footer class="bg-slate-50 border-t border-slate-200 overflow-hidden">
    <div class="max-w-[1200px] w-full mx-auto px-4 py-12 sm:py-14">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-6">
            <div class="md:col-span-5 text-center md:text-start">
                <div class="flex items-center justify-center md:justify-start gap-3">
                    <img
                        alt="{{ $app->title }} logo"
                        width="48"
                        height="48"
                        class="rounded-xl"
                        src="{{ asset($app->logo) }}"
                    >
                    <h2 class="text-lg font-bold text-slate-900">{{ $app->title }}</h2>
                </div>
                <p class="pt-4 pb-5 text-slate-500 text-sm leading-relaxed max-w-md mx-auto md:mx-0">
                    {{ $app->description }}
                </p>

                @php $follow = homeGetSection($appSections, 'Follow On'); @endphp
                @if ($follow)
                    <p class="font-medium mb-3 text-slate-800">{{ $follow->title }}</p>
                    <div class="@if($customize) home-edit @endif">
                        @if ($customize)
                            @include('components.icons.edit-pen', ['class' => 'w-[18px] h-[18px]', 'dialog' => 'footerFollowList'])
                        @endif
                        <div class="flex justify-center md:justify-start">
                            @foreach (json_decode($follow->section_list) as $list)
                                @php $item = (array) $list; @endphp
                                @php $href = \App\Support\SafeUrl::href($item['url'] ?? null); @endphp
                                @if ($href)
                                    <a
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        href="{{ $href }}"
                                        class="mr-4 text-slate-400 hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded"
                                        aria-label="{{ $item['icon'] ?? 'Sosyal medya' }}"
                                    >
                                        @include('components.icons.'.$item['icon'], ['class' => 'w-4 h-4'])
                                    </a>
                                @endif
                            @endforeach
                        </div>
                        @if ($customize)
                            @php $sections = $follow; @endphp
                            @include('components.home-edit.edit_section_list', ['dialog' => 'footerFollowList'])
                        @endif
                    </div>
                @endif
            </div>

            <div class="md:col-span-1"></div>

            <div class="md:col-span-3 text-center md:text-start">
                @php $address = homeGetSection($appSections, 'Address'); @endphp
                @if ($address)
                    <p class="font-semibold text-slate-900 mb-4">{{ $address->title }}</p>
                    <div class="@if($customize) home-edit @endif">
                        @if ($customize)
                            @include('components.icons.edit-pen', ['class' => 'w-[18px] h-[18px]', 'dialog' => 'footerAddressList'])
                        @endif
                        <ul class="text-slate-500 text-sm space-y-3">
                            @foreach (json_decode($address->section_list) as $list)
                                @php $item = (array) $list; @endphp
                                <li>
                                    @php $href = \App\Support\SafeUrl::href($item['url'] ?? null); @endphp
                                    @if ($href)
                                        <a
                                            href="{{ $href }}"
                                            @if(\Illuminate\Support\Str::startsWith($href, ['http://', 'https://'])) target="_blank" rel="noopener noreferrer" @endif
                                            class="hover:text-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded"
                                        >
                                            {{ $item['content'] }}
                                        </a>
                                    @else
                                        {{ $item['content'] }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        @if ($customize)
                            @php $sections = $address; @endphp
                            @include('components.home-edit.edit_section_list', ['dialog' => 'footerAddressList'])
                        @endif
                    </div>
                @endif
            </div>

            <div class="md:col-span-3 text-center md:text-start">
                <p class="font-semibold text-slate-900 mb-4">{{ __('Şirket') }}</p>
                <ul class="text-slate-500 text-sm space-y-3">
                    @if (count($customPages) > 0)
                        @foreach ($customPages as $page)
                            <li>
                                <a href="/app/{{ $page->route }}" class="hover:text-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded">
                                    {{ $page->name }}
                                </a>
                            </li>
                        @endforeach
                    @else
                        <li>{{ __('3 Kare Yazılım ve Tasarım Ajansı Limited Şirketi') }}</li>
                    @endif
                </ul>

                <div class="mt-6 flex flex-col items-center md:items-start gap-3">
                    <img
                        alt="iyzico"
                        width="160"
                        height="56"
                        class="rounded-lg"
                        src="{{ asset('assets/iyizico.svg') }}"
                    >
                    @if (file_exists(public_path('assets/creditcard-logo.png')))
                        <img
                            alt="Kredi kartı ödeme yöntemleri"
                            width="160"
                            height="40"
                            class="rounded-lg"
                            src="{{ asset('assets/creditcard-logo.png') }}"
                        >
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-slate-200">
        <div class="max-w-[1200px] w-full mx-auto px-4 py-6 text-center">
            <p class="text-slate-500 text-sm">
                © {{ date('Y') }} {{ $app->title }}. Tüm hakları saklıdır.
            </p>
        </div>
    </div>
</footer>
