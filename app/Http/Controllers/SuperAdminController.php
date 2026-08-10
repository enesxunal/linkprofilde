<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\User;
use App\Models\Theme;
use App\Models\Testimonial;
use App\Rules\XSSPurifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class SuperAdminController extends Controller
{
    // -------------------------------------------
    // Getting user list
    public function users(Request $req)
    {
        try {
            $page = $req->per_page ? intval($req->per_page) : 10;
            $suspiciousOnly = $req->boolean('suspicious');

            $users = User::whereDoesntHave('roles', function ($query) {
                $query->where('name', 'SUPER-ADMIN');
            })
                ->when($suspiciousOnly, fn ($q) => $q->suspicious())
                ->orderBy('created_at', 'desc')
                ->with('pricing_plan')
                ->paginate($page)
                ->withQueryString();

            return Inertia::render('Admin/Users', compact('users', 'suspiciousOnly'));
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    // -------------------------------------------


    // -------------------------------------------
    // Getting user list
    public function users_update(Request $req, $id)
    {
        try {
            $user = User::find($id);
            $user->status = $req->status;
            $user->save();

            return response(['success' => 'Kullanıcı hesap durumu başarıyla güncellendi.', 'user' => $user]);
        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()]);
        }
    }
    // -------------------------------------------


    // -------------------------------------------
    // Getting user list
    public function users_search(Request $req)
    {
        try {
            $query = $req->value;
            $page = $req->per_page ? intval($req->per_page) : 10;
            $suspiciousOnly = $req->boolean('suspicious');

            $users = User::where(function ($user) use ($query) {
                $user->where('name', 'LIKE', '%' . $query . '%')
                    ->orWhere('email', 'LIKE', '%' . $query . '%');
            })
                ->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'SUPER-ADMIN');
                })
                ->when($suspiciousOnly, fn ($q) => $q->suspicious())
                ->orderBy('created_at', 'desc')
                ->with('pricing_plan')
                ->paginate($page);

            return $users;
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    // -------------------------------------------


    // Managing theme 
    public function ManageThemes()
    {
        try {
            $themes = Theme::all();

            return Inertia::render('Admin/ManageThemes', compact("themes"));
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }

    public function TypeThemes(Request $request, $id)
    {
        try {
            $theme = Theme::find($id);
            $theme->type = $request->type;
            $theme->save();

            return back()->with('success', 'Tema tipi güncellendi.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Sunucu hatası. Lütfen daha sonra tekrar deneyin.');
        }
    }


    // Getting testimonials
    public function Testimonials()
    {
        try {
            $testimonials = Testimonial::all();
            return Inertia::render('Admin/Testimonials', compact('testimonials'));
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    // Add new testimonial
    public function AddTestimonial(Request $req)
    {
        $req->validate([
            'name' => ['required', 'max:50', new XSSPurifier],
            'title' => ['required', 'max:50', new XSSPurifier],
            'testimonial' => ['required', 'max:180', new XSSPurifier],
            'thumbnail' => ['required', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
        ]);

        try {
            $imgUrl = AppHelper::image_uploader($req->thumbnail);

            $res = new Testimonial();
            $res->name = $req->name;
            $res->title = $req->title;
            $res->thumbnail = $imgUrl;
            $res->testimonial = $req->testimonial;
            $res->save();

            return back()->with('success', "Müşteri yorumu başarıyla eklendi.");
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    // Add new testimonial
    public function UpdateTestimonial(Request $req, $id)
    {
        $req->validate([
            'name' => ['required', 'max:50', new XSSPurifier],
            'title' => ['required', 'max:50', new XSSPurifier],
            'testimonial' => ['required', 'max:180', new XSSPurifier],
        ]);

        if ($req->thumbnail) {
            $req->validate(['thumbnail' => ['image', 'mimes:jpeg,png,jpg,svg', 'max:2048']]);
        }

        try {
            $tes = Testimonial::find($id);
            $tes->name = $req->name;
            $tes->title = $req->title;
            $tes->testimonial = $req->testimonial;

            if ($req->thumbnail) {
                if ($tes->thumbnail) File::delete($tes->thumbnail);
                $tes->thumbnail = AppHelper::image_uploader($req->thumbnail);
            }

            $tes->save();

            return back()->with('success', "Müşteri yorumu başarıyla güncellendi.");
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    // Delete a testimonial
    public function DeleteTestimonial($testimonialId)
    {
        try {
            $testimonial = Testimonial::find($testimonialId);
            File::delete($testimonial->thumbnail);
            $testimonial->delete();

            return back()->with('success', "Müşteri yorumu başarıyla silindi.");
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    // Updating user role
    public function UpdateUser(Request $req, $id)
    {
        try {
            $user = User::where('id', $id)->first();
            $user->status = $req->status;
            $user->save();

            return back()->with(['success' => 'Kullanıcı durumu başarıyla güncellendi.']);
        } catch (\Throwable $th) {
            //throw $th;
            return back()->with(['error' => $th->getMessage()]);
        }
    }
}
