<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create()
    {
        $app = AppSetting::first();

        return view('auth.forgot-password', compact('app'));
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        try {
            AppHelper::smtp();
            Password::sendResetLink($request->only('email'));
        } catch (\Throwable $th) {
            report($th);
        }

        // Never disclose whether an e-mail address exists in the system.
        return back()->with(
            'success',
            'Bu e-posta adresi sistemde kayıtlıysa şifre sıfırlama bağlantısı gönderildi.'
        );
    }
}
