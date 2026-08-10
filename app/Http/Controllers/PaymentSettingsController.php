<?php

namespace App\Http\Controllers;

use App\Models\PaymentGateway;
use Illuminate\Http\Request;
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
            if (!$tosla) {
                $tosla = (object) ['id' => null, 'active' => false, 'name' => 'tosla', 'key' => '', 'secret' => ''];
            }
            return Inertia::render('Admin/PaymentSetup', compact('tosla'));
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    // Tosla ödeme ayarları
    public function tosla_update(Request $request)
    {
        $request->validate([
            'tosla_merchant_id' => 'required|string',
            'tosla_secret_key' => 'required|string',
        ]);
        $allow_tosla = $request->allow_tosla ? true : false;

        try {
            PaymentGateway::updateOrCreate(
                ['name' => 'tosla'],
                [
                    'active' => $allow_tosla,
                    'key' => $request->tosla_merchant_id,
                    'secret' => $request->tosla_secret_key,
                ]
            );
            return back()->with('success', 'Tosla ayarları başarıyla güncellendi.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }


    }
