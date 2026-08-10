<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Link;
use App\Models\Theme;
use App\Models\LinkItem;
use App\Models\PricingPlan;
use App\Models\SocialLinks;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class LinkItemsController extends Controller
{
    //--------------------------------------------------------
    // Getting the user bio-link elements
    function EditBioLink($id)
    {
        $user = auth()->user();
        $themes = Theme::all();
        $socialLinks = SocialLinks::all();
        $itemLastPosition = LinkItem::where('link_id', $id)->max('item_position');

        $link = AppHelper::get_link($id);
        if (!$link) back()->with("error", "Link bulunamadı.");

        return Inertia::render('BioLinks/AddItem', compact('link', 'socialLinks', 'themes', 'itemLastPosition'));
    }
    //--------------------------------------------------------








    //--------------------------------------------------------
    // Controlling the tap panel of bio-link editor page
    function BtnController(Request $req)
    {
        $type = $req->input('type');

        if ($type == 'settings') {
            session()->forget(['settings', 'blocks']);
            session(['settings' => true, 'blocks' => false]);
        } else if ($type == 'blocks') {
            session()->forget(['blocks', 'blocks']);
            session(['settings' => false, 'blocks' => true]);
        }
        return;
    }
    //--------------------------------------------------------
}
