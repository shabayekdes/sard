<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    private ?string $tenant_id = null;

    public function __construct()
    {
        $this->tenant_id = (function_exists('tenant') && tenant() !== null ? tenant('id') : null) ?? (auth()->check() ? auth()->user()->tenant_id : null);
    }

    public function boolean(string $key, bool $default = false): bool
    {
        $settings = $this->all();
        return (bool) data_get($settings, $key, $default);
    }

    public function string(string $key, bool $default = false): string
    {
        $settings = $this->all();
        return (string) data_get($settings, $key, $default);
    }

    public function sanitize()
    {
        $user = auth()->user();
        $settings = $this->all();
        if (!$user || $user->type === 'superadmin') {
            return $settings;
        }

        $defaultHost = config('mail.mailers.smtp.host');
        $defaultUsername = config('mail.mailers.smtp.username');
        $defaultPassword = config('mail.mailers.smtp.password');

        $host = $settings['EMAIL_HOST'] ?? null;
        $username = $settings['EMAIL_USERNAME'] ?? null;
        $password = $settings['EMAIL_PASSWORD'] ?? null;

        $usesDefaultCredentials = $host === $defaultHost
            && $username === $defaultUsername
            && $password === $defaultPassword;

        if ($usesDefaultCredentials) {
            $settings['EMAIL_HOST'] = '';
            $settings['EMAIL_USERNAME'] = '';
            $settings['EMAIL_PASSWORD'] = '';
        }

        $paymentKeyMap = [
            'bank_detail' => ['bank_transfer', 'detail'],
            'stripe_key' => ['stripe', 'key'],
            'stripe_secret' => ['stripe', 'secret'],
            'paypal_client_id' => ['paypal', 'client_id'],
            'paypal_secret_key' => ['paypal', 'secret_key'],
            'razorpay_key' => ['razorpay', 'key'],
            'razorpay_secret' => ['razorpay', 'secret'],
            'mercadopago_access_token' => ['mercadopago', 'access_token'],
            'paystack_public_key' => ['paystack', 'public_key'],
            'paystack_secret_key' => ['paystack', 'secret_key'],
            'flutterwave_public_key' => ['flutterwave', 'public_key'],
            'flutterwave_secret_key' => ['flutterwave', 'secret_key'],
            'paytabs_profile_id' => ['paytabs', 'profile_id'],
            'paytabs_server_key' => ['paytabs', 'server_key'],
            'paytabs_region' => ['paytabs', 'region'],
            'skrill_merchant_id' => ['skrill', 'merchant_id'],
            'skrill_secret_word' => ['skrill', 'secret_word'],
            'coingate_api_token' => ['coingate', 'api_token'],
            'payfast_merchant_id' => ['payfast', 'merchant_id'],
            'payfast_merchant_key' => ['payfast', 'merchant_key'],
            'payfast_passphrase' => ['payfast', 'passphrase'],
            'tap_secret_key' => ['tap', 'secret_key'],
            'xendit_api_key' => ['xendit', 'api_key'],
            'paytr_merchant_id' => ['paytr', 'merchant_id'],
            'paytr_merchant_key' => ['paytr', 'merchant_key'],
            'paytr_merchant_salt' => ['paytr', 'merchant_salt'],
            'mollie_api_key' => ['mollie', 'api_key'],
            'toyyibpay_category_code' => ['toyyibpay', 'category_code'],
            'toyyibpay_secret_key' => ['toyyibpay', 'secret_key'],
            'paymentwall_public_key' => ['paymentwall', 'public_key'],
            'paymentwall_private_key' => ['paymentwall', 'private_key'],
            'sspay_secret_key' => ['sspay', 'secret_key'],
            'sspay_category_code' => ['sspay', 'category_code'],
            'benefit_secret_key' => ['benefit', 'secret_key'],
            'benefit_public_key' => ['benefit', 'public_key'],
            'iyzipay_secret_key' => ['iyzipay', 'secret_key'],
            'iyzipay_public_key' => ['iyzipay', 'public_key'],
            'aamarpay_store_id' => ['aamarpay', 'store_id'],
            'aamarpay_signature' => ['aamarpay', 'signature'],
            'midtrans_secret_key' => ['midtrans', 'secret_key'],
            'yookassa_shop_id' => ['yookassa', 'shop_id'],
            'yookassa_secret_key' => ['yookassa', 'secret_key'],
            'nepalste_secret_key' => ['nepalste', 'secret_key'],
            'nepalste_public_key' => ['nepalste', 'public_key'],
            'paiement_merchant_id' => ['paiement', 'merchant_id'],
            'cinetpay_site_id' => ['cinetpay', 'site_id'],
            'cinetpay_api_key' => ['cinetpay', 'api_key'],
            'cinetpay_secret_key' => ['cinetpay', 'secret_key'],
            'payhere_merchant_id' => ['payhere', 'merchant_id'],
            'payhere_merchant_secret' => ['payhere', 'merchant_secret'],
            'payhere_app_id' => ['payhere', 'app_id'],
            'payhere_app_secret' => ['payhere', 'app_secret'],
            'fedapay_secret_key' => ['fedapay', 'secret_key'],
            'fedapay_public_key' => ['fedapay', 'public_key'],
            'authorizenet_merchant_id' => ['authorizenet', 'merchant_id'],
            'authorizenet_transaction_key' => ['authorizenet', 'transaction_key'],
            'khalti_secret_key' => ['khalti', 'secret_key'],
            'khalti_public_key' => ['khalti', 'public_key'],
            'easebuzz_merchant_key' => ['easebuzz', 'merchant_key'],
            'easebuzz_salt_key' => ['easebuzz', 'salt_key'],
            'ozow_site_key' => ['ozow', 'site_key'],
            'ozow_private_key' => ['ozow', 'private_key'],
            'ozow_api_key' => ['ozow', 'api_key'],
            'cashfree_secret_key' => ['cashfree', 'secret_key'],
            'cashfree_public_key' => ['cashfree', 'public_key']
        ];

        $hasPaymentKeys = (bool) array_intersect(array_keys($paymentKeyMap), array_keys($settings));
        if (!$hasPaymentKeys) {
            return $settings;
        }

        $paymentConfig = config('payment_methods', []);
        foreach ($paymentKeyMap as $settingKey => [$method, $configKey]) {
            if (!array_key_exists($settingKey, $settings)) {
                continue;
            }

            $defaultValue = $paymentConfig[$method][$configKey] ?? null;
            if ($defaultValue !== null && $defaultValue !== '' && $settings[$settingKey] === $defaultValue) {
                $settings[$settingKey] = '';
            }
        }

        return $settings;
    }

    /**
     * Update or create a setting by key.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int|string|null  $tenant_id  Optional tenant ID (uses service tenant_id when null)
     */
    public function update(string $key, $value, $tenant_id = null): void
    {
        $tenant_id = $tenant_id ?? $this->tenant_id;

        $setting = Setting::query()
            ->where('key', $key)
            ->when($tenant_id, function (Builder $query) use ($tenant_id) {
                $query->where('tenant_id', $tenant_id);
            }, function (Builder $query) {
                $query->whereNull('tenant_id');
            })
            ->first();

        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            Setting::create([
                'key' => $key,
                'value' => $value,
                'tenant_id' => $tenant_id,
            ]);
        }

        $cacheKey = 'settings' . ($tenant_id ? '.' . $tenant_id : '');
        Cache::forget($cacheKey);
    }
    /**
     * @return array
     */
    public function all(): array
    {
        $cacheKey = 'settings' .  ($this->tenant_id ? '.' . $this->tenant_id : '');
        return Cache::remember($cacheKey, 60 * 60, fn() => $this->settings());
    }
    /**
     * @return array
     */
    private function settings(): array
    {
        $saasSettings = Setting::query()
            ->select(['key', 'value'])
            ->whereNull('tenant_id')
            ->pluck('value', 'key')
            ->toArray();

        $tenantSettings = [];
        if ($this->tenant_id) {
            $tenantSettings = Setting::query()
                ->select(['key', 'value'])
                ->where('tenant_id', $this->tenant_id)
                ->pluck('value', 'key')
                ->toArray();
        }

        return array_merge($saasSettings, $tenantSettings);
    }
}