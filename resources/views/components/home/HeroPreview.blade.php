{{-- Static marketing mockup only. No real user data. No React mount. --}}
<div class="relative w-full max-w-[340px] mx-auto">
    <div class="rounded-[2rem] border-[10px] border-gray-900 bg-gray-900 shadow-2xl overflow-hidden">
        <div class="mx-auto h-5 w-28 rounded-b-2xl bg-gray-900" aria-hidden="true"></div>

        {{-- Themed bio profile surface — solid colors only (reliable in production CSS) --}}
        <div class="relative min-h-[560px] bg-gray-900 text-white">
            <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-blue-600 to-gray-900" aria-hidden="true"></div>

            <div class="relative px-5 pt-8 pb-6">
                <div class="flex flex-col items-center text-center">
                    <div class="relative">
                        <img
                            src="{{ asset('assets/user-profile.png') }}"
                            alt="Demo profil görseli"
                            class="h-[88px] w-[88px] rounded-full object-cover border-[3px] border-white shadow-lg bg-white"
                            width="88"
                            height="88"
                        >
                        <span class="absolute bottom-1 right-1 h-3.5 w-3.5 rounded-full border-2 border-gray-900 bg-green-400" aria-hidden="true"></span>
                    </div>

                    <p class="mt-4 text-lg font-bold tracking-tight text-white">Ayşe Yılmaz</p>
                    <p class="text-sm text-blue-300">@ayse.studio</p>
                    <p class="mt-2 text-sm leading-relaxed text-gray-300 max-w-[240px]">
                        Marka danışmanı · içerik · iş birlikleri. Tüm bağlantılarım tek profilde.
                    </p>

                    <div class="mt-4 flex items-center justify-center gap-2.5" aria-hidden="true">
                        <span class="h-9 w-9 rounded-full bg-pink-500 grid place-items-center text-[11px] font-bold text-white">IG</span>
                        <span class="h-9 w-9 rounded-full bg-red-500 grid place-items-center text-[11px] font-bold text-white">YT</span>
                        <span class="h-9 w-9 rounded-full bg-blue-600 grid place-items-center text-[11px] font-bold text-white">in</span>
                        <span class="h-9 w-9 rounded-full bg-green-500 grid place-items-center text-[11px] font-bold text-white">WA</span>
                    </div>
                </div>

                <div class="mt-6 space-y-2.5">
                    <div class="rounded-2xl bg-white text-gray-900 text-sm font-semibold py-3.5 px-4 text-center shadow-sm">
                        Portfolyo &amp; hizmetler
                    </div>
                    <div class="rounded-2xl bg-gray-800 border border-gray-700 text-white text-sm font-medium py-3.5 px-4 text-center">
                        YouTube kanalı
                    </div>
                    <div class="rounded-2xl bg-gray-800 border border-gray-700 text-white text-sm font-medium py-3.5 px-4 text-center">
                        Spotify playlist
                    </div>
                    <div class="rounded-2xl bg-blue-600 text-white text-sm font-semibold py-3.5 px-4 text-center shadow-sm">
                        İş birliği için yaz
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-2.5" aria-hidden="true">
                    <div class="rounded-xl bg-gray-800 border border-gray-700 px-3 py-2.5 text-left">
                        <p class="text-[10px] uppercase tracking-wide text-blue-300">QR</p>
                        <p class="text-xs font-medium mt-0.5 text-white">Paylaş &amp; indir</p>
                    </div>
                    <div class="rounded-xl bg-gray-800 border border-gray-700 px-3 py-2.5 text-left">
                        <p class="text-[10px] uppercase tracking-wide text-blue-300">Rehber</p>
                        <p class="text-xs font-medium mt-0.5 text-white">vCard ekle</p>
                    </div>
                </div>

                <p class="mt-5 text-center text-[10px] text-gray-400">
                    Demo önizleme
                </p>
            </div>
        </div>
    </div>
</div>
