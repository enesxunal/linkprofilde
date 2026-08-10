<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use App\Models\Link;
use App\Models\Theme;
use App\Models\QRCode;
use App\Models\Language;
use App\Models\SocialLinks;
use App\Models\CustomTheme;
use App\Models\LinkItem;
use App\Models\PricingPlan;
use App\Models\ShetabitVisit;
use App\Rules\CheckLinkName;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Stevebauman\Location\Facades\Location;
use Intervention\Image\ImageManagerStatic as Image;

class BioLinkBlockController extends Controller
{
    //--------------------------------------------------------
    // Add new element of bio-link
    public function add(Request $req)
    {
        $link = AppHelper::get_link((int) $req->link_id);
        if (!$link) {
            abort(403, 'Yetkisiz erişim.');
        }

        try {
            $item = new LinkItem;
            $item->link_id = (int) $req->link_id;
            $item->item_position = (int) $req->item_position;
            $item->item_type = $req->item_type;
            $item->item_sub_type = $req->item_sub_type == "null" ? NULL : $req->item_sub_type;
            $item->item_title = $req->item_title;
            $item->item_link = $req->item_link == "null" ? NULL : $req->item_link;
            $item->item_icon = $req->item_icon;

            if ($req->image != 'null') {
                $imgUrl = AppHelper::image_uploader($req->image);
                $item->content = $imgUrl;
            } else {
                $item->content = $req->content == 'null' ? NULL : $req->content;
            }
            $item->save();
            $link = AppHelper::get_link($req->link_id);

            return response()->json(['success' => true, 'item' => $item, 'link' => $link]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------------


    //--------------------------------------------------------
    // Updating an element of bio-link
    public function edit(Request $req, $id)
    {
        $item = LinkItem::find($id);
        if (!$item) {
            abort(404);
        }
        $link = AppHelper::get_link($item->link_id);
        if (!$link) {
            abort(403, 'Yetkisiz erişim.');
        }

        try {
            $item->item_type = $req->item_type;
            $item->item_sub_type = $req->item_sub_type == "null" ? NULL : $req->item_sub_type;
            $item->item_title = $req->item_title;
            $item->item_link = $req->item_link == "null" ? NULL : $req->item_link;

            if ($req->image != 'null') {
                if ($item->content && $item->content != 'null') {
                    File::delete(public_path($item->content));
                }
                $imgUrl = AppHelper::image_uploader($req->image);
                $item->content = $imgUrl;
            } else {
                $item->content = $req->content == 'null' ? NULL : $req->content;
            }
            $item->save();
            $link = AppHelper::get_link($req->link_id);

            return response()->json(['success' => true, 'link' => $link]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------------


    //--------------------------------------------------------
    // Updating the position of bio-link elements when user drag and drop on view.
    function position(Request $req, $id)
    {
        $link = AppHelper::get_link($id);
        if (!$link) {
            abort(403, 'Yetkisiz erişim.');
        }

        try {
            $linkItems = $req->input('linkItems');
            $newArr = json_decode(json_encode($linkItems));
            foreach ($newArr as $item) {
                LinkItem::where('id', $item->id)->update([
                    'item_position' => $item->position
                ]);
            }

            $link = AppHelper::get_link($id);
            return response()->json(['success' => true, 'link' => $link]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------------


    //--------------------------------------------------------
    // Delete an element of bio-link
    function delete($id)
    {
        $item = LinkItem::find($id);
        if (!$item) {
            abort(404);
        }
        $link = AppHelper::get_link($item->link_id);
        if (!$link) {
            abort(403, 'Yetkisiz erişim.');
        }

        try {
            $link_id = $item->link_id;

            if ($item->item_type == 'Image' && $item->content) {
                File::delete(public_path($item->content));
            }
            $item->delete();

            $link = AppHelper::get_link($link_id);

            return response()->json(['success' => true, 'link' => $link]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------------
}
