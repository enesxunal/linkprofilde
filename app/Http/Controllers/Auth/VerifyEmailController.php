<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if (! $request->user()->hasVerifiedEmail()) {
            if ($request->user()->markEmailAsVerified()) {
                event(new Verified($request->user()));
            }
        }

        $onboardingLinkId = $request->session()->pull('onboarding_link_id');
        if ($onboardingLinkId) {
            $link = Link::query()
                ->where('id', $onboardingLinkId)
                ->where('user_id', $request->user()->id)
                ->where('link_type', 'biolink')
                ->first();

            if ($link) {
                return redirect('/bio-links/customize/' . $link->id . '?onboarding=1');
            }
        }

        return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
    }
}
