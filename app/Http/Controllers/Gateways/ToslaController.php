<?php

namespace App\Http\Controllers\Gateways;

use App\Http\Controllers\Controller;
use App\Models\PaymentAttempt;
use App\Models\PaymentGateway;
use App\Models\PricingPlan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Tosla İşim Ortak Ödeme Sayfası entegrasyonu.
 * Dokümantasyon: https://prepentegrasyon.tosla.com/swagger
 */
class ToslaController extends Controller
{
    public function payment(Request $request)
    {
        if (app()->environment('production')) {
            abort(404);
        }

        $request->validate([
            'plan_id' => 'required|integer|exists:pricing_plans,id',
            'billing_type' => 'required|in:monthly,yearly',
        ]);

        $credentials = $this->toslaCredentials();
        if (!$credentials) {
            return redirect()->route('plan')->with('error', 'Tosla ödeme ayarları yapılandırılmamış. Lütfen yönetici ile iletişime geçin.');
        }

        $plan = PricingPlan::where('id', $request->plan_id)
            ->where('status', 'active')
            ->firstOrFail();

        if (!$this->isTryCurrency($plan->currency)) {
            return redirect()->route('plan')->with('error', 'Bu plan için ödeme para birimi desteklenmiyor.');
        }

        $price = $request->billing_type === 'monthly' ? $plan->monthly_price : $plan->yearly_price;

        try {
            $amount = $this->toMinorUnits($price);
        } catch (Throwable $th) {
            return redirect()->route('plan')->with('error', 'Geçersiz plan tutarı.');
        }

        if ($amount <= 0) {
            return redirect()->route('plan')->with('error', 'Ücretsiz plan için ödeme başlatılamaz.');
        }

        $user = $request->user();
        $orderId = $this->uniqueOrderId((int) $user->id);
        if (!$orderId) {
            return redirect()->route('plan')->with('error', 'Ödeme başlatılamadı. Lütfen daha sonra tekrar deneyin.');
        }
        $auth = $this->makeRequestAuth($credentials);

        $payload = [
            'clientId' => $credentials['client_id'],
            'apiUser' => $credentials['api_user'],
            'rnd' => $auth['rnd'],
            'timeSpan' => $auth['timeSpan'],
            'hash' => $auth['hash'],
            'callbackUrl' => route('tosla.callback'),
            'orderId' => $orderId,
            'amount' => $amount,
            'currency' => 949,
            'installmentCount' => 0,
            'description' => $plan->name . ' ' . $request->billing_type,
        ];

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->asJson()
                ->post($this->paymentBaseUrl() . 'threeDPayment', $payload);
        } catch (Throwable $th) {
            return redirect()->route('plan')->with('error', 'Ödeme servisine ulaşılamadı. Lütfen daha sonra tekrar deneyin.');
        }

        if (!$response->successful()) {
            return redirect()->route('plan')->with('error', 'Ödeme başlatılamadı. Lütfen daha sonra tekrar deneyin.');
        }

        $data = $response->json();
        if (!is_array($data)) {
            return redirect()->route('plan')->with('error', 'Ödeme başlatılamadı. Lütfen daha sonra tekrar deneyin.');
        }

        $code = $this->payloadValue($data, 'Code');
        $threeDSessionId = (string) $this->payloadValue($data, 'ThreeDSessionId');
        $transactionId = (string) $this->payloadValue($data, 'TransactionId');

        if ((int) $code !== 0 || $threeDSessionId === '' || $transactionId === '') {
            return redirect()->route('plan')->with('error', 'Ödeme oturumu oluşturulamadı.');
        }

        try {
            PaymentAttempt::create([
                'order_id' => $orderId,
                'user_id' => $user->id,
                'pricing_plan_id' => $plan->id,
                'billing_type' => $request->billing_type,
                'expected_amount' => $amount,
                'currency' => 949,
                'three_d_session_id' => $threeDSessionId,
                'provider_transaction_id' => $transactionId,
                'status' => 'pending',
            ]);
        } catch (Throwable $th) {
            return redirect()->route('plan')->with('error', 'Ödeme başlatılamadı. Lütfen daha sonra tekrar deneyin.');
        }

