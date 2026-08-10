<section id="pricing" class="max-w-[1200px] w-full mx-auto overflow-hidden px-4 py-20">
    <div
        data-aos="fade-up"
        data-aos-duration="1500"
        data-aos-anchor-placement="center-bottom"
        class="text-center pb-6"
    >
        <h4 class="font-bold">{{__('Fiyatlandırma')}}</h4>
        <p>{{__('Size Uygun Ödeme Planını Seçin')}}</p>
    </div>

    <ul
        role="list"
        data-tabs="tabs"
        class="pricing max-w-[220px] mx-auto grid grid-cols-2 rounded-xl bg-blue-gray-50/60 p-1"
    >
        <li class="z-30 flex-auto text-center" data-ripple-light="true">
            <a
                active=""
                role="tab"
                data-tab-target=""
                aria-selected="true"
                aria-controls="monthly"
                class="flex w-full cursor-pointer items-center justify-center rounded-lg py-2 transition-all ease-in-out"
            >
                {{__('Aylık')}}
            </a>
        </li>
        <li class="z-30 flex-auto text-center" data-ripple-light="true">
            <a
                role="tab"
                data-tab-target=""
                aria-selected="false"
                aria-controls="yearly"
                class="flex w-full cursor-pointer items-center justify-center rounded-lg py-2 transition-all ease-in-out"
            >
                {{__('Yıllık')}}
            </a>
        </li>
    </ul>

    <div data-tab-content="" class="mt-7">
        <div class="block opacity-100" id="monthly" role="tabpanel">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-7">
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
                            $badge = 'bg-gray-100 text-gray-500';
                        } else if ($plan->name == 'STANDARD') {
                            $badge = 'bg-green-50 text-green-500';
                        } else {
                            $badge = 'bg-blue-50 text-blue-500';
                        }
                    @endphp
                    <div
                        data-aos="fade-up"
                        data-aos-duration="1500"
                        data-aos-anchor-placement="center-bottom"
                        class="group relative rounded-lg outline outline-1 outline-gray-200 hover:outline-2 hover:outline-blue-500"
                    >
                        <div class="p-6 border-b border-gray-200">
                            <span class="text-xs font-medium px-2.5 rounded-full {{$badge}}">
                                {{$plan->name}}
                            </span>

                            @if($plan->name == 'BASIC')
                                <h4 class="font-bold pt-4 pb-1">{{__('Ücretsiz')}}</h4>
                            @else
                                <h4 class="font-bold pt-4 pb-1">
                                    {{$plan->monthly_price}}
                                    <span class="font-normal text-sm">
                                        {{$plan->currency}} /Aylık
                                    </span>
                                </h4>
                            @endif

                            <p class="text-sm">{{$plan->description}}</p>
                        </div>

                        <div class="p-6">
                            @foreach ($features as $item)
                                <div class="flex items-center text-gray-700 dark:text-white mb-4">
                                    @include('components.icons.circle-check', ['class' => 'w-4 h-4 mr-2 text-blue-500'])
                                    <small>{{$item}}</small>
                                </div>
                            @endforeach

                            @if ($plan->name == 'BASIC')
                                <button
                                    type="button"
                                    data-ripple-light="true"
                                    data-dialog-target="MonthlyBASIC"
                                    class="w-full text-center py-2 px-5 rounded bg-blue-500 font-medium text-white shadow-md hover:shadow-lg hover:shadow-blue-500/40  shadow-blue-500/20 transition-all active:opacity-[0.85] mt-2"
                                >
                                    {{__('Ücretsiz kaydol')}}
                                </button>
                            @else
                                <a
                                    data-ripple-light="true"
                                    href="{{route('billing', ['id'=>$plan->id, 'type'=>'monthly'])}}"
                                    class="block text-center py-2 px-5 rounded bg-blue-500 font-medium text-white shadow-md hover:shadow-lg hover:shadow-blue-500/40  shadow-blue-500/20 transition-all active:opacity-[0.85] mt-6"
                                >
                                    {{__('Ücretsiz kaydol')}}
                                </a>
                            @endif
                        </div>
                    </div>
                    @include('components.basic-plan-select', ['dialog' => "Monthly$plan->name"])
                @endforeach
            </div>
        </div>

        <div class="hidden opacity-0" id="yearly" role="tabpanel">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-7">
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
                            $badge = 'bg-gray-100 text-gray-500';
                        } else if ($plan->name == 'STANDARD') {
                            $badge = 'bg-green-50 text-green-500';
                        } else {
                            $badge = 'bg-blue-50 text-blue-500';
                        }
                    @endphp
                    <div
                        data-aos="fade-up"
                        data-aos-duration="1500"
                        data-aos-anchor-placement="center-bottom"
                        class="group relative rounded-lg outline outline-1 outline-gray-200 hover:outline-2 hover:outline-blue-500"
                    >
                        <div class="p-6 border-b border-gray-200">
                            <span class="text-xs font-medium px-2.5 rounded-full {{$badge}}">
                                {{$plan->name}}
                            </span>

                            @if($plan->name == 'BASIC')
                                <h4 class="font-bold pt-4 pb-1">{{__('Ücretsiz')}}</h4>
                            @else
                                <h4 class="font-bold pt-4 pb-1">
                                    {{$plan->yearly_price}}
                                    <span class="font-normal text-sm">
                                        {{$plan->currency}} /yıllık
                                    </span>
                                </h4>
                            @endif

                            <p class="text-sm">{{$plan->description}}</p>
                        </div>

                        <div class="p-6">
                            @foreach ($features as $item)
                                <div class="flex items-center text-gray-700 dark:text-white mb-4">
                                    @include('components.icons.circle-check', ['class' => 'w-4 h-4 mr-2 text-blue-500'])
                                    <small>{{$item}}</small>
                                </div>
                            @endforeach

                            @if ($plan->name == 'BASIC')
                                <button
                                    type="button"
                                    data-ripple-light="true"
                                    data-dialog-target="YearlyBASIC"
                                    class="w-full text-center py-2 px-5 rounded bg-blue-500 font-medium text-white shadow-md hover:shadow-lg hover:shadow-blue-500/40  shadow-blue-500/20 transition-all active:opacity-[0.85] mt-2"
                                >
                                    {{__('Ücretsiz kaydol')}}
                                </button>
                            @else
                                <a
                                    data-ripple-light="true"
                                    href="{{route('billing', ['id'=>$plan->id, 'type'=>'yearly'])}}"
                                    class="block text-center py-2 px-5 rounded bg-blue-500 font-medium text-white shadow-md hover:shadow-lg hover:shadow-blue-500/40  shadow-blue-500/20 transition-all active:opacity-[0.85] mt-6"
                                >
                                    {{__('Ücretsiz kaydol')}}
                                </a>
                            @endif
                        </div>
                    </div>
                    @include('components.basic-plan-select', ['dialog' => "Yearly$plan->name"])
                @endforeach
            </div>
        </div>
    </div>
</section>
