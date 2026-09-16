<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\Link;
use App\Models\PricingPlan;
use App\Models\SocialLogin;
use App\Models\Theme;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\CheckLinkName;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    public function create(Request $request)
    {
        $app = AppSetting::first();
        $google = SocialLogin::where('name', 'google')->first();
        $linkname = $request->linkname;

        return view('auth.register', compact('app', 'google', 'linkname'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                function ($attribute, $value, $fail) {
                    $name = trim($value);
                    if (strlen($name) > 25 && ! str_contains($name, ' ')) {
                        $fail('Lütfen gerçek adınızı veya anlamlı bir kullanıcı adı girin.');
                    }
                },
            ],
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'url_name' => ['required', 'string', 'min:5', 'max:50', 'unique:links,url_name', new CheckLinkName],
        ]);

        $theme = Theme::query()->orderBy('id')->first();
        $plan = PricingPlan::where('name', 'BASIC')->first();

        if (! $theme || ! $plan) {
            throw ValidationException::withMessages([
                'email' => 'Kayıt işlemi şu anda kullanılamıyor. Lütfen daha sonra tekrar deneyin.',
            ]);
        }

        try {
            [$user, $link] = DB::transaction(function () use ($request, $theme, $plan) {
                $user = User::create([
                    'name' => trim($request->name),
                    'email' => strtolower(trim($request->email)),
                    'password' => Hash::make($request->password),
                ]);

                $user->assignRole('BASIC');
                $user->pricing_plan_id = $plan->id;
                $user->save();

                $link = new Link();
                $link->user_id = $user->id;
                $link->link_name = trim($request->name);
                $link->url_name = strtolower(trim($request->url_name));
                $link->link_type = 'biolink';
                $link->theme_id = $theme->id;
                $link->save();

                return [$user, $link];
            });
        } catch (\Throwable $th) {
            report($th);

            throw ValidationException::withMessages([
                'email' => AppHelper::publicExceptionMessage(
                    $th,
                    'Hesabınız oluşturulamadı. Lütfen tekrar deneyin.'
                ),
            ]);
        }

        Auth::login($user);
        $request->session()->put('onboarding_link_id', $link->id);

        try {
            AppHelper::smtp();
            event(new Registered($user));
        } catch (\Throwable $th) {
            report($th);
        }

        return redirect(RouteServiceProvider::HOME)
            ->with('success', 'Hesabınız oluşturuldu. E-posta doğrulamasından sonra profilinizi tamamlayabilirsiniz.');
    }
}