        session()->put('tosla_order_id', $orderId);
        session()->put('tosla_plan_id', $plan->id);
        session()->put('tosla_billing_type', $request->billing_type);
        session()->put('tosla_expected_amount', $amount);
        session()->put('tosla_currency', 949);
        session()->put('tosla_three_d_session_id', $threeDSessionId);
        session()->put('tosla_transaction_id', $transactionId);

        return redirect()->away($this->paymentBaseUrl() . 'threeDSecure/' . $threeDSessionId);
    }

    public function form(Request $request)
    {
        if (app()->environment('production')) {
            abort(404);
        }

        return redirect()->route('plan')->with('error', 'Geçersiz ödeme adımı.');
    }

    public function callback(Request $request)
    {
        if (app()->environment('production')) {
            abort(404);
        }

        $credentials = $this->toslaCredentials();
        if (!$credentials) {
            return redirect()->route('plan')->with('error', 'Ödeme doğrulanamadı.');
        }

        $callbackClientId = $this->callbackValue($request, 'ClientId');
        $callbackApiUser = $this->callbackValue($request, 'ApiUser');

        if ($callbackClientId !== '' && !hash_equals($credentials['client_id'], $callbackClientId)) {
            return redirect()->route('plan')->with('error', 'Ödeme doğrulanamadı.');
        }

        if ($callbackApiUser !== '' && !hash_equals($credentials['api_user'], $callbackApiUser)) {
            return redirect()->route('plan')->with('error', 'Ödeme doğrulanamadı.');
        }

        if (!$this->callbackHashIsValid($request, $credentials, $callbackClientId, $callbackApiUser)) {
            return redirect()->route('plan')->with('error', 'Ödeme doğrulanamadı.');
        }

        if ($this->callbackValue($request, 'MdStatus') !== '1') {
            return redirect()->route('plan')->with('error', 'Ödeme doğrulanamadı.');
        }

        if ($this->callbackValue($request, 'BankResponseCode') !== '00') {
            return redirect()->route('plan')->with('error', 'Ödeme doğrulanamadı.');
        }

        $orderId = $this->callbackValue($request, 'OrderId');
        $callbackThreeDSessionId = $this->callbackValue($request, 'ThreeDSessionId');
        $callbackTransactionId = $this->callbackValue($request, 'TransactionId');

        if ($callbackThreeDSessionId === '') {
            return redirect()->route('plan')->with('error', 'Ödeme doğrulanamadı.');
        }

        $attempt = PaymentAttempt::where('order_id', $orderId)->first();
        if (!$attempt || $attempt->status === 'paid') {
            if ($attempt && $attempt->status === 'paid') {
                return redirect()->route('plan')->with('success', 'Ödeme başarıyla tamamlandı.');
            }

            return redirect()->route('plan')->with('error', 'Ödeme kaydı bulunamadı.');
        }

        if (!hash_equals((string) $attempt->three_d_session_id, $callbackThreeDSessionId)) {
            return redirect()->route('plan')->with('error', 'Ödeme doğrulanamadı.');
        }

        if ($callbackTransactionId !== '' && !hash_equals((string) $attempt->provider_transaction_id, $callbackTransactionId)) {
            return redirect()->route('plan')->with('error', 'Ödeme doğrulanamadı.');
        }

        $inquiry = $this->inquirePayment($credentials, $attempt);
        if (!$inquiry) {
            return redirect()->route('plan')->with('error', 'Ödeme doğrulanamadı.');
        }

        try {
            $this->activatePaidPlan($attempt, $inquiry['transaction_id']);
        } catch (Throwable $th) {
            return redirect()->route('plan')->with('error', 'Ödeme işlenemedi.');
        }

        $this->forgetToslaSession();

        return redirect()->route('plan')->with('success', 'Ödeme başarıyla tamamlandı.');
    }

    public function success(Request $request)
    {
        if (app()->environment('production')) {
            abort(404);
        }

        return redirect()->route('plan')->with('info', 'Ödeme sonucu kontrol ediliyor.');
    }

    public function cancel(Request $request)
    {
        $this->forgetToslaSession();

        return redirect()->route('plan')->with('info', 'Ödeme iptal edildi.');
    }

    private function paymentBaseUrl(): string
    {
        if (app()->environment('production')) {
            return 'https://entegrasyon.tosla.com/api/Payment/';
        }

        return 'https://prepentegrasyon.tosla.com/api/Payment/';
    }

    private function toslaCredentials(): ?array
    {
        $tosla = PaymentGateway::where('name', 'tosla')->first();
        if (!$tosla || !$tosla->active) {
            return null;
        }

        if (empty($tosla->client_id) || empty($tosla->api_user) || empty($tosla->api_pass)) {
            return null;
        }

        return [
            'client_id' => (string) $tosla->client_id,
            'api_user' => (string) $tosla->api_user,
            'api_pass' => (string) $tosla->api_pass,
        ];
    }

    private function makeRequestAuth(array $credentials): array
    {
        $rnd = substr(bin2hex(random_bytes(12)), 0, 24);
        $timeSpan = now('Europe/Istanbul')->format('YmdHis');
        $hashString = $credentials['api_pass']
            . $credentials['client_id']
            . $credentials['api_user']
            . $rnd
            . $timeSpan;

        return [
            'rnd' => $rnd,
            'timeSpan' => $timeSpan,
            'hash' => base64_encode(hash('sha512', $hashString, true)),
        ];
    }

    private function uniqueOrderId(int $userId): ?string
    {
        for ($i = 0; $i < 3; $i++) {
            $orderId = $this->makeOrderId($userId);
            if (!PaymentAttempt::where('order_id', $orderId)->exists()) {
                return $orderId;
            }
        }

        return null;
    }

    private function makeOrderId(int $userId): string
    {
        $user = strtoupper(str_pad(dechex($userId % 0xFFFFF), 5, '0', STR_PAD_LEFT));
        $time = strtoupper(substr(dechex(time()), -7));
        $rand = strtoupper(substr(bin2hex(random_bytes(4)), 0, 7));

        return substr('T' . $user . $time . $rand, 0, 20);
    }

    private function toMinorUnits($price): int
    {
        $normalized = str_replace(',', '.', trim((string) $price));
        if (!preg_match('/^\d+(\.\d{1,2})?$/', $normalized)) {
            throw new \InvalidArgumentException('Invalid amount');
        }

        if (!str_contains($normalized, '.')) {
            return (int) $normalized * 100;
        }

        [$lira, $kurus] = explode('.', $normalized, 2);
        $kurus = str_pad($kurus, 2, '0');

        return ((int) $lira * 100) + (int) $kurus;
    }

    private function isTryCurrency($currency): bool
    {
        $value = strtoupper(trim((string) $currency));

        return in_array($value, ['TRY', 'TL', '949'], true);
    }

    private function callbackHashIsValid(Request $request, array $credentials, string $callbackClientId, string $callbackApiUser): bool
    {
        $providedHash = $this->callbackValue($request, 'Hash');
        if ($providedHash === '') {
            return false;
        }

        $clientId = $callbackClientId !== '' ? $callbackClientId : $credentials['client_id'];
        $apiUser = $callbackApiUser !== '' ? $callbackApiUser : $credentials['api_user'];

        // HashParameters alanı dokümanda dinamik; sıra/format resmi olarak
        // netleşmeden tahmin edilmiyor. Sabit doküman örneği kullanılıyor:
        // ApiPass + ClientId + ApiUser + OrderId + MdStatus + BankResponseCode
        // + BankResponseMessage + RequestStatus
        $hashString = $credentials['api_pass']
            . $clientId
            . $apiUser
            . $this->callbackValue($request, 'OrderId')
            . $this->callbackValue($request, 'MdStatus')
            . $this->callbackValue($request, 'BankResponseCode')
            . $this->callbackValue($request, 'BankResponseMessage')
            . $this->callbackValue($request, 'RequestStatus');

        $expectedHash = base64_encode(hash('sha512', $hashString, true));

        return hash_equals($expectedHash, $providedHash);
    }

    private function inquirePayment(array $credentials, PaymentAttempt $attempt): ?array
    {
        $auth = $this->makeRequestAuth($credentials);

        $payload = [
            'clientId' => $credentials['client_id'],
            'apiUser' => $credentials['api_user'],
            'rnd' => $auth['rnd'],
            'timeSpan' => $auth['timeSpan'],
            'hash' => $auth['hash'],
            'orderId' => $attempt->order_id,
        ];

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->asJson()
                ->post($this->paymentBaseUrl() . 'inquiry', $payload);
        } catch (Throwable $th) {
            return null;
        }

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();
        if (!is_array($data)) {
            return null;
        }

        $code = $this->payloadValue($data, 'Code');
        $requestStatus = $this->payloadValue($data, 'RequestStatus');
        $bankResponseCode = (string) $this->payloadValue($data, 'BankResponseCode');
        $orderId = (string) $this->payloadValue($data, 'OrderId');
        $amount = $this->payloadValue($data, 'Amount');
        $currency = $this->payloadValue($data, 'Currency');
        $transactionId = (string) $this->payloadValue($data, 'TransactionId');
        $clientId = (string) $this->payloadValue($data, 'ClientId');

        if ((int) $code !== 0) {
            return null;
        }

        if ((int) $requestStatus !== 1) {
            return null;
        }

        if ($bankResponseCode !== '00') {
            return null;
        }

        if ($orderId !== (string) $attempt->order_id) {
            return null;
        }

        if ((int) $amount !== (int) $attempt->expected_amount) {
            return null;
        }

        if ((int) $currency !== (int) $attempt->currency) {
            return null;
        }

        if ($transactionId === '') {
            return null;
        }

        if ($clientId !== $credentials['client_id']) {
            return null;
        }

        if (!hash_equals((string) $attempt->provider_transaction_id, $transactionId)) {
            return null;
        }

        return [
            'transaction_id' => $transactionId,
            'payload' => $data,
        ];
    }

    private function activatePaidPlan(PaymentAttempt $attempt, string $transactionId): void
    {
        DB::transaction(function () use ($attempt, $transactionId) {
            $locked = PaymentAttempt::where('id', $attempt->id)->lockForUpdate()->first();
            if (!$locked || $locked->status === 'paid') {
                return;
            }

            $existing = Subscription::where('transaction_id', $transactionId)->first();
            if ($existing) {
                $locked->status = 'paid';
                $locked->provider_transaction_id = $transactionId;
                $locked->save();
                return;
            }

            $user = User::where('id', $locked->user_id)->lockForUpdate()->firstOrFail();
            $plan = PricingPlan::where('id', $locked->pricing_plan_id)->firstOrFail();
            $nextPayment = $locked->billing_type === 'monthly'
                ? date('Y-m-d', strtotime('+1 month'))
                : date('Y-m-d', strtotime('+1 year'));

            $subscription = Subscription::create([
                'user_id' => $user->id,
                'method' => 'tosla',
                'billing' => $locked->billing_type,
                'transaction_id' => $transactionId,
                'total_price' => number_format($locked->expected_amount / 100, 2, '.', ''),
                'currency' => $plan->currency,
            ]);

            $user->pricing_plan_id = $plan->id;
            $user->next_payment = $nextPayment;
            $user->subscription_id = $subscription->id;
            $user->recurring = $locked->billing_type;
            $user->save();

            $locked->status = 'paid';
            $locked->provider_transaction_id = $transactionId;
            $locked->save();
        });
    }

    private function callbackValue(Request $request, string $name): string
    {
        $value = $request->input($name);
        if ($value === null) {
            $value = $request->input(lcfirst($name));
        }

        return $value === null ? '' : (string) $value;
    }

    private function payloadValue(array $payload, string $name)
    {
        if (array_key_exists($name, $payload)) {
            return $payload[$name];
        }

        $camel = lcfirst($name);
        if (array_key_exists($camel, $payload)) {
            return $payload[$camel];
        }

        foreach ($payload as $key => $value) {
            if (strcasecmp((string) $key, $name) === 0) {
                return $value;
            }
        }

        return null;
    }

    private function forgetToslaSession(): void
    {
        session()->forget([
            'tosla_order_id',
            'tosla_plan_id',
            'tosla_billing_type',
            'tosla_expected_amount',
            'tosla_currency',
            'tosla_three_d_session_id',
            'tosla_transaction_id',
            'plan_id',
            'billing_type',
            'tosla_amount',
        ]);
    }
}
