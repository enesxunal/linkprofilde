<?php

namespace App\Http\Controllers;

use App\Services\Analytics\LinkAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LinkAnalyticsController extends Controller
{
    public function __construct(
        private LinkAnalyticsService $analytics
    ) {}

    /**
     * Aggregated analytics for a bio link or short link.
     */
    public function show(Request $request, int $id)
    {
        $user = $request->user();
        $link = $this->analytics->findAuthorizedLink($user, $id);

        try {
            $range = $this->analytics->resolveDateRange($request);
        } catch (ValidationException $e) {
            throw $e;
        }

        $analytics = $this->analytics->buildForLink($link, $range);

        return Inertia::render('LinkAnalytics', [
            'analytics' => $analytics,
        ]);
    }
}
