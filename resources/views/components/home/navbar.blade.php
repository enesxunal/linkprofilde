<nav id="navbar" class="fixed z-50 block h-max w-full max-w-full bg-white/90 backdrop-blur-md border-b border-slate-200/80 py-0.5">
    <div class="max-w-[1200px] w-full mx-auto px-4 py-2 lg:py-3">
        <div class="flex items-center text-slate-900">
            <div class="flex items-center min-w-0">
                <img
                    width="40"
                    height="40"
                    class="rounded-lg shrink-0"
                    src="{{ asset($app->logo) }}"
                    alt="{{ $app->title }} logo"
                >
                <p class="ml-2.5 text-base sm:text-lg font-semibold text-slate-800 truncate">
                    <a href="/" class="focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded">
                        {{ $app->title }}
                    </a>
                </p>
            </div>

            <ul class="ml-auto mr-4 hidden items-center gap-5 xl:gap-6 lg:flex text-sm font-medium text-slate-600">
                <li><a href="#features" class="hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded">{{ __('Özellikler') }}</a></li>
                <li><a href="#bio-link" class="hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded">{{ __('Bio Link') }}</a></li>
                <li><a href="#short-links" class="hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded">{{ __('Kısa Link') }}</a></li>
                <li><a href="#qr" class="hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded">{{ __('QR Kod') }}</a></li>
                <li><a href="#pricing" class="hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded">{{ __('Fiyatlandırma') }}</a></li>
            </ul>

            <div class="ml-auto lg:ml-0 flex items-center gap-2">
                @if (auth()->user())
                    @if ($SA)
                        @if ($customize)
                            <a
                                href="/"
                                data-ripple-light="true"
                                class="hidden sm:inline-flex py-2 px-4 rounded-lg font-medium border border-blue-500 text-blue-600 text-sm hover:bg-blue-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                            >
                                {{ __('Görüntüle') }}
                            </a>
                        @else
                            <a
                                href="?customize=intro"
                                data-ripple-light="true"
                                class="hidden sm:inline-flex py-2 px-4 rounded-lg font-medium border border-blue-500 text-blue-600 text-sm hover:bg-blue-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                            >
                                {{ __('Özelleştir') }}
                            </a>
                        @endif
                    @endif

                    <a
                        href="/dashboard"
                        data-ripple-light="true"
                        class="hidden sm:inline-flex py-2.5 px-5 rounded-lg bg-blue-600 font-medium text-white text-sm shadow-sm hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 transition-colors"
                    >
                        {{ __('Yönetim Paneli') }}
                    </a>
                @else
                    <a
                        href="/login"
                        data-ripple-light="true"
                        class="hidden sm:inline-flex py-2.5 px-4 rounded-lg font-medium text-slate-800 text-sm border border-slate-200 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 transition-colors"
                    >
                        {{ __('Giriş Yap') }}
                    </a>
                    <a
                        href="/register"
                        data-ripple-light="true"
                        class="hidden sm:inline-flex py-2.5 px-5 rounded-lg bg-blue-600 font-medium text-white text-sm shadow-sm hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 transition-colors"
                    >
                        {{ __('Ücretsiz Başla') }}
                    </a>
                @endif

                <button
                    id="navbar-menu"
                    type="button"
                    class="relative ml-1 h-10 w-10 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 lg:hidden"
                    data-collapse-target="sticky-navar"
                    aria-label="Menüyü aç"
                    aria-expanded="false"
                    aria-controls="mobile-nav"
                >
                    <span class="absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </span>
                </button>
            </div>
        </div>

        <div
            id="mobile-nav"
            data-collapse="sticky-navar"
            class="block h-0 w-full basis-full overflow-hidden text-slate-800 transition-all duration-300 ease-in lg:hidden"
        >
            <ul class="flex flex-col gap-4 pt-5 pb-4 text-sm font-medium">
                <li><a href="#features">{{ __('Özellikler') }}</a></li>
                <li><a href="#bio-link">{{ __('Bio Link') }}</a></li>
                <li><a href="#short-links">{{ __('Kısa Link') }}</a></li>
                <li><a href="#qr">{{ __('QR Kod') }}</a></li>
                <li><a href="#pricing">{{ __('Fiyatlandırma') }}</a></li>

                @if (auth()->user())
                    @if ($SA)
                        @if ($customize)
                            <li>
                                <a href="/" class="inline-flex py-2.5 px-5 rounded-lg font-medium border border-blue-500 text-blue-600">
                                    {{ __('Görüntüle') }}
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="?customize=intro" class="inline-flex py-2.5 px-5 rounded-lg font-medium border border-blue-500 text-blue-600">
                                    {{ __('Özelleştir') }}
                                </a>
                            </li>
                        @endif
                    @endif
                    <li>
                        <a href="/dashboard" class="inline-flex w-full justify-center py-2.5 px-5 rounded-lg bg-blue-600 font-medium text-white">
                            {{ __('Yönetim Paneli') }}
                        </a>
                    </li>
                @else
                    <li>
                        <a href="/login" class="inline-flex w-full justify-center py-2.5 px-5 rounded-lg border border-slate-200 font-medium text-slate-900">
                            {{ __('Giriş Yap') }}
                        </a>
                    </li>
                    <li>
                        <a href="/register" class="inline-flex w-full justify-center py-2.5 px-5 rounded-lg bg-blue-600 font-medium text-white">
                            {{ __('Ücretsiz Başla') }}
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
