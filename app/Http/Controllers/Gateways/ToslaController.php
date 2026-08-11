<?php

namespace App\Http\Controllers\Gateways;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\PricingPlan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Tosla ödeme entegrasyonu.
 * Merchant ID ve Secret Key ile Tosla panelinden alınır.
 * Gerçek API çağrıları için Tosla dokümantasyonu: https://prepentegrasyon.tosla.com/swagger
 */
class ToslaController extends Controller
{
    public function payment(Request $request)
    {
        if (app()->environment('production')) {
            abort(404);
        }

        $request->validate([
            'plan_id' => 'required|exists:pricing_plans,id',
            'billing_type' => 'required|in:monthly,yearly',
        ]);

        $tosla = PaymentGateway::where('name', 'tosla')->first();
        if (!$tosla || !$tosla->active || empty($tosla->key) || empty($tosla->secret)) {
            return redirect()->route('plan')->with('error', 'Tosla ödeme ayarları yapılandırılmamış. Lütfen yönetici ile iletişime geçin.');
        }

        $plan = PricingPlan::findOrFail($request->plan_id);
        $price = $request->billing_type === 'monthly' ? $plan->monthly_price : $plan->yearly_price;

        session()->put('plan_id', $plan->id);
        session()->put('billing_type', $request->billing_type);
        session()->put('tosla_amount', $price);
        session()->put('tosla_currency', $plan->currency);

        // Tosla API ile ödeme oturumu oluşturulacak.
        // Örnek: Tosla'ya istek atıp dönen ödeme sayfası URL'sine yönlendirme.
        $callbackUrl = route('tosla.callback');
        $successUrl = route('tosla.success');
        $cancelUrl = route('tosla.cancel');

        // TODO: Tosla API'ye istek (Merchant ID, Secret Key, tutar, para birimi, callback URL).
        // Şimdilik test için kullanıcıyı form sayfasına yönlendiriyoruz; gerçek entegrasyonda
        // Tosla'nın döndüğü payment URL'ye redirect edilecek.
        return redirect()->route('tosla.form');
    }

    /**
     * Tosla ödeme formu (test veya gerçek yönlendirme).
     * Gerçek entegrasyonda bu route kaldırılıp doğrudan Tosla'ya yönlendirilebilir.
     */
    public function form(Request $request)
    {
        if (app()->environment('production')) {
            abort(404);
        }

        if (!session()->has('plan_id')) {
            return redirect()->route('plan')->with('error', 'Oturum süresi doldu. Lütfen plan seçimini tekrarlayın.');
        }
        return view('pages.gateways.tosla-form');
    }

    /**
     * Tosla'dan başarılı ödeme sonrası callback (Tosla sunucusu bu URL'yi çağırır).
     */
    public function callback(Request $request)
    {
        if (app()->environment('production')) {
            abort(404);
        }

        // TODO: Tosla'dan gelen parametreleri doğrula (imza, transaction_id vb.).
        // Şimdilik GET ile test için success'e yönlendiriyoruz.
        return redirect()->route('tosla.success');
    }

    public function success(Request $request)
    {
        if (app()->environment('production')) {
            abort(404);
        }

        try {
            $plan_id = session()->get('plan_id');
            $billing_type = session()->get('billing_type');
            if (!$plan_id) {
                return redirect()->route('plan')->with('error', 'Oturum bulunamadı.');
            }

            $user = User::where('id', auth()->id())->first();
            $plan = PricingPlan::findOrFail($plan_id);
            $nextPayment = $billing_type === 'monthly'
                ? date('Y-m-d', strtotime('+1 month'))
                : date('Y-m-d', strtotime('+1 year'));

            $subscription = Subscription::create([
                'user_id' => $user->id,
                'method' => 'tosla',
                'billing' => $billing_type,
                'transaction_id' => 'TOSLA-' . uniqid(),
                'total_price' => session()->get('tosla_amount', $billing_type === 'monthly' ? $plan->monthly_price : $plan->yearly_price),
                'currency' => session()->get('tosla_currency', $plan->currency),
            ]);

            $user->pricing_plan_id = $plan->id;
            $user->next_payment = $nextPayment;
            $user->subscription_id = $subscription->id;
            $user->recurring = $billing_type;
            $user->save();

            session()->forget(['plan_id', 'billing_type', 'tosla_amount', 'tosla_currency']);

            return redirect()->route('plan')->with('success', 'Ödeme başarıyla tamamlandı.');
        } catch (\Throwable $th) {
            return redirect()->route('plan')->with('error', $th->getMessage());
        }
    }

    public function cancel(Request $request)
    {
        session()->forget(['plan_id', 'billing_type', 'tosla_amount', 'tosla_currency']);
        return redirect()->route('plan')->with('info', 'Ödeme iptal edildi.');
    }
}
