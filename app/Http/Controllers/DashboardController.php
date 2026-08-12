<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Project;
use App\Models\QRCode;
use App\Services\Analytics\LinkAnalyticsService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct(
        private LinkAnalyticsService $analytics
    ) {}

    public static function overview_counter($links, $analytics, $projects, $qrcodes)
    {
        $overview = [
            [
                'icon' => 'fa-solid fa-link-simple',
                'title' => 'Toplam link',
                'total' => count($links),
            ],
            [
                'icon' => 'fa-regular fa-eye',
                'title' => 'Link sayfa görüntüleme',
                'total' => count($analytics),
            ],
            [
                'icon' => 'fa-solid fa-list-check',
                'title' => 'Toplam proje',
                'total' => count($projects),
            ],
            [
                'icon' => 'fa-regular fa-qrcode',
                'title' => 'Toplam QR kod',
                'total' => count($qrcodes),
            ],
        ];

        return $overview;
    }

    public function index()
    {
        try {
            $user = auth()->user();
            $isSuperAdmin = $user->hasRole('SUPER-ADMIN');

            if ($isSuperAdmin) {
                $links = Link::query()->count();
                $qrcodes = QRCode::query()->count();
                $projects = Project::query()->count();
                [$linkIds, $visitsQuery] = $this->analytics->dashboardVisitsQuery($user);
                $analytics = (clone $visitsQuery)->count();
                $visitors = $this->analytics->monthlyCounts($linkIds);
                $page_view = $this->analytics->lastSevenDaysCounts($linkIds);
            } else {
                $links = Link::query()->where('user_id', $user->id)->count();
                $qrcodes = QRCode::query()->where('user_id', $user->id)->count();
                $projects = Project::query()->where('user_id', $user->id)->count();
                [$linkIds, $visitsQuery] = $this->analytics->dashboardVisitsQuery($user);
                $analytics = (clone $visitsQuery)->count();
                $visitors = $this->analytics->monthlyCounts($linkIds);
                $page_view = $this->analytics->lastSevenDaysCounts($linkIds);
            }

            return Inertia::render(
                'Dashboard',
                compact('qrcodes', 'links', 'analytics', 'projects', 'visitors', 'page_view')
            );
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
