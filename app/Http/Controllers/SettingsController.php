<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\User;
use App\Rules\XSSPurifier;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ChangeEmailVerification;
use App\Models\AppSetting;

class SettingsController extends Controller
{
    public function index()
    {
        try {
            return Inertia::render('Settings');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    //--------------------------------------------------------
    // basic updating of user profile
    function profile(Request $req)
    {
        $req->validate([
            'name' => ['required', 'string', 'max:50', new XSSPurifier],
        ]);
        if (strlen($req->phone) > 0) {
            $req->validate([
                'phone' => ['string', 'max:20', new XSSPurifier],
            ]);
        }
        if ($req->hasFile('image')) {
            $req->validate([
                'image' => AppHelper::imageRules(2048),
            ]);
        }

        try {
            $user = User::find(auth()->user()->id);
            $user->name = $req->name;
            $user->phone = $req->phone;

            if ($req->hasFile('image')) {
                AppHelper::safeDeleteUpload($user->image);
                $user->image = AppHelper::image_uploader($req->file('image'));
            }
            $user->save();

            return back()->with('success', 'Profil başarıyla güncellendi.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    //--------------------------------------------------------


    public function change_email(Request $request)
    {
        $request->validate([
            'current_email' => 'required|string|email|max:55||exists:users,email',
            'new_email' => 'required|string|email|max:55|unique:users,email',
        ]);

        try {
            AppHelper::smtp();
            $app = AppSetting::first();
            $user = User::find(auth()->user()->id);

            // Generate a unique token for email verification
            $token = Str::random(60);
            $url = route('save.email', ['token' => $token]);

            // Store the email change request in the database
            $user->email_change_new_email = $request->new_email;
            $user->email_change_token = $token;
            $user->save();

            // Send an email with the verification link to the new email
            Mail::to($request->new_email)->send(new ChangeEmailVerification($user, $app, $url));

            return back()->with('success', 'Yeni e-posta adresinize doğrulama bağlantısı gönderildi.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function save_email(Request $request)
    {
        try {
            $user = User::find(auth()->user()->id);

            if ($request->token !== $user->email_change_token) {
                return redirect()->route('settings')->with('error', "Doğrulama kodu eşleşmedi veya süresi doldu.");
            }
            $user->email = $user->email_change_new_email;
            $user->email_change_new_email = null;
            $user->email_change_token = null;
            $user->save();

            return redirect()->route('settings')->with('success', "E-posta adresi başarıyla güncellendi.");
        } catch (\Throwable $th) {
            return redirect()->route('settings')->with('error', $th->getMessage());
        }
    }
}
