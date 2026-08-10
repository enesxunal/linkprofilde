<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use App\Models\Link;
use App\Models\Theme;
use App\Models\QRCode;
use App\Models\Language;
use App\Models\CustomTheme;
use App\Models\LinkItem;
use App\Models\PricingPlan;
use App\Models\ShetabitVisit;
use App\Rules\CheckLinkName;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Stevebauman\Location\Facades\Location;
use Intervention\Image\ImageManagerStatic as Image;

use function PHPSTORM_META\type;

class LinksController extends Controller
{
    // Getting the total bio-links of user
    public function BioLinks(Request $req)
    {
        try {
            $linkLimit = 0;
            $user = auth()->user();
            $SA = $user->hasRole('SUPER-ADMIN');
            $plan = PricingPlan::where('id', $user->pricing_plan_id)->first();

            if ($SA) {
                $links = Link::where('link_type', 'biolink')
                    ->orderBy('created_at', 'desc')
                    ->with('qrcode')
                    ->with('visited')
                    ->paginate(10);
            } else {
                $links = Link::where('user_id', $user->id)
                    ->where('link_type', 'biolink')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
            }

            return Inertia::render('BioLinks/Show', compact('links'));
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }
    // -------------------------------------------------


    // Getting the total bio-links of user
    public function GetShortLinks(Request $req)
    {
        $linkLimit = 0;
        $user = auth()->user();
        $SA = $user->hasRole('SUPER-ADMIN');
        $plan = PricingPlan::where('id', $user->pricing_plan_id)->first();

        if ($SA) {
            $links = Link::where('link_type', 'shortlink')->orderBy('created_at', 'desc')->paginate(10);
            return view('pages.admin.short-links', compact('links'));
        } else {
            $links = Link::where('user_id', $user->id)->where('link_type', 'shortlink')->orderBy('created_at', 'desc')->paginate(10);

            if ($plan->biolinks == 'Limitsiz') {
                $limit_over = FALSE;
                return view('pages.short_links', compact('links', 'limit_over'));
            }

            if ($links->count() >=  intval($plan->biolinks)) {
                $limit_over = TRUE;
                return view('pages.short_links', compact('links', 'limit_over'));
            } else {
                $limit_over = FALSE;
                return view('pages.short_links', compact('links', 'limit_over'));
            }
        }
    }
    // -------------------------------------------------


    // -------------------------------------------------
    // Creating a new bio-link
    function CreateLink(Request $req)
    {
        $user = auth()->user();
        $app = AppSetting::get()->first();

        if ($req->link_type == 'shortlink') {
            $req->validate([
                'link_name' => ['required', 'string', 'min:5', 'max:255'],
                'external_url' => 'required|min:1|max:255|url',
            ]);

            $link_key = rand(10000000, 90000000);
            $short_link = base_convert($link_key, 10, 36);

            $link = new Link;
            $link->user_id = $user->id;
            $link->link_name = $req->link_name;
            $link->link_type = $req->link_type;
            $link->url_name = $short_link;
            $link->external_url = $req->external_url;
            $link->save();
        } else {
            $req->validate([
                'link_name' => ['required', 'string', 'min:5', 'max:255'],
                'url_name' => ['required', 'unique:links', 'string', 'min:5', 'max:255', new CheckLinkName]
            ]);

            $theme = Theme::get()->first();
            $trimUrl = trim(str_replace(" ", "", $req->url_name));
            $urlName = preg_replace("/\s+/", "", strtolower($trimUrl));

            $link = new Link;
            $link->user_id = $user->id;
            $link->link_name = $req->link_name;
            $link->url_name = $urlName;
            $link->theme_id = $theme->id;
            $link->branding = $app->logo;
            $link->save();
        }

        return back()->with('success', 'Link created successfully');
    }
    //--------------------------------------------------


    //----------------------------------------------------
    // Bio-link name or username updating
    public function UpdateLink(Request $req, $linkId)
    {
        $urlName = preg_replace("/\s+/", "", strtolower($req->url_name));

        if ($req->link_type && $req->link_type == 'shortlink') {

            $rules = ['external_url' => 'required|url'];
            $messages = ['external_url.url' => 'Please provide a valid url.'];
            $this->validate($req, $rules, $messages,);

            Link::where('id', $linkId)->update([
                'link_name' => $req->link_name,
                'external_url' => $req->external_url,
            ]);

            return back();
        } else {
            if ($req->url_name) {
                $rules = ['url_name' => ['unique:links', new CheckLinkName]];
                $messages = ['url_name.unique' => 'URL name already used, please try another.'];
                $this->validate($req, $rules, $messages);
            } else {
                $urlName = sprintf('%04x%04x', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
            }

            $link = AppHelper::get_link($linkId);

            return back();
        }
    }
    //--------------------------------------------------











    //----------------------------------------------
    // Delete a bio-link
    function DeleteLink($linkId)
    {
        $link = Link::find($linkId);
        LinkItem::where('item_link', $link->url_name)->delete();
        if ($link->qrcode_id) {
            QRCode::find($link->qrcode_id)->delete();
        }
        $link->delete();

        return back()->with('success', 'Link deleted successfully');
    }
    //--------------------------------------------------















}
