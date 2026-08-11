<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\AppSection;
use App\Models\AppSetting;
use App\Models\CustomPage;
use App\Models\PricingPlan;
use App\Models\Testimonial;
use Illuminate\Http\Request;


class HomeController extends Controller
{
    public function Home(Request $request)
    {
        $app = AppSetting::first();
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
                'section_title.required' => 'Section Title is require',
                'new_thumbnail.mimes' => 'Allow only jpg, png, jpeg type image',
                'new_thumbnail.max' => 'Image size will be 5MB',
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
    //-------------------------------------------------


    //-------------------------------------------------
    // Section edit or update of home page
    public function EditSectionList(Request $req, $sectionId)
    {
        $allList = [];
        $oneList = ['content' => '', 'icon' => '', 'url' => ''];

        for ($i = 1; $i <= count($req->all()) - 2; $i++) {

            foreach ($req->all() as $key => $value) {
                if ($key != '_token' && $key != '_method') {
                    $str = substr($key, -1);
                    $newKey = substr($key, 0, -1);
                    $number =  (int) $str;

                    if ($i == $number) {
                        $oneList[$newKey] = $value;
                    }
                }
            }

            if ($oneList['content'] == '' && $oneList['icon'] == '' && $oneList['url'] == '') {
                break;
            } else {
                array_push($allList, $oneList);
                $oneList = ['content' => '', 'icon' => '', 'url' => ''];
            }
        }

        AppSection::where('id', $sectionId)->update([
            'section_list' => json_encode($allList)
        ]);

        return back();
    }
    //-------------------------------------------------
}
