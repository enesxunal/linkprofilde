<div
   data-dialog-backdrop="{{ $dialog }}"
   data-dialog-backdrop-close="true"
   class="pointer-events-none fixed inset-0 z-[999] grid h-screen w-screen place-items-center bg-black bg-opacity-60 hidden backdrop-blur-sm transition-opacity duration-300 p-4"
>
   <div
      data-dialog="{{ $dialog }}"
      class="relative min-w-[300px] max-w-[600px] w-full max-h-[calc(100vh-100px)] rounded-lg bg-white font-sans text-base leading-relaxed antialiased shadow-2xl p-4 overflow-auto"
   >
      <div class="flex items-center justify-between mb-8">
         <p class="text-lg font-medium">{{ __('Listeyi Düzenle') }}: {{ $sections->name }}</p>
         <button type="button" class="text-3xl leading-none cursor-pointer text-slate-500 hover:text-slate-800" data-dialog-close="true" aria-label="{{ __('Kapat') }}">×</button>
      </div>

      <form method="POST" action="/home-section/edit-list/{{ $sections->id }}">
         @csrf
         @method('PUT')

         <?php $counter = 0; ?>
         @foreach (json_decode($sections->section_list) as $list)
            <?php
               $counter++;
               $encode = json_encode($list);
               $item = json_decode($encode, true);
            ?>
            @if ($item['content'] && $item['url'])
               <div class="mb-3 text-start">
                  <div class="border border-gray-200 rounded-md flex items-center overflow-hidden">
                     <span class="max-w-[96px] py-2 px-3 bg-gray-100 text-sm">{{ __('İçerik') }}</span>
                     <input required name="content{{ $counter }}" value="{{ $item['content'] }}" class="w-full p-2 focus:outline-0">
                  </div>
                  <input hidden name="icon{{ $counter }}" value="{{ $item['icon'] }}">
                  <div class="border border-gray-200 rounded-md flex items-center overflow-hidden mt-1">
                     <span class="max-w-[96px] py-2 px-3 bg-gray-100 whitespace-nowrap text-sm">{{ __('Bağlantı') }}</span>
                     <input required name="url{{ $counter }}" value="{{ $item['url'] }}" class="w-full p-2 focus:outline-0">
                  </div>
               </div>
            @elseif ($item['url'])
               <div class="mb-3 text-start">
                  <div class="border border-gray-200 rounded-md flex items-center overflow-hidden">
                     <span class="max-w-[96px] py-2 px-3 bg-gray-100 whitespace-nowrap text-sm">{{ __('Bağlantı') }}</span>
                     <input required name="url{{ $counter }}" value="{{ $item['url'] }}" class="w-full p-2 focus:outline-0">
                  </div>
                  <input hidden name="icon{{ $counter }}" value="{{ $item['icon'] }}">
                  <input hidden name="content{{ $counter }}" value="{{ $item['content'] }}">
               </div>
            @else
               <div class="mb-3 text-start">
                  <div class="border border-gray-200 rounded-md flex items-center overflow-hidden">
                     <span class="max-w-[96px] py-2 px-3 bg-gray-100 text-sm">{{ __('İçerik') }}</span>
                     <input required name="content{{ $counter }}" value="{{ $item['content'] }}" class="w-full p-2 focus:outline-0">
                  </div>
                  <input hidden name="icon{{ $counter }}" value="{{ $item['icon'] }}">
                  <input hidden name="url{{ $counter }}" value="{{ $item['url'] }}">
               </div>
            @endif
         @endforeach

         <div class="flex shrink-0 flex-wrap items-center justify-end gap-2 mt-4">
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
               {{ __('Kaydet') }}
            </button>
         </div>
      </form>
   </div>
</div>
