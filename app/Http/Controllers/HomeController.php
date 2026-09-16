<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\AppSection;
use App\Models\AppSetting;
use App\Models\CustomPage;
use App\Models\PricingPlan;
use App\Models\Testimonial;
use App\Support\SafeUrl;
use Illuminate\Http\Request;


class HomeController extends Controller
{
    public function Home(Request $request)
    {
        $app = AppSetting::first();
        if (!$app) {
            $app = (object) [];
        }

        $app->title = filled($app->title ?? null)
            ? $app->title
            : config('app.name', 'LinkProfilde');
        $app->name = filled($app->name ?? null)
            ? $app->name
            : $app->title;
        $app->description = filled($app->description ?? null)
            ? $app->description
            : 'Dijital profil, kısa link, QR kod ve analitik araçlarını tek panelden yönetin.';
        $app->logo = filled($app->logo ?? null)
            ? $app->logo
            : 'assets/icons/link-drop.png';

        $appSections = AppSection::all();
        $customPages = CustomPage::all();
        $testimonials = Testimonial::all();
        $plans = PricingPlan::where('status', 'active')->get();
        $user = auth()->user();
        $customize = false;
        $SA = false;
        if ($user) {
            $SA = $user->hasRole('SUPER-ADMIN');
            if ($SA && $request->customize) {
                $customize = true;
            } else {
                $customize = false;
            }
        }

        return view(
            'pages.home',
            compact('app', 'plans', 'appSections', 'customPages', 'testimonials', 'customize', 'SA')
        );
    }


    //-------------------------------------------------
    // Section edit or update of home page
    public function EditHomeSection(Request $req, $sectionId)
    {
        $section = AppSection::find($sectionId);
        if (!$section) {
            abort(404);
        }

        $section_title = ucfirst($req->section_title);

        if ($req->hasFile('new_thumbnail')) {
            $rules = [
                'section_title' => 'required',
                'new_thumbnail' => AppHelper::imageRules(5120),
            ];
            $messages = [
                'section_title.required' => 'Bölüm başlığı zorunludur.',
                'new_thumbnail.mimes' => 'Yalnızca JPG, JPEG veya PNG görseller yüklenebilir.',
                'new_thumbnail.max' => 'Görsel boyutu en fazla 5 MB olabilir.',
            ];
            $this->validate($req, $rules, $messages);

            AppHelper::safeDeleteUpload($section->thumbnail);
            $section->thumbnail = AppHelper::image_uploader($req->file('new_thumbnail'));
        }

        $section->title = $section_title;
        $section->description = $req->description ? $req->description : null;
        $section->save();

        return back();
    }


    function EditSectionList(Request $req, $sectionId)
    {
        try {
            $section = AppSection::find($sectionId);
            if (!$section) {
                abort(404);
            }

            $section_list = $req->section_list;
            if (is_array($section_list)) {
                foreach ($section_list as &$item) {
                    if (isset($item['url']) && $item['url'] !== null && $item['url'] !== '') {
                        $safe = SafeUrl::canonicalHttpUrl($item['url']);
                        if ($safe === null) {
                            return back()->with('error', 'Geçersiz bağlantı adresi.');
                        }
                        $item['url'] = $safe;
                    }
                }
                unset($item);
            }

            $section->section_list = json_encode($section_list, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $section->save();

            return back()->with('success', 'Bölüm içeriği güncellendi.');
        } catch (\Throwable $th) {
            report($th);
            return back()->with('error', 'Bölüm güncellenemedi. Lütfen tekrar deneyin.');
        }
    }
}
