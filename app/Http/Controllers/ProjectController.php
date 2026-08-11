<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\PricingPlan;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\QRCode;
use App\Rules\XSSPurifier;
use Inertia\Inertia;

class ProjectController extends Controller
{
    //-------------------------------------------------------
    // Getting the all project of user or admin
    public function index(Request $req)
    {
        try {
            $user = auth()->user();
            $SA = $user->hasRole('SUPER-ADMIN');
            $page = $req->per_page ? intval($req->per_page) : 10;

            $projects = Project::when(!$SA, function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
                ->orderBy('created_at', 'desc')
                ->with('qrcodes')
                ->paginate($page);

            $limit = AppHelper::limit_checker('projects', $projects->count());

            return Inertia::render('Projects/Show', compact('projects', 'limit'));
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }
    //-------------------------------------------------------


    //-------------------------------------------------------
    // Create a new project
    public function create(Request $req)
    {
        $user = auth()->user();
        $current = Project::where('user_id', $user->id)->count();
        $limit = AppHelper::limit_checker('projects', $current);
        if ($limit) return back()->with("error", $limit);

        $req->validate([
            'project_name' => ['required', 'string', 'max:50', new XSSPurifier]
        ]);

        try {
            $user = auth()->user();

            $result = new Project;
            $result->user_id = $user->id;
            $result->project_name = $req->project_name;
            $result->save();

            return back()->with('success', 'Proje başarıyla oluşturuldu.');
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }
    //-------------------------------------------------------


    //-------------------------------------------------------
    // Create a new project
    public function update(Request $req, $id)
    {
        $req->validate([
            'project_name' => ['required', 'string', 'max:50', new XSSPurifier]
        ]);

        $query = Project::where('id', $id)->with('qrcodes');
        if (!auth()->user()->hasRole('SUPER-ADMIN')) {
            $query->where('user_id', auth()->id());
        }
        $project = $query->firstOrFail();

        try {
            $project->project_name = $req->project_name;
            $project->save();

            return response(['success' => 'Proje başarıyla güncellendi.', 'project' => $project]);
        } catch (\Throwable $th) {
            return response(['error' => $th->getMessage()]);
        }
    }
    //-------------------------------------------------------


    //-------------------------------------------------------
    // Delete project
    public function delete($id)
    {
        $query = Project::where('id', $id);
        if (!auth()->user()->hasRole('SUPER-ADMIN')) {
            $query->where('user_id', auth()->id());
        }
        $project = $query->firstOrFail();

        try {
            if ($project->qrcode_id) {
                QRCode::where('id', $project->qrcode_id)->delete();
            }
            $project->delete();

            return back()->with('success', 'Proje başarıyla silindi.');
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }
    //-------------------------------------------------------


    //--------------------------------------------------
    // Searching of short links
    public function search(Request $request)
    {
        try {
            $user = auth()->user();
            $query = $request->value;
            $SA = $user->hasRole('SUPER-ADMIN');
            $page = $request->per_page ? intval($request->per_page) : 10;

            $projects = Project::when(!$SA, function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
                ->where('project_name', 'LIKE', '%' . $query . '%')
                ->orderBy('created_at', 'desc')
                ->with('qrcodes')
                ->paginate($page);

            return $projects;
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()]);
        }
    }
    //--------------------------------------------------


    //---------------------------------------------------
    // Getting all the qr-code of user or admin
    public function qrcodes($id)
    {
        $query = Project::where('id', $id);
        if (!auth()->user()->hasRole('SUPER-ADMIN')) {
            $query->where('user_id', auth()->id());
        }
        $project = $query->firstOrFail();

        try {
            $project->load(['qrcodes' => function ($query) {
                if (!auth()->user()->hasRole('SUPER-ADMIN')) {
                    $query->where('user_id', auth()->id());
                }

                $query->orderBy('created_at', 'desc');
            }]);

            return Inertia::render('Projects/QRCodes', compact('project'));
        } catch (\Throwable $th) {
            return back()->with("error", $th->getMessage());
        }
    }
    //---------------------------------------------------
}
