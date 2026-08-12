<div
  data-dialog-backdrop="{{ $dialog }}"
  data-dialog-backdrop-close="true"
  class="pointer-events-none fixed inset-0 z-[999] grid h-screen w-screen place-items-center bg-black bg-opacity-60 hidden backdrop-blur-sm transition-opacity duration-300 p-4"
>
   <div
      data-dialog="{{ $dialog }}"
      class="relative min-w-[300px] max-w-[460px] w-full max-h-[calc(100vh-100px)] overflow-y-auto rounded-lg bg-white font-sans text-base leading-relaxed antialiased shadow-2xl p-4"
   >
      <form method="POST" action="{{ route('plan.basic-plan', ['id' => $plan->id]) }}">
         @csrf

         <p class="text-slate-800 text-center py-8 text-lg font-medium">
            {{ __('Mevcut planınızı ücretsiz (BASIC) plana geçirmek istediğinize emin misiniz?') }}
         </p>

         <div class="flex items-center justify-center gap-4 pb-6">
            <button
               type="button"
               data-ripple-dark="true"
               data-dialog-close="true"
               class="rounded-lg py-3 px-6 text-xs font-bold uppercase text-red-500 transition-all hover:bg-red-500/10 border border-red-500"
            >
               {{ __('İptal') }}
            </button>
            <button
               type="submit"
               data-ripple-light="true"
               class="rounded-lg bg-blue-600 py-3 px-6 text-xs font-bold uppercase text-white shadow-sm hover:bg-blue-700"
            >
               {{ __('Onayla') }}
            </button>
         </div>
      </form>
   </div>
</div>
