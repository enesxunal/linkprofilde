<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use Illuminate\Http\Request;
use App\Models\Link;
use App\Models\QRCode;
use App\Models\LinkItem;
use App\Models\PricingPlan;
use App\Rules\CheckLinkName;
use App\Rules\XSSPurifier;
use Illuminate\Validation\Rule;
use Inertia\Inertia;


class ShortLinkController extends Controller
{
    // Getting the total bio-links of user
    public function index(Request $req)
    {
        try {
            $linkLimit = 0;
            $user = auth()->user();
            $SA = $user->hasRole('SUPER-ADMIN');
            $plan = PricingPlan::where('id', $user->pricing_plan_id)->first();
            $page = $req->per_page ? intval($req->per_page) : 10;

            $links = Link::when(!$SA, function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
                ->where('link_type', 'shortlink')
                ->orderBy('created_at', 'desc')
                ->with('qrcode')
                ->with('visited')
                ->paginate($page);

            $limit = AppHelper::limit_checker('shortlinks', $links->count());

            return Inertia::render('ShortLinks/Show', compact('links', 'limit'));
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }
    // -------------------------------------------------


    // -------------------------------------------------
    // Creating a new short-link
    function create(Request $req)
    {
        $user = auth()->user();
        $current = Link::where('user_id', $user->id)->where('link_type', 'shortlink')->count();
        $limit = AppHelper::limit_checker('shortlinks', $current);
        if ($limit) {
            return back()->with("error", $limit);
        }

        $req->validate([
            'link_name' => ['required', 'string', 'min:5', 'max:255', new XSSPurifier],
            'external_url' => ['required', 'min:10', 'max:255', 'url', new XSSPurifier],
        ]);

        if ($req->link_slug) {
            $req->validate([
                'link_slug' => [
                    'string',
                    'min:8',
                    'max:50',
                    new XSSPurifier,
                    new CheckLinkName,
                    Rule::unique('links', 'url_name')->where(function ($query) use ($req) {
                        return $query->where('url_name', $req->link_slug);
                    }),
                ],
            ]);
        }

        try {
            $short_link = "";
            if ($req->link_slug) {
                $short_link = $req->link_slug;
            } else {
                $link_key = rand(10000000, 90000000);
                $short_link = base_convert($link_key, 10, 36);
            }

            $link = new Link;
            $link->user_id = $user->id;
            $link->link_name = $req->link_name;
            $link->link_type = $req->link_type;
            $link->url_name = $short_link;
            $link->external_url = $req->external_url;
            $link->save();

            return back()->with('success', 'Kısa link başarıyla oluşturuldu.');
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }
    //--------------------------------------------------


    //----------------------------------------------------
    // Bio-link name or username updating
    public function update(Request $req, $id)
    {
        $req->validate([
            'link_name' => ['required', 'string', 'min:5', 'max:255', new XSSPurifier],
            'external_url' => ['required', 'min:10', 'max:255', 'url', new XSSPurifier],
        ]);

        $query = Link::where('id', $id)->where('link_type', 'shortlink');
        if (!auth()->user()->hasRole('SUPER-ADMIN')) {
            $query->where('user_id', auth()->id());
        }
        $link = $query->firstOrFail();

        try {
            $link->link_name = $req->link_name;
            $link->external_url = $req->external_url;
            $link->save();

            return response(['success' => 'Kısa link başarıyla güncellendi.', 'link' => $link]);
        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------


    //----------------------------------------------
    // Delete a bio-link
    public function delete($id)
    {
        $query = Link::where('id', $id)->where('link_type', 'shortlink');
        if (!auth()->user()->hasRole('SUPER-ADMIN')) {
            $query->where('user_id', auth()->id());
        }
        $link = $query->firstOrFail();

        try {
            LinkItem::where('item_link', $link->url_name)->delete();
            if ($link->qrcode_id) {
                $qrQuery = QRCode::where('id', $link->qrcode_id)
                    ->where('link_id', $link->id);

                if (!auth()->user()->hasRole('SUPER-ADMIN')) {
                    $qrQuery->where('user_id', auth()->id());
                }

                $qrQuery->delete();
            }
            $link->delete();

            return back()->with('success', 'Link başarıyla silindi.');
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }
    //--------------------------------------------------



    //--------------------------------------------------
    // Searching of short links
    public function search(Request $request)
    {
        try {
            $user = auth()->user();
            $query = $request->value;
            $page = $request->per_page ? intval($request->per_page) : 10;

            $baseQuery = Link::where('link_type', 'shortlink');

            if (!$user->hasRole('SUPER-ADMIN')) {
                $baseQuery->where('user_id', $user->id);
            }

            $links = $baseQuery
                ->where('link_name', 'LIKE', '%' . $query . '%')
                ->orderBy('created_at', 'desc')
                ->with('qrcode')
                ->with('visited')
                ->paginate($page);

            return $links;
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------
}
