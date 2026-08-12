<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Link;
use App\Models\Theme;
use App\Models\QRCode;
use App\Models\LinkItem;
use App\Rules\XSSPurifier;
use App\Helpers\AppHelper;
use App\Models\SocialLinks;
use Illuminate\Http\Request;
use App\Models\CustomTheme;
use App\Models\PricingPlan;
use App\Models\ShetabitVisit;
use App\Rules\CheckLinkName;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use App\Jobs\UpdateVisitLocationJob;
use App\Support\SafeTheme;
use App\Support\SafeUrl;
use Illuminate\Validation\ValidationException;

class BioLinkController extends Controller
{
    // Getting the total bio-links of users
    public function index(Request $req)
    {
        try {
            $linkLimit = 0;
            $user = AppHelper::user();
            $SA = $user->hasRole('SUPER-ADMIN');
            $plan = PricingPlan::where('id', $user->pricing_plan_id)->first();
            $page = $req->per_page ? intval($req->per_page) : 10;

            $links = Link::when(!$SA, function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
                ->where('link_type', 'biolink')
                ->orderBy('created_at', 'desc')
                ->with('qrcode')
                ->withCount('visited')
                ->paginate($page);

            $limit = AppHelper::limit_checker('biolinks', $links->count());

            return Inertia::render('BioLinks/Show', compact('links', 'limit'));
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }
    // -------------------------------------------------


    // -------------------------------------------------
    // Creating a new bio-link
    function create(Request $req)
    {
        $user = auth()->user();

        // Sahte link / spam önlemi: Kullanıcı başına saatte en fazla 10 link oluşturma
        $rateKey = 'bio-link-create:' . $user->id;
        if (RateLimiter::tooManyAttempts($rateKey, 10)) {
            return back()->with('error', 'Çok fazla deneme. Lütfen bir saat sonra tekrar deneyin.');
        }
        RateLimiter::hit($rateKey, 3600);

        $current = Link::where('user_id', $user->id)->where('link_type', 'biolink')->count();
        $limit = AppHelper::limit_checker('biolinks', $current);
        if ($limit) {
            return back()->with("error", $limit);
        }

        $req->validate([
            'link_name' => ['required', 'string', 'min:5', 'max:50', new XSSPurifier],
            'url_name' => ['required', 'string', 'unique:links', 'min:5', 'max:50', new XSSPurifier, new CheckLinkName],
        ]);

        $theme = Theme::get()->first();
        if (!$theme) {
            return back()->with('error', 'Sistemde henüz tema tanımlı değil. Lütfen yöneticiden tema eklemesini isteyin.');
        }

        try {
            $trimUrl = trim(str_replace(" ", "", $req->url_name));
            $urlName = preg_replace("/\s+/", "", strtolower($trimUrl));

            $link = new Link;
            $link->user_id = $user->id;
            $link->link_name = $req->link_name;
            $link->url_name = $urlName;
            $link->theme_id = $theme->id;
            $link->save();

            return back()->with('success', 'Link başarıyla oluşturuldu.');
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }
    //--------------------------------------------------


    //----------------------------------------------------
    // Bio-link name or username updating
    public function update(Request $req, $id)
    {
        $link = AppHelper::get_link($id);
        if (!$link) {
            abort(403, 'Yetkisiz erişim.');
        }

        $req->validate([
            'link_name' => ['required', 'string', 'min:5', 'max:50', new XSSPurifier]
        ]);
        if ($req->new_url) {
            $req->validate([
                'url_name' => ['required', 'string', 'min:5', 'max:50', new XSSPurifier, new CheckLinkName, Rule::unique('links', 'url_name')->ignore($id)]
            ]);
        }

        try {
            $link->link_name = $req->link_name;
            if ($req->new_url) $link->url_name = $req->url_name;
            $link->save();

            return response(['success' => 'Bio link başarıyla güncellendi.', 'link' => $link]);
        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------


    //----------------------------------------------
    // Delete a bio-link
    public function delete($id)
    {
        $link = AppHelper::get_link($id);
        if (!$link) {
            abort(403, 'Yetkisiz erişim.');
        }

        try {
            LinkItem::where('link_id', $link->id)->delete();
            if ($link->qrcode_id) {
                QRCode::where('id', $link->qrcode_id)->delete();
            }
            $link->delete();

            return back()->with('success', 'Link başarıyla silindi.');
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }
    //--------------------------------------------------


    //--------------------------------------------------------
    // Getting the single bio-link to to customize
    function customize($id)
    {
        $user = auth()->user();
        $themes = Theme::all();
        $socialLinks = SocialLinks::all();
        $itemLastPosition = LinkItem::where('link_id', $id)->max('item_position');

        $link = AppHelper::get_link($id);
        if (!$link) {
            abort(403, 'Yetkisiz erişim.');
        }

        return Inertia::render(
            'BioLinks/AddItem',
            compact('link', 'socialLinks', 'themes', 'itemLastPosition')
        );
    }
    //--------------------------------------------------------


    //----------------------------------------------------
    // Socials links updating of bio-link
    public function add_socials(Request $req, $id)
    {
        $link = AppHelper::get_link($id);
        if (!$link) {
            abort(403, 'Yetkisiz erişim.');
        }

        try {
            $link->socials = $this->sanitizedSocials($req->socials);
            $color = SafeTheme::hex(is_string($req->social_color) ? $req->social_color : null);
            $link->social_color = $color ?? '#101828';
            $link->save();

            $updatedLink = AppHelper::get_link($id);
            return response($updatedLink);
        } catch (ValidationException $e) {
            return response([
                'error' => collect($e->errors())->flatten()->first() ?? 'Geçersiz sosyal bağlantı.',
            ]);
        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------


    //----------------------------------------------------
    //Bio-link profile updating
    public function update_profile(Request $req, $id)
    {
        $link = AppHelper::get_link($id);
        if (!$link) {
            abort(403, 'Yetkisiz erişim.');
        }

        try {
            $rules = ['link_bio' => 'max:200'];
            $messages = ['link_bio.max' => 'Bio description length must be 1 to 200 characters'];

            $this->validate($req, $rules, $messages,);

            if ($req->hasFile('thumbnail')) {
                $rules = ['thumbnail' => AppHelper::imageRules(1024)];
                $messages = [
                    'thumbnail.max' => 'Image size will be 1MB',
                    'thumbnail.image' => 'Allow only jpg, png, jpeg type image',
                ];

                $this->validate($req, $rules, $messages,);
                AppHelper::safeDeleteUpload($link->thumbnail);
                $imgUrl = AppHelper::image_uploader($req->file('thumbnail'));

                $link->link_name = $req->link_name;
                $link->short_bio = $req->short_bio;
                $link->thumbnail = $imgUrl;
                $link->save();
            } else {
                $link->link_name = $req->link_name;
                $link->short_bio = $req->short_bio;
                $link->save();
            }

            $updatedLink = AppHelper::get_link($id);
            return response(['success' => true, 'link' => $updatedLink]);
        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------


    //----------------------------------------------------
    // Bio-link name or username updating
    public function update_logo(Request $req, $id)
    {
        $link = AppHelper::get_link($id);
        if (!$link) {
            abort(403, 'Yetkisiz erişim.');
        }

        if ($req->hasFile('branding')) {
            $req->validate([
                'branding' => AppHelper::imageRules(2048),
            ]);
        }

        try {
            if ($req->hasFile('branding')) {
                AppHelper::safeDeleteUpload($link->branding);
                $link->branding = AppHelper::image_uploader($req->file('branding'));
                $link->save();
            }

            $updatedLink = AppHelper::get_link($id);
            return response(['success' => true, 'link' => $updatedLink]);
        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------


    //----------------------------------------------
    // Changing the current theme of bio-link
    function update_theme($themeId, $linkId)
    {
        $link = AppHelper::get_link($linkId);
        if (!$link) {
            abort(403, 'Yetkisiz erişim.');
        }

        try {
            $link->custom_theme_active = FALSE;
            $link->theme_id = (int) $themeId;
            $link->save();

            $updatedLink = AppHelper::get_link($linkId);
            return response(['success' => true, 'link' => $updatedLink]);
        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------


    //----------------------------------------------
    // Creating custom theme for user bio-link
    function custom_theme_create(Request $req, $id)
    {
        $link = AppHelper::get_link($id);
        if (!$link) {
            abort(403, 'Yetkisiz erişim.');
        }

        try {
            $bgColor = SafeTheme::hex(is_string($req->bg_color) ? $req->bg_color : null) ?? SafeTheme::DEFAULT_BG;
            $textColor = SafeTheme::hex(is_string($req->text_color) ? $req->text_color : null) ?? SafeTheme::DEFAULT_TEXT;
            $btnBg = SafeTheme::hex(is_string($req->btn_bg_color) ? $req->btn_bg_color : null) ?? SafeTheme::DEFAULT_BTN_BG;
            $btnText = SafeTheme::hex(is_string($req->btn_text_color) ? $req->btn_text_color : null) ?? SafeTheme::DEFAULT_BTN_TEXT;
            $font = SafeTheme::font(is_string($req->font_family) ? $req->font_family : null) ?? SafeTheme::DEFAULT_FONT;
            $radius = SafeTheme::radius(is_string($req->btn_radius) ? $req->btn_radius : null) ?? SafeTheme::DEFAULT_RADIUS;
            $btnType = SafeTheme::buttonType(is_string($req->btn_type) ? $req->btn_type : null) ?? 'rounded';

            $theme = new CustomTheme();
            $theme->link_id = $id;
            $theme->background = SafeTheme::colorBackground($bgColor);
            $theme->background_type = 'color';
            $theme->bg_color = $bgColor;
            $theme->text_color = $textColor;
            $theme->btn_type = $btnType;
            $theme->btn_transparent = filter_var($req->btn_transparent, FILTER_VALIDATE_BOOLEAN);
            $theme->btn_radius = $radius;
            $theme->btn_bg_color = $btnBg;
            $theme->btn_text_color = $btnText;
            $theme->font_family = $font;
            $theme->save();

            $link->custom_theme_active = TRUE;
            $link->custom_theme_id = $theme->id;
            $link->save();

            $updatedLink = AppHelper::get_link($id);
            return response(['success' => true, 'link' => $updatedLink]);
        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------


    //----------------------------------------------
    // Activating the user custom theme for bio-link
    function custom_theme_active($id)
    {
        $link = AppHelper::get_link($id);
        if (!$link) {
            abort(403, 'Yetkisiz erişim.');
        }

        try {
            $link->custom_theme_active = TRUE;
            $link->save();

            $updatedLink = AppHelper::get_link($id);
            return response(['success' => true, 'link' => $updatedLink]);
        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------


    //-------------------------------------------
    // Updating the user custom theme
    function custom_theme_update(Request $req, $themeId, $linkId)
    {
        $link = AppHelper::get_link($linkId);
        if (!$link) {
            abort(403, 'Yetkisiz erişim.');
        }

        $theme = CustomTheme::where('id', $themeId)->where('link_id', $linkId)->first();
        if (!$theme) {
            abort(403, 'Yetkisiz erişim.');
        }

        try {
            switch ($req->type) {
                case 'bg_color':
                    $hex = SafeTheme::hex(is_string($req->bg_color) ? $req->bg_color : null);
                    if ($hex === null) {
                        return response(['error' => 'Geçersiz renk.']);
                    }
                    $theme->background = SafeTheme::colorBackground($hex);
                    $theme->background_type = "color";
                    $theme->bg_color = $hex;
                    break;

                case 'bg_image':
                    $rules = [
                        'bg_image' => AppHelper::imageRules(5120),
                    ];
                    $messages = [
                        'bg_image.image' => 'Allow only jpg, png, jpeg type image',
                        'bg_image.max' => 'Image size will be 5MB',
                    ];

                    $this->validate($req, $rules, $messages,);
                    AppHelper::safeDeleteUpload($theme->bg_image);

                    $imgUrl = AppHelper::image_uploader($req->file('bg_image'));

                    $theme->background = SafeTheme::imageBackground($imgUrl);
                    $theme->background_type = "image";
                    $theme->bg_image = $imgUrl;
                    break;

                case 'text_color':
                    $hex = SafeTheme::hex(is_string($req->text_color) ? $req->text_color : null);
                    if ($hex === null) {
                        return response(['error' => 'Geçersiz renk.']);
                    }
                    $theme->text_color = $hex;
                    break;

                case 'button':
                    $btnType = SafeTheme::buttonType(is_string($req->btn_type) ? $req->btn_type : null);
                    $radius = SafeTheme::radius(is_string($req->btn_radius) ? $req->btn_radius : null);
                    if ($btnType === null || $radius === null) {
                        return response(['error' => 'Geçersiz buton stili.']);
                    }
                    $theme->btn_type = $btnType;
                    $theme->btn_transparent = filter_var($req->btn_transparent, FILTER_VALIDATE_BOOLEAN);
                    $theme->btn_radius = $radius;
                    break;

                case 'btn_bg_color':
                    $hex = SafeTheme::hex(is_string($req->btn_bg_color) ? $req->btn_bg_color : null);
                    if ($hex === null) {
                        return response(['error' => 'Geçersiz renk.']);
                    }
                    $theme->btn_bg_color = $hex;
                    break;

                case 'btn_text_color':
                    $hex = SafeTheme::hex(is_string($req->btn_text_color) ? $req->btn_text_color : null);
                    if ($hex === null) {
                        return response(['error' => 'Geçersiz renk.']);
                    }
                    $theme->btn_text_color = $hex;
                    break;

                case 'font_family':
                    $font = SafeTheme::font(is_string($req->font_family) ? $req->font_family : null);
                    if ($font === null) {
                        return response(['error' => 'Geçersiz yazı tipi.']);
                    }
                    $theme->font_family = $font;
                    break;

                default:
                    break;
            }
            $theme->save();

            $updated_link = AppHelper::get_link($linkId);
            return response(['result' => $updated_link]);
        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()]);
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

            $baseQuery = Link::where('link_type', 'biolink')
                ->where('link_name', 'LIKE', '%' . $query . '%');

            if (!$user->hasRole('SUPER-ADMIN')) {
                $baseQuery->where('user_id', $user->id);
            }

            $links = $baseQuery
                ->orderBy('created_at', 'desc')
                ->with('qrcode')
                ->withCount('visited')
                ->paginate($page);

            return $links;
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    //----------------------------------------------


    //----------------------------------------------
    // Access the main bio-link page
    function bioLinkView(Request $req, $linkName)
    {
        try {
            $link = Link::where('url_name', $linkName)
                ->with('items')
                ->with('theme')
                ->with('custom_theme')
                ->first();
            if ($link) {
                $model = new ShetabitVisit;
                $result = $req->visitor()->visit($model);

                ShetabitVisit::where('id', $result->id)->update([
                    'link_id' => $link->id,
                    // Privacy quick win: drop bulky/sensitive request dumps from new rows.
                    'request' => null,
                    'headers' => null,
                ]);

                UpdateVisitLocationJob::dispatch($result->id, $req->ip());

                if ($link->link_type == 'shortlink') {
                    return redirect()->to(url($link->external_url));
                } else {
                    return Inertia::render('BioLinks/View', compact('link'));
                }
            } else {
                abort(404);
            }
        } catch (\Throwable $th) {
            abort(404);
        }
    }
    //--------------------------------------------------

    private function sanitizedSocials(mixed $raw): string
    {
        $list = $raw;
        if (is_string($raw)) {
            $list = json_decode($raw, true);
        }
        if (!is_array($list)) {
            throw ValidationException::withMessages([
                'socials' => 'Geçersiz sosyal bağlantı verisi.',
            ]);
        }

        $out = [];
        foreach ($list as $item) {
            if (!is_array($item)) {
                continue;
            }
            $name = isset($item['name']) && is_string($item['name']) ? $item['name'] : '';
            $link = isset($item['link']) && is_string($item['link']) ? trim($item['link']) : '';
            $icon = isset($item['icon']) && is_string($item['icon']) ? $item['icon'] : '';
            if ($name === '' || $link === '') {
                continue;
            }

            if ($name === 'email') {
                $email = preg_replace('/^mailto:/i', '', $link) ?? $link;
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw ValidationException::withMessages([
                        'socials' => 'Geçersiz e-posta adresi.',
                    ]);
                }
                $link = $email;
            } elseif ($name === 'telephone' || $name === 'whatsapp') {
                $digits = preg_replace('/[^\d+]/', '', $link) ?? '';
                if (!preg_match('/^\+?\d{7,20}$/', $digits)) {
                    throw ValidationException::withMessages([
                        'socials' => 'Geçersiz telefon numarası.',
                    ]);
                }
            } else {
                $canonical = SafeUrl::canonicalize($link);
                if ($canonical === null) {
                    throw ValidationException::withMessages([
                        'socials' => 'Geçersiz sosyal medya bağlantısı.',
                    ]);
                }
                $link = $canonical;
            }

            $out[] = [
                'name' => $name,
                'link' => $link,
                'icon' => $icon,
            ];
        }

        return json_encode($out);
    }
}
