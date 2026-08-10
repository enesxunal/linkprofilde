<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Link;
use Illuminate\Http\Request;
use App\Models\QRCode;
use App\Models\Project;
use App\Rules\XSSPurifier;
use Inertia\Inertia;

class QRCodeController extends Controller
{
    //---------------------------------------------------
    // Getting all the qr-code of user or admin
    public function index(Request $req)
    {
        try {
            $user = auth()->user();
            $SA = $user->hasRole('SUPER-ADMIN');
            $page = $req->per_page ? intval($req->per_page) : 10;

            $qrcodes = QRCode::when(!$SA, function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
                ->orderBy('created_at', 'desc')
                ->with('link')
                ->with('project')
                ->paginate($page);

            $limit = AppHelper::limit_checker('qrcodes', $qrcodes->count());

            return Inertia::render('QRCodes/Show', compact('qrcodes', 'limit'));
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }
    //---------------------------------------------------


    //---------------------------------------------------
    // Accessing qr-code editor page
    function create()
    {
        $user = auth()->user();
        $current = QRCode::where('user_id', $user->id)->count();
        $limit = AppHelper::limit_checker('qrcodes', $current);
        if ($limit) return back()->with("error", $limit);

        try {
            $user = auth()->user();
            $projects = Project::where('user_id', $user->id)->get();

            return Inertia::render('QRCodes/Create', compact('projects'));
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }
    //---------------------------------------------------


    //---------------------------------------------------
    // Creating a new qr-code for project
    function save_qr(Request $req)
    {
        $req->validate([
            'qr_code' => ['required'],
            'project_id' => ['required'],
            'content' => ['required', 'string', 'max:500', new XSSPurifier],
            'name' => ['nullable', 'string', 'max:100', new XSSPurifier],
        ]);

        try {
            $user = auth()->user();

            $result = new QRCode;
            $result->user_id = $user->id;
            $result->project_id = $req->project_id;
            $result->name = $req->name ? trim($req->name) : null;
            $result->qr_type = $req->qr_type;
            $result->content = $req->content;
            $result->img_data = $req->qr_code;
            $result->save();

            return redirect()->to('/qrcodes')->with('success', 'QR kod başarıyla oluşturuldu.');
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }
    //---------------------------------------------------


    //--------------------------------------------------
    // Searching of short links
    public function save_link_qr(Request $req)
    {
        $req->validate([
            'link_id' => ['required', 'exists:links,id'],
            'qr_code' => ['required'],
            'qr_type' => ['required'],
            'content' => ['required', 'string', 'max:500'],
            'name' => ['nullable', 'string', 'max:100', new XSSPurifier],
        ]);

        try {
            $user = auth()->user();
            $link = Link::find($req->link_id);

            $qrCode = new QRCode;
            $qrCode->user_id = $user->id;
            $qrCode->link_id = $link->id;
            $qrCode->name = $req->name ? trim($req->name) : null;
            $qrCode->qr_type = $req->qr_type;
            $qrCode->content = $req->content;
            $qrCode->img_data = $req->qr_code;
            $qrCode->save();

            $link->qrcode_id = $qrCode->id;
            $link->save();

            return back()->with('success', 'QR kod başarıyla oluşturuldu.');
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }
    //--------------------------------------------------


    //---------------------------------------------------
    // Delete qr code from bio-link or project
    function delete($id)
    {
        try {
            $qrCode = QRCode::find($id);
            if ($qrCode->link_id) {
                Link::where('id', $qrCode->link_id)->update(['qrcode_id' => NULL]);
            }

            $qrCode->delete();

            return back()->with('success', 'QR kod başarıyla silindi.');
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }
    //---------------------------------------------------
}
