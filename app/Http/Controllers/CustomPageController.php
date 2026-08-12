<?php

namespace App\Http\Controllers;

use App\Models\AppSection;
use App\Models\AppSetting;
use App\Models\CustomPage;
use App\Support\PageHtml;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CustomPageController extends Controller
{
    function index()
    {
        try {
            $custom_pages = CustomPage::all();

            return Inertia::render('Admin/CustomPage/Show', compact('custom_pages'));
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    function create()
    {
        try {
            return Inertia::render('Admin/CustomPage/Create');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    function store(Request $request)
    {
        $data = $this->validatedPage($request);

        try {
            CustomPage::create($data);

            return redirect()
                ->route('custom-page')
                ->with('success', 'A new page created successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    function pageView(Request $request, $page)
    {
        $currentPage = CustomPage::where('route', $page)->first();
        if (!$currentPage) {
            abort(404);
        }

        try {
            $app = AppSetting::first();
            $customPages = CustomPage::all();
            $appSections = AppSection::all();
            $safeContent = PageHtml::sanitize((string) $currentPage->content);

            return view(
                'custom-page',
                compact('app', 'customPages', 'currentPage', 'appSections', 'safeContent')
            );
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    function update($id)
    {
        try {
            $custom_page = CustomPage::find($id);

            return Inertia::render('Admin/CustomPage/Update', compact('custom_page'));
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    function save(Request $request, $id)
    {
        $page = CustomPage::find($id);
        if (!$page) {
            abort(404);
        }

        $data = $this->validatedPage($request);

        try {
            $page->update($data);

            return redirect()
                ->route('custom-page')
                ->with('success', 'Page updated successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    function delete($id)
    {
        try {
            CustomPage::find($id)->delete();

            return back()->with('success', 'Page deleted successfully.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    private function validatedPage(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:30'],
            'route' => ['required', 'string', 'max:30', 'regex:/^[a-z]+(?:-[a-z]+)*$/'],
            'content' => ['required', 'string'],
        ]);

        $html = PageHtml::sanitize($data['content']);
        if ($html === '') {
            throw ValidationException::withMessages([
                'content' => 'The content contains invalid HTML.',
            ]);
        }

        return [
            'name' => $data['name'],
            'route' => $data['route'],
            'content' => $html,
        ];
    }
}
