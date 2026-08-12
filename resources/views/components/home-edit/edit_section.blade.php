<div
  data-dialog-backdrop="{{ $dialog }}"
  data-dialog-backdrop-close="true"
  class="pointer-events-none fixed inset-0 z-[999] grid h-screen w-screen place-items-center bg-black bg-opacity-60 hidden backdrop-blur-sm transition-opacity duration-300 p-4"
>
   <div
      data-dialog="{{ $dialog }}"
      class="relative min-w-[300px] max-w-[600px] w-full max-h-[calc(100vh-100px)] overflow-y-auto rounded-lg bg-white font-sans text-base leading-relaxed antialiased shadow-2xl p-4"
   >
      <div class="flex items-center justify-between mb-8">
         <p class="text-lg font-medium">{{ __('Bölümü Düzenle') }}: {{ $sections->name }}</p>
         <button type="button" class="text-3xl leading-none cursor-pointer text-slate-500 hover:text-slate-800" data-dialog-close="true" aria-label="{{ __('Kapat') }}">×</button>
      </div>

      <form method="POST" action="/home-section/edit/{{ $sections->id }}" enctype="multipart/form-data">
         @csrf
         @method('PUT')

         <div class="mb-4">
            <label for="section_title_{{ $dialog }}">{{ __('Başlık') }}</label>
            <input
               required
               id="section_title_{{ $dialog }}"
               name="section_title"
               placeholder="{{ __('Başlık') }}"
               value="{{ $sections->title }}"
               class="w-full py-1.5 px-2 mt-2 border border-gray-200 focus:border-blue-500 focus:outline-0 rounded-md"
            >
            @error('section_title')
               <small class="text-xs text-red-500">{{ $message }}</small>
            @enderror
         </div>

         @if ($sections->description !== null)
            <div class="mb-4">
               <label for="description_{{ $dialog }}">{{ __('Açıklama') }}</label>
               <textarea
                  rows="3"
                  id="description_{{ $dialog }}"
                  name="description"
                  placeholder="{{ __('Açıklama') }}"
                  class="w-full px-2 mt-2 rounded-md border border-gray-200"
               >{{ $sections->description }}</textarea>
               @error('description')
                  <small class="text-xs text-red-500">{{ $message }}</small>
               @enderror
            </div>
         @endif

         @if ($sections->thumbnail)
            <div class="mb-4">
               <img
                  alt="{{ __('Mevcut görsel') }}"
                  width="100%"
                  id="currentThumbnail{{ $sections->id }}"
                  src="{{ asset($sections->thumbnail) }}"
               >
               <input name="current_thumbnail" value="{{ $sections->thumbnail }}" hidden>

               <label class="block mb-2 mt-3" for="newThumbnail{{ $sections->id }}">
                  {{ __('Görseli Değiştir') }}
               </label>

               <input
                  type="file"
                  name="new_thumbnail"
                  id="newThumbnail{{ $sections->id }}"
                  accept="image/jpeg,image/png,image/jpg,image/webp"
               >

               @error('new_thumbnail')
                  <small class="text-xs text-red-500">{{ $message }}</small>
               @enderror
            </div>
         @endif

         <div class="flex shrink-0 flex-wrap items-center justify-end gap-2">
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
