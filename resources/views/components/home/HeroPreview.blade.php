{{-- Static marketing mockup only. No real user data. No React mount. --}}
<div class="relative w-full max-w-[340px] mx-auto">
    <div class="absolute -left-4 top-10 hidden sm:block rounded-xl bg-white border border-slate-200 shadow-sm px-3 py-2 text-xs font-medium text-slate-600" aria-hidden="true">
        <span class="inline-flex items-center gap-1.5">
            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
            1.2k görüntülenme
        </span>
    </div>

    <div class="absolute -right-2 bottom-24 hidden sm:flex items-center gap-2 rounded-xl bg-white border border-slate-200 shadow-sm px-3 py-2" aria-hidden="true">
        <div class="h-9 w-9 rounded-md bg-slate-900 text-white grid place-items-center text-[10px] font-bold tracking-tight">
            QR
        </div>
        <span class="text-xs font-medium text-slate-600">Paylaşılabilir QR</span>
    </div>

    <div class="relative rounded-[2rem] border-[10px] border-slate-900 bg-slate-900 shadow-2xl shadow-slate-900/20 overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 z-10 h-5 w-24 rounded-b-xl bg-slate-900" aria-hidden="true"></div>

        <div class="bg-gradient-to-b from-slate-100 to-white min-h-[520px] px-5 pt-10 pb-6">
            <div class="flex flex-col items-center text-center">
                <img
                    src="{{ asset($app->logo) }}"
                    alt="Demo profil görseli"
                    class="h-20 w-20 rounded-full object-cover border-4 border-white shadow-md bg-white"
                    width="80"
                    height="80"
                >
                <p class="mt-4 text-lg font-bold text-slate-900">@demo.profil</p>
                <p class="mt-1 text-sm text-slate-500 max-w-[220px]">
                    İçerik üreticisi · Linkler, projeler ve iletişim
                </p>

                <div class="mt-4 flex items-center gap-3 text-slate-500" aria-hidden="true">
                    <span class="h-8 w-8 rounded-full bg-white border border-slate-200 grid place-items-center text-xs font-semibold text-pink-600">IG</span>
                    <span class="h-8 w-8 rounded-full bg-white border border-slate-200 grid place-items-center text-xs font-semibold text-emerald-600">WA</span>
                    <span class="h-8 w-8 rounded-full bg-white border border-slate-200 grid place-items-center text-xs font-semibold text-blue-700">in</span>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                <div class="rounded-xl bg-slate-900 text-white text-sm font-medium py-3 px-4 text-center shadow-sm">
                    Portfolyo
                </div>
                <div class="rounded-xl bg-white border border-slate-200 text-slate-800 text-sm font-medium py-3 px-4 text-center">
                    İletişim
                </div>
                <div class="rounded-xl bg-white border border-slate-200 text-slate-800 text-sm font-medium py-3 px-4 text-center">
                    Son projeler
                </div>
            </div>

            <p class="mt-8 text-center text-[11px] text-slate-400">
                Demo önizleme
            </p>
        </div>
    </div>
</div>
