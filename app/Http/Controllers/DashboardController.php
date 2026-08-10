<?php

namespace App\Http\Controllers;

use App\Models\QRCode;
use App\Models\Link;
use App\Models\Project;
use App\Models\ShetabitVisit;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public static function overview_counter($links, $analytics, $projects, $qrcodes) 
    {
        $overview = [
            [
                "icon"=>"fa-solid fa-link-simple",
                'title'=>"Toplam link", 
                "total"=>count($links)
            ],
            [
                "icon"=>"fa-regular fa-eye",
                'title'=>"Link sayfa görüntüleme", 
                "total"=>count($analytics)
            ],
            [
                "icon"=>"fa-solid fa-list-check",
                'title'=>"Toplam proje", 
                "total"=>count($projects)
            ],
            [
                "icon"=>"fa-regular fa-qrcode",
                'title'=>"Toplam QR kod", 
                "total"=>count($qrcodes)
            ],
        ];

        return $overview;
    }

    public static function visitors_counter($analytics) 
    {
        $currentYear = date('Y');
        $counting = array_fill(1, 12, 0);

        foreach ($analytics as $item) {
            $year = $item->created_at->format('Y');
            if ($year == $currentYear) {
                $monthNum = (int) $item->created_at->format('n');
                $counting[$monthNum]++;
            }
        }

        return array_values($counting);
    }

    public static function weekly_page_view($analytics) 
    {
        // Counting the weekly page view
        $weeklyPageView = [0, 0, 0, 0, 0, 0, 0];
        foreach($analytics as $item){
            $day = $item->created_at->format('d');
            $year = $item->created_at->format('Y');
            $month = $item->created_at->format('m');
            
            if ($year == date("Y") && $month == date("m")) {
                for ($i=6, $j=0; $i >= 0 ; $i--, $j++) { 
                    $d=strtotime("-{$i} Days");
                    $countDay = date("d", $d);
                    if ($countDay == $day) {
                        $weeklyPageView[$j]++;
                    }
                }
            }
        };

        return $weeklyPageView;
    }


    public function index() 
    {
        try {
            $user = auth()->user();
            $SA = $user->hasRole('SUPER-ADMIN');
            
            if ($SA) {
                $links = Link::all()->count();
                $qrcodes = QRCode::all()->count();
                $projects = Project::all()->count();
                $analytics = ShetabitVisit::all();

                $visitors = self::visitors_counter($analytics);
                $page_view = self::weekly_page_view($analytics);
                $analytics = $analytics->count();
            } else {
                $links = Link::where('user_id', $user->id)->get()->count();
                $qrcodes = QRCode::where('user_id', $user->id)->get()->count();
                $projects = Project::where('user_id', $user->id)->get()->count();
                $analytics = ShetabitVisit::where('visitor_id', $user->id)->get();

                $visitors = self::visitors_counter($analytics);
                $page_view = self::weekly_page_view($analytics);
                $analytics = $analytics->count();   
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
