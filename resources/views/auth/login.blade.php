@extends('auth.layout')

@section('title', __('Giriş Yap') . ' · ' . ($app->title ?? 'LinkProfilde'))

@section('content')
    <div class="w-full max-w-[920px] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="grid grid-cols-1 lg:grid-cols-12">
            <aside class="relative hidden overflow-hidden bg-gray-900 px-8 py-10 text-white lg:col-span-5 lg:flex lg:flex-col lg:justify-between">
                <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-blue-600 to-gray-900" aria-hidden="true"></div>

                <div class="relative">
                    <p class="text-sm font-medium text-blue-300">{{ __('Hoş geldin') }}</p>
                    <h1 class="mt-3 text-2xl font-bold tracking-tight text-white sm:text-3xl" style="font-size:1.75rem;line-height:1.2;">
                        {{ __('Tek hesap. Tüm dijital dünyan.') }}
                    </h1>
                    <p class="mt-3 text-sm leading-relaxed text-gray-300">
                        {{ __('Bio link, kısa link, QR kod ve analitiği tek panelden yönet.') }}
                    </p>
                </div>

                <ul class="relative mt-10 space-y-3 text-sm text-gray-300">
                    <li class="flex items-start gap-2.5">
                        <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-600 text-[11px] font-bold text-white">✓</span>
                        <span>{{ __('Hazır temalar ve içerik blokları') }}</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-600 text-[11px] font-bold text-white">✓</span>
                        <span>{{ __('Kısa link ve QR kod yönetimi') }}</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-blue-600 text-[11px] font-bold text-white">✓</span>
                        <span>{{ __('Ziyaretçi analitikleri') }}</span>
                    </li>
                </ul>
            </aside>

            <div class="px-6 py-8 sm:px-10 sm:py-10 lg:col-span-7">
                <div class="mb-6 flex items-center gap-1 rounded-lg border border-slate-200 bg-slate-50 p-1">
                    <a
                        href="{{ route('login') }}"
                        class="flex-1 rounded-md bg-white px-3 py-2 text-center text-sm font-semibold text-slate-900 shadow-sm"
                        aria-current="page"
                    >
                        {{ __('Giriş Yap') }}
                    </a>
                    <a
                        href="{{ route('register') }}"
                        class="flex-1 rounded-md px-3 py-2 text-center text-sm font-medium text-slate-500 transition-colors hover:text-slate-800"
                    >
                        {{ __('Kayıt Ol') }}
                    </a>
                </div>

                <p class="mb-6 text-sm text-slate-600 lg:hidden">
                    {{ __('Hesabına giriş yap ve profilini yönetmeye devam et.') }}
                </p>

                @if (session('status'))
                    <p class="mb-4 rounded-lg border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-700">
                        {{ session('status') }}
                    </p>
                @endif

                <form method="POST" class="auth-form space-y-4" action="{{ route('login') }}">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('E-posta') }}</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2">
                                @include('components.icons.email', ['class' => 'w-5 h-5 text-slate-400'])
                            </span>
                            <input
                                required
                                id="email"
                                name="email"
                                type="email"
                                autocomplete="email"
                                value="{{ old('email') }}"
                                placeholder="{{ __('ornek@email.com') }}"
                                class="w-full rounded-lg border border-slate-200 bg-white py-3 pl-11 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                            >
                        </div>
                        @error('email')
                            <span class="mt-1.5 block text-xs text-red-500" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">{{ __('Şifre') }}</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2">
                                @include('components.icons.lock-keyhole', ['class' => 'w-5 h-5 text-slate-400'])
                            </span>
                            <input
                                required
                                id="password"
                                type="password"
                                name="password"
                                autocomplete="current-password"
                                placeholder="{{ __('Şifren') }}"
                                class="w-full rounded-lg border border-slate-200 bg-white py-3 pl-11 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                            >
                        </div>
                        @error('password')
                            <span class="mt-1.5 block text-xs text-red-500" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-1 text-sm">
                        <label for="remember" class="inline-flex cursor-pointer items-center gap-2 text-slate-600">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <span>{{ __('Beni Hatırla') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-blue-700">
                                {{ __('Şifremi Unuttum?') }}
                            </a>
                        @endif
                    </div>

                    <button
                        type="submit"
                        class="mt-2 inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                    >
                        {{ __('Giriş Yap') }}
                    </button>
                </form>

                @if ($google && $google->active)
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <div class="relative flex justify-center text-xs">
                            <span class="bg-white px-3 text-slate-400">{{ __('veya') }}</span>
                        </div>
                    </div>

                    <form action="{{ url('auth/google') }}" method="GET">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm font-medium text-slate-800 transition-colors hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                        >
                            <img src="{{ asset('assets/icons/google.svg') }}" alt="" class="h-5 w-5" width="20" height="20">
                            {{ __('Google ile devam et') }}
                        </button>
                    </form>
                @endif

                <p class="mt-6 text-center text-sm text-slate-500">
                    {{ __('Hesabın yok mu?') }}
                    <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700">{{ __('Ücretsiz kayıt ol') }}</a>
                </p>
            </div>
        </div>
    </div>
@endsection
