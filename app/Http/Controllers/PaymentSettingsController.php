<?php

namespace App\Http\Controllers;

use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class PaymentSettingsController extends Controller
{
    private $config;

    public function __construct()
    {
        $this->config = base_path('config/app.php');
    }

    public function configRewrite($key, $prevValue, $newValue)
    {
        file_put_contents(
            $this->config,
            str_replace("'$key' => '" . $prevValue . "'", "'$key' => '" . $newValue . "'", file_get_contents($this->config))
        );
    }


    // Ödeme ayarları – sadece Tosla
    public function index(Request $req)
    {
        try {
            $tosla = PaymentGateway::where('name', 'tosla')->first();
            $toslaProps = [
                'id' => $tosla->id ?? null,
                'active' => $tosla->active ?? false,
                'name' => 'tosla',
                'client_id' => $tosla->client_id ?? '',
                'api_user' => $tosla->api_user ?? '',
            ];

            return Inertia::render('Admin/PaymentSetup', ['tosla' => $toslaProps]);
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    // Tosla ödeme ayarları
    public function tosla_update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tosla_client_id' => 'required|string',
            'tosla_api_user' => 'required|string',
            'tosla_api_pass' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->except('tosla_api_pass'));
        }

        $allow_tosla = $request->allow_tosla ? true : false;
        $existing = PaymentGateway::where('name', 'tosla')->first();
        $apiPass = $request->filled('tosla_api_pass')
            ? $request->input('tosla_api_pass')
            : ($existing->api_pass ?? null);

        if (!$existing && empty($apiPass)) {
            return back()
                ->withErrors(['tosla_api_pass' => 'Yeni Tosla kaydı için ApiPass gerekli.'])
                ->withInput($request->except('tosla_api_pass'));
        }

        try {
            PaymentGateway::updateOrCreate(
                ['name' => 'tosla'],
                [
                    'active' => $allow_tosla,
                    'client_id' => $request->tosla_client_id,
                    'api_user' => $request->tosla_api_user,
                    'api_pass' => $apiPass,
                    'key' => $request->tosla_client_id,
                    'secret' => null,
                ]
            );
            return back()->with('success', 'Tosla ayarları başarıyla güncellendi.');
        } catch (\Throwable $th) {
            return back()
                ->withInput($request->except('tosla_api_pass'))
                ->with('error', $th->getMessage());
        }
    }
}
