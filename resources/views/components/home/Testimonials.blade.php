<section id="testimonials" class="py-20 sm:py-24 bg-white">
    <div class="max-w-[1200px] w-full mx-auto px-4">
        <div data-aos="fade-up" class="text-center pb-10 max-w-2xl mx-auto">
            <h2 class="home-section-title text-slate-900 font-bold tracking-tight mb-3">{{ __('Yorumlar') }}</h2>
            <p class="text-slate-600 text-base sm:text-lg">{{ __('Kullanıcılarımızın deneyimleri') }}</p>
        </div>

        @if (count($testimonials) > 0)
            <div class="swiper-container px-1 sm:px-3" data-aos="fade-up">
                <div class="swiper-wrapper pb-10 pt-10">
                    @foreach ($testimonials as $item)
                        <div class="swiper-slide">
                            <article class="h-full shadow-sm relative p-6 pt-14 text-center rounded-2xl border border-slate-200 bg-slate-50">
                                <img
                                    src="{{ asset($item->thumbnail) }}"
                                    class="w-[72px] h-[72px] object-cover border-2 border-white rounded-full absolute -top-8 left-1/2 -translate-x-1/2 shadow-sm bg-white"
                                    alt="{{ $item->name }}"
                                    width="72"
                                    height="72"
                                >
                                <p class="text-slate-600 text-sm leading-relaxed">{{ $item->testimonial }}</p>
                                <div class="border-t border-slate-200 my-4"></div>
                                <p class="text-blue-600 font-bold">{{ $item->name }}</p>
                                <p class="text-sm text-slate-500">{{ $item->title }}</p>
                            </article>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination" style="position: initial !important"></div>
            </div>
        @endif
    </div>
</section>
