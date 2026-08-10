<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Intervention\Image\ImageManagerStatic as Image;

class AccountController extends Controller
{
    public function index()
    {
        try {
            return Inertia::render('Account');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    //--------------------------------------------------------
    // basic updating of user profile
    function UpdateSetting(Request $req)
    {
        $user = auth()->user();
        $name = $req->input('name');
        $phone = $req->input('phone');

        if ($req->image) {
            File::delete($user->image);

            $location = public_path('/upload/');
            $image = Image::make($req->image);
            $image->save($location . time() . $req->image->getClientOriginalName());

            User::where('id', '=', $user->id)->update([
                'name' => $name,
                'phone' => $phone,
                'image' => 'upload/' . $image->filename . '.' . $image->extension,
            ]);
        } else {
            $data = ['name' => $name, 'phone' => $phone];
            User::where('id', '=', $user->id)->update($data);
        }

        return back();
    }
    //--------------------------------------------------------


    //--------------------------------------------------------
    // Updating or changing the user current mail address
    public function UpdateEmail(Request $req)
    {
        $user = auth()->user();

        $req->validate([
            'current_email' => 'required',
            'email' => 'required|string|email|max:55|unique:users',
        ]);

        $isMatch = $req->current_email == $user->email ? true : false;

        if ($isMatch) {
            try {
                $status = setSmtpConfig();
                if ($status) {
                    User::where('id', $user->id)->update([
                        'email' => $req->email,
                        'email_verified_at' => NULL,
                    ]);

                    $updatedUser = User::where('id', $user->id)->first();
                    $updatedUser->sendEmailVerificationNotification($req->email);
                    return redirect()->back()->with('success', 'Email Successfully Changed');
                } else {
                    return redirect()->back()->with('error', 'SMTP Info may have not been set yet.');
                }
            } catch (\Throwable $th) {
                User::where('id', $user->id)->update([
                    'email' => $user->email,
                    'email_verified_at' => now(),
                ]);
                return redirect()->back()->with('error', 'Something was wrong with smtp info or smtp info may not be correct.');
            }
        } else {
            return redirect()->back()->with('error', 'Current Email Not Matched');
        }
    }
    //--------------------------------------------------------


    //--------------------------------------------------------
    // Updating or changing the user current password
    public function UpdatePassword(Request $req)
    {
        $user = auth()->user();

        $req->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $isMatch = Hash::check($req->current_password, $user->password);

        if ($isMatch) {
            $hashedPassword = Hash::make($req->password);

            User::where('id', '=', $user->id)->update(['password' => $hashedPassword]);
            return redirect()->back()->with('success', 'Şifre başarıyla güncellendi.');
        } else {
            return redirect()->back()->with('error', 'Current Password Not Matched');
        }
    }
    //--------------------------------------------------------
}
