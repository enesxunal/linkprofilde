<section id="pricing" class="py-20 sm:py-24 bg-slate-50">
    <div class="max-w-[1200px] w-full mx-auto px-4">
        <div
            data-aos="fade-up"
            class="text-center pb-8 max-w-2xl mx-auto"
        >
            <h2 class="home-section-title text-slate-900 font-bold tracking-tight">{{ __('Fiyatlandırma') }}</h2>
            <p class="mt-4 text-slate-600 text-base sm:text-lg">{{ __('Size uygun planı seçin') }}</p>
        </div>

        <ul
            role="tablist"
            data-tabs="tabs"
            class="pricing max-w-[220px] mx-auto grid grid-cols-2 rounded-xl bg-slate-200/70 p-1"
        >
            <li class="z-30 flex-auto text-center" data-ripple-light="true">
                <a
                    active=""
                    role="tab"
                    data-tab-target=""
                    aria-selected="true"
                    aria-controls="monthly"
                    class="flex w-full cursor-pointer items-center justify-center rounded-lg py-2 text-sm font-medium transition-all ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    {{ __('Aylık') }}
                </a>
            </li>
            <li class="z-30 flex-auto text-center" data-ripple-light="true">
                <a
                    role="tab"
                    data-tab-target=""
                    aria-selected="false"
                    aria-controls="yearly"
                    class="flex w-full cursor-pointer items-center justify-center rounded-lg py-2 text-sm font-medium transition-all ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    {{ __('Yıllık') }}
                </a>
            </li>
        </ul>

        <div data-tab-content="" class="mt-8">
            <div class="block opacity-100" id="monthly" role="tabpanel">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6">
                    @foreach ($plans as $plan)
                        @php
                            $features = [
                                "$plan->biolinks Profil Link Oluşturma",
                                "$plan->shortlinks Kısa Link Oluşturma",
                                "$plan->qrcodes QR Kod Oluşturma",
                                "$plan->themes Temalara Erişim",
                                $plan->custom_theme ? 'Özel Tema Oluşturulabilir' : 'Özel Tema Oluşturulamaz',
                            ];

                            if ($plan->name == 'BASIC') {
                                $badge = 'bg-slate-100 text-slate-600';
                                $cta = __('Ücretsiz Başla');
                            } else if ($plan->name == 'STANDARD') {
                                $badge = 'bg-emerald-50 text-emerald-700';
                                $cta = __('Planı Seç');
                            } else {
                                $badge = 'bg-blue-50 text-blue-700';
                                $cta = __('Planı Seç');
                            }
                        @endphp
                        <article
                            data-aos="fade-up"
                            class="group relative rounded-2xl border border-slate-200 bg-white p-0 shadow-sm hover:border-blue-300 hover:shadow-md transition-all"
                        >
                            <div class="p-6 border-b border-slate-100">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $badge }}">
                                    {{ $plan->name }}
                                </span>

                                @if ($plan->name == 'BASIC')
                                    <p class="text-3xl font-bold pt-4 pb-1 text-slate-900">{{ __('Ücretsiz') }}</p>
                                @else
                                    <p class="text-3xl font-bold pt-4 pb-1 text-slate-900">
                                        {{ $plan->monthly_price }}
                                        <span class="font-normal text-sm text-slate-500">
                                            {{ $plan->currency }} / {{ __('ay') }}
                                        </span>
                                    </p>
                                @endif

                                <p class="text-sm text-slate-600 mt-1">{{ $plan->description }}</p>
                            </div>

                            <div class="p-6">
                                @foreach ($features as $item)
                                    <div class="flex items-center text-slate-700 mb-3 last:mb-0">
                                        @include('components.icons.circle-check', ['class' => 'w-4 h-4 mr-2 text-blue-600 shrink-0'])
                                        <span class="text-sm">{{ $item }}</span>
                                    </div>
                                @endforeach

                                @if ($plan->name == 'BASIC')
                                    <button
                                        type="button"
                                        data-ripple-light="true"
                                        data-dialog-target="MonthlyBASIC"
                                        class="w-full text-center py-2.5 px-5 rounded-lg bg-blue-600 font-semibold text-white shadow-sm hover:bg-blue-700 mt-6 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                    >
                                        {{ $cta }}
                                    </button>
                                @else
                                    <a
                                        data-ripple-light="true"
                                        href="{{ route('billing', ['id' => $plan->id, 'type' => 'monthly']) }}"
                                        class="block text-center py-2.5 px-5 rounded-lg bg-blue-600 font-semibold text-white shadow-sm hover:bg-blue-700 mt-6 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                    >
                                        {{ $cta }}
                                    </a>
                                @endif
                            </div>
                        </article>
                        @include('components.basic-plan-select', ['dialog' => "Monthly$plan->name"])
                    @endforeach
                </div>
            </div>

            <div class="hidden opacity-0" id="yearly" role="tabpanel">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6">
                    @foreach ($plans as $plan)
                        @php
                            $features = [
                                "$plan->biolinks Profil Link Oluşturma",
                                "$plan->shortlinks Kısa Link Oluşturma",
                                "$plan->qrcodes QR Kod Oluşturma",
                                "$plan->themes Temalara Erişim",
                                $plan->custom_theme ? 'Özel Tema Oluşturulabilir' : 'Özel Tema Oluşturulamaz',
                            ];

                            if ($plan->name == 'BASIC') {
                                $badge = 'bg-slate-100 text-slate-600';
                                $cta = __('Ücretsiz Başla');
                            } else if ($plan->name == 'STANDARD') {
                                $badge = 'bg-emerald-50 text-emerald-700';
                                $cta = __('Planı Seç');
                            } else {
                                $badge = 'bg-blue-50 text-blue-700';
                                $cta = __('Planı Seç');
                            }
                        @endphp
                        <article
                            data-aos="fade-up"
                            class="group relative rounded-2xl border border-slate-200 bg-white p-0 shadow-sm hover:border-blue-300 hover:shadow-md transition-all"
                        >
                            <div class="p-6 border-b border-slate-100">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $badge }}">
                                    {{ $plan->name }}
                                </span>

                                @if ($plan->name == 'BASIC')
                                    <p class="text-3xl font-bold pt-4 pb-1 text-slate-900">{{ __('Ücretsiz') }}</p>
                                @else
                                    <p class="text-3xl font-bold pt-4 pb-1 text-slate-900">
                                        {{ $plan->yearly_price }}
                                        <span class="font-normal text-sm text-slate-500">
                                            {{ $plan->currency }} / {{ __('yıl') }}
                                        </span>
                                    </p>
                                @endif

                                <p class="text-sm text-slate-600 mt-1">{{ $plan->description }}</p>
                            </div>

                            <div class="p-6">
                                @foreach ($features as $item)
                                    <div class="flex items-center text-slate-700 mb-3 last:mb-0">
                                        @include('components.icons.circle-check', ['class' => 'w-4 h-4 mr-2 text-blue-600 shrink-0'])
                                        <span class="text-sm">{{ $item }}</span>
                                    </div>
                                @endforeach

                                @if ($plan->name == 'BASIC')
                                    <button
                                        type="button"
                                        data-ripple-light="true"
                                        data-dialog-target="YearlyBASIC"
                                        class="w-full text-center py-2.5 px-5 rounded-lg bg-blue-600 font-semibold text-white shadow-sm hover:bg-blue-700 mt-6 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                    >
                                        {{ $cta }}
                                    </button>
                                @else
                                    <a
                                        data-ripple-light="true"
                                        href="{{ route('billing', ['id' => $plan->id, 'type' => 'yearly']) }}"
                                        class="block text-center py-2.5 px-5 rounded-lg bg-blue-600 font-semibold text-white shadow-sm hover:bg-blue-700 mt-6 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                    >
                                        {{ $cta }}
                                    </a>
                                @endif
                            </div>
                        </article>
                        @include('components.basic-plan-select', ['dialog' => "Yearly$plan->name"])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
