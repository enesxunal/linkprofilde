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
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request)
    {
        // return Inertia::render('Auth/Register');
        $app = AppSetting::first();
        $google = SocialLogin::where('name', 'google')->first();
        $linkname = $request->linkname;

        return view('auth.register', compact('app', 'google', 'linkname'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $theme = Theme::get()->first();
        $plan = PricingPlan::where('name', 'BASIC')->first();

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                function ($attribute, $value, $fail) {
                    $name = trim($value);
                    // Tek kelime ve 25+ karakter → muhtemel sahte (örn. rastgele yapıştırılmış)
                    if (strlen($name) > 25 && ! str_contains($name, ' ')) {
                        $fail('Lütfen gerçek adınızı veya anlamlı bir kullanıcı adı girin.');
                    }
                },
            ],
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'url_name' => ['required', 'string', 'max:50', 'unique:links', new CheckLinkName],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ])->assignRole('BASIC');
        $user->pricing_plan_id = $plan->id;
        $user->save();

        $link = new Link();
        $link->user_id = $user->id;
        $link->link_name = $request->name;
        $link->url_name = $request->url_name;
        $link->theme_id = $theme->id;
        $link->save();

        AppHelper::smtp();
        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
