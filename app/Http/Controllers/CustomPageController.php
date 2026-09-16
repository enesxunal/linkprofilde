<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\AppSection;
use App\Models\AppSetting;
use App\Models\CustomPage;
use App\Support\PageHtml;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CustomPageController extends Controller
{
    public function index()
    {
        try {
            $custom_pages = CustomPage::query()->orderBy('created_at', 'desc')->get();

            return Inertia::render('Admin/CustomPage/Show', compact('custom_pages'));
        } catch (\Throwable $th) {
            return back()->with('error', AppHelper::publicExceptionMessage($th));
        }
    }

    public function create()
    {
        return Inertia::render('Admin/CustomPage/Create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedPage($request);

        try {
            CustomPage::create($data);

            return redirect()
                ->route('custom-page')
                ->with('success', 'Sayfa başarıyla oluşturuldu.');
        } catch (\Throwable $th) {
            return back()->with('error', AppHelper::publicExceptionMessage(
                $th,
                'Sayfa oluşturulamadı. Lütfen tekrar deneyin.'
            ));
        }
    }

    public function pageView(Request $request, $page)
    {
        $currentPage = CustomPage::where('route', $page)->first();
        if (! $currentPage) {
            abort(404);
        }

        try {
            $app = AppSetting::first() ?? (object) [
                'title' => config('app.name', 'LinkProfilde'),
                'name' => config('app.name', 'LinkProfilde'),
                'description' => 'Dijital profil ve bağlantı yönetimi.',
                'logo' => 'favicon.ico',
            ];
            $customPages = CustomPage::all();
            $appSections = AppSection::all();
            $safeContent = PageHtml::sanitize((string) $currentPage->content);

            return view(
                'custom-page',
                compact('app', 'customPages', 'currentPage', 'appSections', 'safeContent')
            );
        } catch (\Throwable $th) {
            report($th);
            abort(500);
        }
    }

    public function update($id)
    {
        $custom_page = CustomPage::findOrFail($id);

        return Inertia::render('Admin/CustomPage/Update', compact('custom_page'));
    }

    public function save(Request $request, $id)
    {
        $page = CustomPage::findOrFail($id);
        $data = $this->validatedPage($request, (int) $page->id);

        try {
            $page->update($data);

            return redirect()
                ->route('custom-page')
                ->with('success', 'Sayfa başarıyla güncellendi.');
        } catch (\Throwable $th) {
            return back()->with('error', AppHelper::publicExceptionMessage(
                $th,
                'Sayfa güncellenemedi. Lütfen tekrar deneyin.'
            ));
        }
    }

    public function delete($id)
    {
        $page = CustomPage::findOrFail($id);

        try {
            $page->delete();

            return back()->with('success', 'Sayfa başarıyla silindi.');
        } catch (\Throwable $th) {
            return back()->with('error', AppHelper::publicExceptionMessage(
                $th,
                'Sayfa silinemedi. Lütfen tekrar deneyin.'
            ));
        }
    }

    private function validatedPage(Request $request, ?int $ignoreId = null): array
    {
        $routeRules = [
            'required',
            'string',
            'max:30',
            'regex:/^[a-z]+(?:-[a-z]+)*$/',
            Rule::unique('custom_pages', 'route')->ignore($ignoreId),
        ];

        $data = $request->validate([
            'name' => ['required', 'string', 'max:30'],
            'route' => $routeRules,
            'content' => ['required', 'string', 'max:200000'],
        ], [
            'route.regex' => 'Sayfa yolu yalnızca küçük harf ve tire içerebilir.',
            'route.unique' => 'Bu sayfa yolu zaten kullanılıyor.',
            'content.max' => 'Sayfa içeriği çok uzun.',
        ]);

        $html = PageHtml::sanitize($data['content']);
        if ($html === '') {
            throw ValidationException::withMessages([
                'content' => 'Sayfa içeriği boş veya geçersiz HTML içeriyor.',
            ]);
        }

        return [
            'name' => trim($data['name']),
            'route' => strtolower(trim($data['route'])),
            'content' => $html,
        ];
    }
}
