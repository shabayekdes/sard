<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PaymentSettingsService
{
    private const TABLE = 'payment_settings';

    /**
     * Get raw payment settings by tenant_id.
     * When tenant_id is null or empty: returns settings where tenant_id IS NULL (global/superadmin).
     * When tenant_id is a UUID: returns settings for that tenant.
     *
     * @param string|null $tenantId UUID or null for global
     * @return array<string, mixed>
     */
    public function getPaymentSettings(?string $tenantId = null): array
    {
        $query = DB::table(self::TABLE)->select('key', 'value');

        if ($tenantId === null || $tenantId === '') {
            $query->whereNull('tenant_id');
        } else {
            $query->where('tenant_id', $tenantId);
        }

        $rows = $query->get();

        return $rows->pluck('value', 'key')->toArray();
    }

    /**
     * Get safe payment settings for frontend (plans page, subscription modal, etc.).
     * Filters out sensitive credentials and only returns enabled flags and safe config.
     *
     * @param string|null $tenantId UUID or null for global (tenant_id IS NULL)
     * @return array<string, mixed>
     */
    public function getSafePaymentSettingsArray(?string $tenantId = null): array
    {
        $settings = $this->getPaymentSettings($tenantId);
        $safeSettings = $this->filterSensitiveData($settings);
        $globalSettings = settings();
        $safeSettings['defaultCurrency'] = $globalSettings['DEFAULT_CURRENCY'] ?? 'SAR';

        return $safeSettings;
    }

    /**
     * Filter out sensitive payment gateway credentials.
     * Only include enabled status, modes, and keys safe for frontend.
     *
     * @param array<string, mixed> $settings
     * @return array<string, mixed>
     */
    public function filterSensitiveData(array $settings): array
    {
        $safeSettings = [];

        $enabledKeys = [
            'manually_enabled', 'bank_transfer_enabled', 'stripe_enabled', 'paypal_enabled',
            'razorpay_enabled', 'mercadopago_enabled', 'paystack_enabled', 'flutterwave_enabled',
            'paytabs_enabled', 'skrill_enabled', 'coingate_enabled', 'payfast_enabled',
            'tap_enabled', 'xendit_enabled', 'paytr_enabled', 'mollie_enabled',
            'toyyibpay_enabled', 'paymentwall_enabled', 'sspay_enabled', 'benefit_enabled',
            'iyzipay_enabled', 'aamarpay_enabled', 'midtrans_enabled', 'yookassa_enabled',
            'nepalste_enabled', 'paiement_enabled', 'cinetpay_enabled', 'payhere_enabled',
            'fedapay_enabled', 'authorizenet_enabled', 'khalti_enabled', 'easebuzz_enabled',
            'ozow_enabled', 'cashfree_enabled',
        ];

        $modeKeys = [
            'paypal_mode', 'mercadopago_mode', 'paytabs_mode', 'coingate_mode', 'payfast_mode',
            'benefit_mode', 'iyzipay_mode', 'midtrans_mode', 'nepalste_mode', 'payhere_mode',
            'fedapay_mode', 'authorizenet_mode', 'ozow_mode', 'cashfree_mode', 'aamarpay_mode',
        ];

        $frontendKeys = [
            'stripe_key', 'razorpay_key', 'paystack_public_key', 'flutterwave_public_key',
            'khalti_public_key', 'cashfree_public_key', 'iyzipay_public_key', 'benefit_public_key',
            'fedapay_public_key', 'nepalste_public_key', 'paymentwall_public_key',
            'paypal_client_id', 'toyyibpay_category_code', 'aamarpay_store_id',
            'authorizenet_merchant_id', 'cinetpay_site_id', 'easebuzz_merchant_key',
            'ozow_site_key', 'paiement_merchant_id', 'payfastMerchantId',
            'payhere_merchant_id', 'paytr_merchant_id', 'skrill_merchant_id',
            'yookassa_shop_id',
            'bank_detail',
        ];

        foreach (array_merge($enabledKeys, $modeKeys, $frontendKeys) as $key) {
            if (isset($settings[$key])) {
                $safeSettings[$key] = $settings[$key];
            }
        }

        return $safeSettings;
    }
}
