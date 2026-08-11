<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\PaymentGateway;
use App\Models\PricingPlan;
use Illuminate\Http\Request;
use App\Models\User;
use App\Rules\XSSPurifier;
use Inertia\Inertia;

class PlanController extends Controller
{
    public function get_plan()
    {
        try {
            $plans = PricingPlan::where('status', 'active')->get();

            return Inertia::render('CurrentPlan/Select', compact('plans'));
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    // -----------------------------------------


    // -----------------------------------------
    // Getting specific user plan
    public function user_plan()
    {
        try {
            $user = AppHelper::user();
            $SA = $user->hasRole('SUPER-ADMIN');
            $plan = PricingPlan::when(!$SA, function ($query) use ($user) {
                return $query->where('id', $user->pricing_plan_id);
            })->first();

            return Inertia::render('CurrentPlan/Show', compact('plan'));
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    // -----------------------------------------


    // Get payment gateway page
    public function selected(Request $request, $id)
    {
        try {
            $type = $request->type;
            $plan = PricingPlan::where('id', $id)->first();
            $methods = PaymentGateway::where('active', true)->get();

            // return Inertia::render('UpdatePlan/Update', compact('plan', 'type', 'methods'));
            return view('pages.checkout', compact('plan', 'type', 'methods'));
        } catch (\Throwable $th) {
            return back()->with('error', 'Sunucu hatası. Lütfen daha sonra tekrar deneyin.');
        }
    }


    public function get_admin_plan()
    {
        try {
            $plans = PricingPlan::all();

            return Inertia::render('Admin/PricingPlans/Show', compact('plans'));
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    // -----------------------------------------

    // Create plan page
    public function create_plan()
    {
        try {
            return Inertia::render('Admin/PricingPlans/Create');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
    // -----------------------------------------


    // Created plan store
    public function store_plan(Request $req)
    {
        $req->validate([
            'name' => ['required', 'max:50'],
            'description' => ['required', 'max:100'],
            'monthly_price' => ['required'],
            'yearly_price' => ['required'],
            'currency' => ['required'],
            'status' => ['required'],
            'biolinks' => ['required'],
            'biolink_blocks' => ['required'],
            'shortlinks' => ['required'],
            'projects' => ['required'],
            'qrcodes' => ['required'],
            'themes' => ['required'],
            'custom_theme' => ['required'],
            'support' => ['required'],
        ]);

        try {
            PricingPlan::create($req->all());

            return redirect()->route('plans')->with('success', 'Yeni fiyatlandırma planı oluşturuldu.');
        } catch (\Throwable $th) {
            return redirect()->route('plans')->with('error', $th->getMessage());
        }
    }
    // -----------------------------------------


    // Plan update page
    public function get_update_plan(Request $req, $id)
    {
        try {
            $plan = PricingPlan::where('id', $id)->first();

            return Inertia::render('Admin/PricingPlans/Update', compact('plan'));
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    // Update pricing plan details
    public function update_plan(Request $req, $id)
    {
        $req->validate([
            'name' => ['required', 'max:50'],
            'description' => ['required', 'max:100'],
            'monthly_price' => ['required'],
            'yearly_price' => ['required'],
            'currency' => ['required'],
            'status' => ['required'],
            'biolinks' => ['required'],
            'biolink_blocks' => ['required'],
            'shortlinks' => ['required'],
            'projects' => ['required'],
            'qrcodes' => ['required'],
            'themes' => ['required'],
            'custom_theme' => ['required'],
            'support' => ['required'],
        ]);

        try {
            PricingPlan::where('id', $id)->update([
                'name' => $req->name,
                'description' => $req->description,
                'monthly_price' => $req->monthly_price,
                'yearly_price' => $req->yearly_price,
                'currency' => $req->currency,
                'status' => $req->status,
                'biolinks' => $req->biolinks,
                'biolink_blocks' => $req->biolink_blocks,
                'shortlinks' => $req->shortlinks,
                'projects' => $req->projects,
                'qrcodes' => $req->qrcodes,
                'themes' => $req->themes,
                'custom_theme' => $req->custom_theme,
                'support' => $req->support,
            ]);

            return redirect()->route('plans')->with('success', 'Fiyatlandırma planı başarıyla güncellendi.');
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->route('plans')->with('error', $th->getMessage());
        }
    }
    // -----------------------------------------


    // -----------------------------------------
    // When user select pro plan to basic plan
    public function basic_plan($id)
    {
        $plan = PricingPlan::where('name', 'BASIC')
            ->where('status', 'active')
            ->where('monthly_price', 0)
            ->where('yearly_price', 0)
            ->firstOrFail();

        try {
            $user = User::where('id', auth()->user()->id)->first();

            $user->pricing_plan_id = $plan->id;
            $user->next_payment = null;
            $user->subscription_id = null;
            $user->recurring = null;
            $user->save();

            return redirect()
                ->route('plan')
                ->with('success', 'Planınız başarıyla temel plana geçirildi.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
