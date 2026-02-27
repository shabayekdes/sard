<?php

use App\Enum\EmailTemplateName;
use App\Models\Coupon;
use App\Models\PaymentSetting;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

if (!function_exists('getCacheSize')) {
    /**
     * Get the total cache size in MB
     *
     * @return string
     */
    function getCacheSize()
    {
        $file_size = 0;
        $framework_path = storage_path('framework');

        if (is_dir($framework_path)) {
            foreach (\File::allFiles($framework_path) as $file) {
                $file_size += $file->getSize();
            }
        }

        return number_format($file_size / 1000000, 2);
    }
}

if (! function_exists('settings')) {
    function settings($tenant = null)
    {
        // Explicit arg > tenant() from domain (e.g. tenant routes) > auth user's tenant_id (e.g. universal routes)
        $tenantId = $tenant ?? (function_exists('tenant') && tenant() !== null ? tenant('id') : null) ?? (auth()->check() ? auth()->user()->tenant_id : null);
        $userSettings = Setting::where('tenant_id', $tenantId)->pluck('value', 'key')->toArray();

        // If user is not superadmin, merge with superadmin settings for specific keys
        if (auth()->check() && auth()->user()->type !== 'superadmin') {
            $superAdmin = User::where('type', 'superadmin')->first();
            if ($superAdmin) {
                $superAdminKeys = ['decimalFormat', 'defaultCurrency', 'thousandsSeparator', 'floatNumber', 'currencySymbolSpace', 'currencySymbolPosition', 'dateFormat', 'timeFormat', 'calendarStartDay', 'defaultTimezone', 'defaultCountry', 'defaultTaxRate'];
                $superAdminSettings = Setting::whereNull('tenant_id')
                    ->whereIn('key', $superAdminKeys)
                    ->pluck('value', 'key')
                    ->toArray();
                $userSettings = array_merge($superAdminSettings, $userSettings);
            }
        }

        // Add demo mode flag from config
        $userSettings['is_demo'] = config('app.is_demo', false);

        return $userSettings;
    }
}

if (! function_exists('sanitizeSettingsForUi')) {
    /**
     * Hide SaaS-owned credentials from UI payloads.
     *
     * @param array $settings
     * @param int|null $userId
     * @return array
     */
    function sanitizeSettingsForUi(array $settings, $userId = null)
    {
        $user = auth()->user();
        if (!$user || $user->type === 'superadmin') {
            return $settings;
        }

        $defaultHost = config('mail.mailers.smtp.host');
        $defaultUsername = config('mail.mailers.smtp.username');
        $defaultPassword = config('mail.mailers.smtp.password');

        $host = $settings['email_host'] ?? null;
        $username = $settings['email_username'] ?? null;
        $password = $settings['email_password'] ?? null;

        $usesDefaultCredentials = $host === $defaultHost
            && $username === $defaultUsername
            && $password === $defaultPassword;

        if ($usesDefaultCredentials) {
            $settings['email_host'] = '';
            $settings['email_username'] = '';
            $settings['email_password'] = '';
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
}

if (! function_exists('formatDateTime')) {
    function formatDateTime($date, $includeTime = true)
    {
        if (!$date) {
            return null;
        }

        $settings = settings();

        $dateFormat = $settings['dateFormat'] ?? 'Y-m-d';
        $timeFormat = $settings['timeFormat'] ?? 'H:i';
        $timezone = $settings['defaultTimezone'] ?? config('app.timezone', 'UTC');

        $format = $includeTime ? "$dateFormat $timeFormat" : $dateFormat;

        return Carbon::parse($date)->timezone($timezone)->format($format);
    }
}

if (! function_exists('getSetting')) {
    function getSetting($key, $default = null, $tenant_id = null)
    {
        $settings = settings($tenant_id);

        // If no value found and no default provided, try to get from defaultSettings
        if (!isset($settings[$key]) && $default === null) {
            $defaultSettings = defaultSettings();
            $default = $defaultSettings[$key] ?? null;
        }

        return $settings[$key] ?? $default;
    }
}
if (! function_exists('IsDemo')) {
    /**
     * Check if the application is in demo mode
     *
     * @return bool
     * @deprecated
     */
    function IsDemo()
    {
        if (config('app.is_demo')) {
            return true;
        } else {
            return false;
        }
    }
}

if (! function_exists('updateSetting')) {
    function updateSetting($key, $value, $tenant_id = null)
    {
        if (is_null($tenant_id)) {
            if (auth()->user()) {
                $tenant_id = auth()->user()->tenant_id;
            } else {
                $user = User::where('type', 'superadmin')->first();
                $tenant_id = $user ? $user->tenant_id : null;
            }
        }

        return Setting::updateOrCreate(
            ['tenant_id' => $tenant_id, 'key' => $key],
            ['value' => $value]
        );
    }
}

if (! function_exists('isLandingPageEnabled')) {
    function isLandingPageEnabled()
    {
        return getSetting('landingPageEnabled', true) === true || getSetting('landingPageEnabled', true) === '1';
    }
}

if (! function_exists('defaultRoleAndSetting')) {
    /**
     * Set up default roles, settings, and data for a new user
     *
     * @param \App\Models\User $user
     * @return bool
     */
    function defaultRoleAndSetting($user)
    {
        $companyRole = Role::where('name', 'company')->first();

        if ($companyRole) {
            $user->assignRole($companyRole);
        }

        // Create default settings for the user
        if ($user->type === 'superadmin') {
            createDefaultSettings($user->id);
        } elseif ($user->type === 'company' && $user->tenant_id) {
            // Dispatch all seeding jobs in parallel
            // This prevents blocking the HTTP request and improves user experience
            // SeedDefaultCompanyData will dispatch all individual seeding jobs
            \App\Jobs\SeedDefaultCompanyData::dispatch($user->tenant_id);
        }

        return true;
    }
}


if (! function_exists('getPaymentSettings')) {
    /**
     * Get payment settings for a tenant
     *
     * @param string|int|null $tenantIdOrUserId Tenant ID (UUID), or user ID for backward compat (resolved to tenant_id)
     * @return array
     */
    function getPaymentSettings($tenantIdOrUserId = null)
    {
        $tenantId = $tenantIdOrUserId;
        if (is_null($tenantId)) {
            $tenantId = createdBy();
        } elseif (is_numeric($tenantId)) {
            $tenantId = User::find($tenantId)?->tenant_id ?? $tenantId;
        }
        return PaymentSetting::getUserSettings($tenantId);
    }
}

if (! function_exists('updatePaymentSetting')) {
    /**
     * Update or create a payment setting
     *
     * @param string $key
     * @param mixed $value
     * @param string|int|null $tenantIdOrUserId Tenant ID (UUID), or user ID for backward compat (resolved to tenant_id)
     * @return \App\Models\PaymentSetting
     */
    function updatePaymentSetting($key, $value, $tenantIdOrUserId = null)
    {
        $tenantId = $tenantIdOrUserId;
        if (is_null($tenantId)) {
            $tenantId = createdBy();
        } elseif (is_numeric($tenantId)) {
            $tenantId = User::find($tenantId)?->tenant_id ?? $tenantId;
        }
        return PaymentSetting::updateOrCreateSetting($tenantId, $key, $value);
    }
}

if (! function_exists('isPaymentMethodEnabled')) {
    /**
     * Check if a payment method is enabled
     *
     * @param string $method (stripe, paypal, razorpay, mercadopago, bank_transfer)
     * @param int|null $userId
     * @return bool
     */
    function isPaymentMethodEnabled($method, $userId = null)
    {
        $userId = $userId ?: getPaymentSettingsUserId();
        $settings = getPaymentSettings($userId);
        $key = "{$method}_enabled";

        return isset($settings[$key]) && ($settings[$key] === true || $settings[$key] === '1');
    }
}

if (! function_exists('getPaymentMethodConfig')) {
    /**
     * Get configuration for a specific payment method
     *
     * @param string $method (stripe, paypal, razorpay, mercadopago)
     * @param int|null $userId
     * @return array
     */
    function getPaymentMethodConfig($method, $userId = null)
    {
        $userId = $userId ?: getPaymentSettingsUserId();
        $settings = getPaymentSettings($userId);

        switch ($method) {
            case 'stripe':
                return [
                    'enabled' => isPaymentMethodEnabled('stripe', $userId),
                    'key' => $settings['stripe_key'] ?? null,
                    'secret' => $settings['stripe_secret'] ?? null,
                ];

            case 'paypal':
                return [
                    'enabled' => isPaymentMethodEnabled('paypal', $userId),
                    'mode' => $settings['paypal_mode'] ?? 'sandbox',
                    'client_id' => $settings['paypal_client_id'] ?? null,
                    'secret' => $settings['paypal_secret_key'] ?? null,
                ];

            case 'razorpay':
                return [
                    'enabled' => isPaymentMethodEnabled('razorpay', $userId),
                    'key' => $settings['razorpay_key'] ?? null,
                    'secret' => $settings['razorpay_secret'] ?? null,
                ];

            case 'mercadopago':
                return [
                    'enabled' => isPaymentMethodEnabled('mercadopago', $userId),
                    'mode' => $settings['mercadopago_mode'] ?? 'sandbox',
                    'access_token' => $settings['mercadopago_access_token'] ?? null,
                ];

            case 'paystack':
                return [
                    'enabled' => isPaymentMethodEnabled('paystack', $userId),
                    'public_key' => $settings['paystack_public_key'] ?? null,
                    'secret_key' => $settings['paystack_secret_key'] ?? null,
                ];

            case 'flutterwave':
                return [
                    'enabled' => isPaymentMethodEnabled('flutterwave', $userId),
                    'public_key' => $settings['flutterwave_public_key'] ?? null,
                    'secret_key' => $settings['flutterwave_secret_key'] ?? null,
                ];

            case 'bank_transfer':
                return [
                    'enabled' => isPaymentMethodEnabled('bank_transfer', $userId),
                    'details' => $settings['bank_detail'] ?? null,
                ];

            case 'paytabs':
                return [
                    'enabled' => isPaymentMethodEnabled('paytabs', $userId),
                    'mode' => $settings['paytabs_mode'] ?? 'sandbox',
                    'profile_id' => $settings['paytabs_profile_id'] ?? null,
                    'server_key' => $settings['paytabs_server_key'] ?? null,
                    'region' => $settings['paytabs_region'] ?? 'ARE',
                ];

            case 'skrill':
                return [
                    'enabled' => isPaymentMethodEnabled('skrill', $userId),
                    'merchant_id' => $settings['skrill_merchant_id'] ?? null,
                    'secret_word' => $settings['skrill_secret_word'] ?? null,
                ];

            case 'coingate':
                return [
                    'enabled' => isPaymentMethodEnabled('coingate', $userId),
                    'mode' => $settings['coingate_mode'] ?? 'sandbox',
                    'api_token' => $settings['coingate_api_token'] ?? null,
                ];

            case 'payfast':
                return [
                    'enabled' => isPaymentMethodEnabled('payfast', $userId),
                    'mode' => $settings['payfast_mode'] ?? 'sandbox',
                    'merchant_id' => $settings['payfast_merchant_id'] ?? null,
                    'merchant_key' => $settings['payfast_merchant_key'] ?? null,
                    'passphrase' => $settings['payfast_passphrase'] ?? null,
                ];

            case 'tap':
                return [
                    'enabled' => isPaymentMethodEnabled('tap', $userId),
                    'secret_key' => $settings['tap_secret_key'] ?? null,
                ];

            case 'xendit':
                return [
                    'enabled' => isPaymentMethodEnabled('xendit', $userId),
                    'api_key' => $settings['xendit_api_key'] ?? null,
                ];

            case 'paytr':
                return [
                    'enabled' => isPaymentMethodEnabled('paytr', $userId),
                    'merchant_id' => $settings['paytr_merchant_id'] ?? null,
                    'merchant_key' => $settings['paytr_merchant_key'] ?? null,
                    'merchant_salt' => $settings['paytr_merchant_salt'] ?? null,
                ];

            case 'mollie':
                return [
                    'enabled' => isPaymentMethodEnabled('mollie', $userId),
                    'api_key' => $settings['mollie_api_key'] ?? null,
                ];

            case 'toyyibpay':
                return [
                    'enabled' => isPaymentMethodEnabled('toyyibpay', $userId),
                    'category_code' => $settings['toyyibpay_category_code'] ?? null,
                    'secret_key' => $settings['toyyibpay_secret_key'] ?? null,
                    'mode' => $settings['toyyibpay_mode'] ?? 'sandbox',
                ];

            case 'cashfree':
                return [
                    'enabled' => isPaymentMethodEnabled('cashfree', $userId),
                    'mode' => $settings['cashfree_mode'] ?? 'sandbox',
                    'public_key' => $settings['cashfree_public_key'] ?? null,
                    'secret_key' => $settings['cashfree_secret_key'] ?? null,
                ];

            case 'iyzipay':
                return [
                    'enabled' => isPaymentMethodEnabled('iyzipay', $userId),
                    'mode' => $settings['iyzipay_mode'] ?? 'sandbox',
                    'public_key' => $settings['iyzipay_public_key'] ?? null,
                    'secret_key' => $settings['iyzipay_secret_key'] ?? null,
                ];

            case 'benefit':
                return [
                    'enabled' => isPaymentMethodEnabled('benefit', $userId),
                    'mode' => $settings['benefit_mode'] ?? 'sandbox',
                    'public_key' => $settings['benefit_public_key'] ?? null,
                    'secret_key' => $settings['benefit_secret_key'] ?? null,
                ];

            case 'ozow':
                return [
                    'enabled' => isPaymentMethodEnabled('ozow', $userId),
                    'mode' => $settings['ozow_mode'] ?? 'sandbox',
                    'site_key' => $settings['ozow_site_key'] ?? null,
                    'private_key' => $settings['ozow_private_key'] ?? null,
                    'api_key' => $settings['ozow_api_key'] ?? null,
                ];

            case 'easebuzz':
                return [
                    'enabled' => isPaymentMethodEnabled('easebuzz', $userId),
                    'merchant_key' => $settings['easebuzz_merchant_key'] ?? null,
                    'salt_key' => $settings['easebuzz_salt_key'] ?? null,
                    'environment' => $settings['easebuzz_environment'] ?? 'test',
                ];

            case 'khalti':
                return [
                    'enabled' => isPaymentMethodEnabled('khalti', $userId),
                    'public_key' => $settings['khalti_public_key'] ?? null,
                    'secret_key' => $settings['khalti_secret_key'] ?? null,
                ];

            case 'authorizenet':
                return [
                    'enabled' => isPaymentMethodEnabled('authorizenet', $userId),
                    'mode' => $settings['authorizenet_mode'] ?? 'sandbox',
                    'merchant_id' => $settings['authorizenet_merchant_id'] ?? null,
                    'transaction_key' => $settings['authorizenet_transaction_key'] ?? null,
                    'supported_countries' => ['US', 'CA', 'GB', 'AU'],
                    'supported_currencies' => ['USD', 'CAD', 'CHF', 'DKK', 'EUR', 'GBP', 'NOK', 'PLN', 'SEK', 'AUD', 'NZD'],
                ];

            case 'fedapay':
                return [
                    'enabled' => isPaymentMethodEnabled('fedapay', $userId),
                    'mode' => $settings['fedapay_mode'] ?? 'sandbox',
                    'public_key' => $settings['fedapay_public_key'] ?? null,
                    'secret_key' => $settings['fedapay_secret_key'] ?? null,
                ];

            case 'payhere':
                return [
                    'enabled' => isPaymentMethodEnabled('payhere', $userId),
                    'mode' => $settings['payhere_mode'] ?? 'sandbox',
                    'merchant_id' => $settings['payhere_merchant_id'] ?? null,
                    'merchant_secret' => $settings['payhere_merchant_secret'] ?? null,
                    'app_id' => $settings['payhere_app_id'] ?? null,
                    'app_secret' => $settings['payhere_app_secret'] ?? null,
                ];

            case 'cinetpay':
                return [
                    'enabled' => isPaymentMethodEnabled('cinetpay', $userId),
                    'site_id' => $settings['cinetpay_site_id'] ?? null,
                    'api_key' => $settings['cinetpay_api_key'] ?? null,
                    'secret_key' => $settings['cinetpay_secret_key'] ?? null,
                ];

            case 'midtrans':
                return [
                    'enabled' => isPaymentMethodEnabled('midtrans', $userId),
                    'mode' => $settings['midtrans_mode'] ?? 'sandbox',
                    'secret_key' => $settings['midtrans_secret_key'] ?? null,
                    'client_key' => $settings['midtrans_client_key'] ?? null,
                ];

            case 'paiement':
                return [
                    'enabled' => isPaymentMethodEnabled('paiement', $userId),
                    'merchant_id' => $settings['paiement_merchant_id'] ?? null,
                ];

            case 'paymentwall':
                return [
                    'enabled' => isPaymentMethodEnabled('paymentwall', $userId),
                    'mode' => $settings['paymentwall_mode'] ?? 'sandbox',
                    'public_key' => $settings['paymentwall_public_key'] ?? null,
                    'private_key' => $settings['paymentwall_private_key'] ?? null,
                ];

            case 'sspay':
                return [
                    'enabled' => isPaymentMethodEnabled('sspay', $userId),
                    'secret_key' => $settings['sspay_secret_key'] ?? null,
                    'category_code' => $settings['sspay_category_code'] ?? null,
                ];

            case 'yookassa':
                return [
                    'enabled' => isPaymentMethodEnabled('yookassa', $userId),
                    'shop_id' => $settings['yookassa_shop_id'] ?? null,
                    'secret_key' => $settings['yookassa_secret_key'] ?? null,
                ];

            case 'aamarpay':
                return [
                    'enabled' => isPaymentMethodEnabled('aamarpay', $userId),
                    'store_id' => $settings['aamarpay_store_id'] ?? null,
                    'signature' => $settings['aamarpay_signature'] ?? null,
                    'mode' => $settings['aamarpay_mode'] ?? 'sandbox',
                ];

            default:
                return [];
        }
    }
}

if (! function_exists('getEnabledPaymentMethods')) {
    /**
     * Get all enabled payment methods
     *
     * @param int|null $userId
     * @return array
     */
    function getEnabledPaymentMethods($userId = null)
    {
        $userId = $userId ?: getPaymentSettingsUserId();
        $methods = ['stripe', 'paypal', 'razorpay', 'mercadopago', 'paystack', 'flutterwave', 'bank_transfer', 'paytabs', 'skrill', 'coingate', 'payfast', 'tap', 'xendit', 'paytr', 'mollie', 'toyyibpay', 'cashfree', 'iyzipay', 'benefit', 'ozow', 'easebuzz', 'khalti', 'authorizenet', 'fedapay', 'payhere', 'cinetpay', 'paiement', 'paymentwall', 'sspay', 'yookassa', 'aamarpay'];
        $enabled = [];

        foreach ($methods as $method) {
            if (isPaymentMethodEnabled($method, $userId)) {
                $enabled[$method] = getPaymentMethodConfig($method, $userId);
            }
        }

        return $enabled;
    }
}

if (! function_exists('validatePaymentMethodConfig')) {
    /**
     * Validate payment method configuration
     *
     * @param string $method
     * @param array $config
     * @return array [valid => bool, errors => array]
     */
    function validatePaymentMethodConfig($method, $config)
    {
        $errors = [];

        switch ($method) {
            case 'stripe':
                if (empty($config['key'])) {
                    $errors[] = 'Stripe publishable key is required';
                }
                if (empty($config['secret'])) {
                    $errors[] = 'Stripe secret key is required';
                }
                break;

            case 'paypal':
                if (empty($config['client_id'])) {
                    $errors[] = 'PayPal client ID is required';
                }
                if (empty($config['secret'])) {
                    $errors[] = 'PayPal secret key is required';
                }
                break;

            case 'razorpay':
                if (empty($config['key'])) {
                    $errors[] = 'Razorpay key ID is required';
                }
                if (empty($config['secret'])) {
                    $errors[] = 'Razorpay secret key is required';
                }
                break;

            case 'mercadopago':
                if (empty($config['access_token'])) {
                    $errors[] = 'MercadoPago access token is required';
                }
                break;

            case 'bank_transfer':
                if (empty($config['details'])) {
                    $errors[] = 'Bank details are required';
                }
                break;

            case 'paytabs':
                if (empty($config['server_key'])) {
                    $errors[] = 'PayTabs server key is required';
                }
                if (empty($config['profile_id'])) {
                    $errors[] = 'PayTabs profile id is required';
                }
                if (empty($config['region'])) {
                    $errors[] = 'PayTabs region is required';
                }
                break;

            case 'skrill':
                if (empty($config['merchant_id'])) {
                    $errors[] = 'Skrill merchant ID is required';
                }
                if (empty($config['secret_word'])) {
                    $errors[] = 'Skrill secret word is required';
                }
                break;

            case 'coingate':
                if (empty($config['api_token'])) {
                    $errors[] = 'CoinGate API token is required';
                }
                break;

            case 'payfast':
                if (empty($config['merchant_id'])) {
                    $errors[] = 'Payfast merchant ID is required';
                }
                if (empty($config['merchant_key'])) {
                    $errors[] = 'Payfast merchant key is required';
                }
                break;

            case 'tap':
                if (empty($config['secret_key'])) {
                    $errors[] = 'Tap secret key is required';
                }
                break;

            case 'xendit':
                if (empty($config['api_key'])) {
                    $errors[] = 'Xendit api key is required';
                }
                break;

            case 'paytr':
                if (empty($config['merchant_id'])) {
                    $errors[] = 'PayTR merchant ID is required';
                }
                if (empty($config['merchant_key'])) {
                    $errors[] = 'PayTR merchant key is required';
                }
                if (empty($config['merchant_salt'])) {
                    $errors[] = 'PayTR merchant salt is required';
                }
                break;

            case 'mollie':
                if (empty($config['api_key'])) {
                    $errors[] = 'Mollie API key is required';
                }
                break;

            case 'toyyibpay':
                if (empty($config['category_code'])) {
                    $errors[] = 'toyyibPay category code is required';
                }
                if (empty($config['secret_key'])) {
                    $errors[] = 'toyyibPay secret key is required';
                }
                break;

            case 'cashfree':
                if (empty($config['public_key'])) {
                    $errors[] = 'Cashfree App ID is required';
                }
                if (empty($config['secret_key'])) {
                    $errors[] = 'Cashfree Secret Key is required';
                }
                break;

            case 'iyzipay':
                if (empty($config['public_key'])) {
                    $errors[] = 'Iyzipay API key is required';
                }
                if (empty($config['secret_key'])) {
                    $errors[] = 'Iyzipay secret key is required';
                }
                break;

            case 'benefit':
                if (empty($config['public_key'])) {
                    $errors[] = 'Benefit API key is required';
                }
                if (empty($config['secret_key'])) {
                    $errors[] = 'Benefit secret key is required';
                }
                break;

            case 'ozow':
                if (empty($config['site_key'])) {
                    $errors[] = 'Ozow site key is required';
                }
                if (empty($config['private_key'])) {
                    $errors[] = 'Ozow private key is required';
                }
                break;

            case 'easebuzz':
                if (empty($config['merchant_key'])) {
                    $errors[] = 'Easebuzz merchant key is required';
                }
                if (empty($config['salt_key'])) {
                    $errors[] = 'Easebuzz salt key is required';
                }
                break;

            case 'khalti':
                if (empty($config['public_key'])) {
                    $errors[] = 'Khalti public key is required';
                }
                if (empty($config['secret_key'])) {
                    $errors[] = 'Khalti secret key is required';
                }
                break;

            case 'authorizenet':
                if (empty($config['merchant_id'])) {
                    $errors[] = 'AuthorizeNet merchant ID is required';
                }
                if (empty($config['transaction_key'])) {
                    $errors[] = 'AuthorizeNet transaction key is required';
                }
                break;

            case 'fedapay':
                if (empty($config['public_key'])) {
                    $errors[] = 'FedaPay public key is required';
                }
                if (empty($config['secret_key'])) {
                    $errors[] = 'FedaPay secret key is required';
                }
                break;

            case 'payhere':
                if (empty($config['merchant_id'])) {
                    $errors[] = 'PayHere merchant ID is required';
                }
                if (empty($config['merchant_secret'])) {
                    $errors[] = 'PayHere merchant secret is required';
                }
                break;

            case 'cinetpay':
                if (empty($config['site_id'])) {
                    $errors[] = 'CinetPay site ID is required';
                }
                if (empty($config['api_key'])) {
                    $errors[] = 'CinetPay API key is required';
                }
                break;

            case 'paiement':
                if (empty($config['merchant_id'])) {
                    $errors[] = 'Paiement Pro merchant ID is required';
                }
                break;

            case 'nepalste':
                if (empty($config['public_key'])) {
                    $errors[] = 'Nepalste public key is required';
                }
                if (empty($config['secret_key'])) {
                    $errors[] = 'Nepalste secret key is required';
                }
                break;

            case 'yookassa':
                if (empty($config['shop_id'])) {
                    $errors[] = 'YooKassa shop ID is required';
                }
                if (empty($config['secret_key'])) {
                    $errors[] = 'YooKassa secret key is required';
                }
                break;

            case 'midtrans':
                if (empty($config['secret_key'])) {
                    $errors[] = 'Midtrans secret key is required';
                }
                break;

            case 'aamarpay':
                if (empty($config['store_id'])) {
                    $errors[] = 'Aamarpay store ID is required';
                }
                if (empty($config['signature'])) {
                    $errors[] = 'Aamarpay signature is required';
                }
                break;

            case 'paymentwall':
                if (empty($config['public_key'])) {
                    $errors[] = 'PaymentWall public key is required';
                }
                if (empty($config['private_key'])) {
                    $errors[] = 'PaymentWall private key is required';
                }
                break;

            case 'sspay':
                if (empty($config['secret_key'])) {
                    $errors[] = 'SSPay secret key is required';
                }
                break;
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}

if (! function_exists('calculatePlanPricing')) {
    function calculatePlanPricing($plan, $couponCode = null, $billingCycle = 'monthly')
    {
        // Use the plan's method to get correct price for billing cycle
        $originalPrice = $plan->getPriceForCycle($billingCycle);
        $discountAmount = 0;
        $finalPrice = $originalPrice;
        $couponId = null;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)
                ->where('status', 1)
                ->first();

            if ($coupon) {
                if ($coupon->type === 'percentage') {
                    $discountAmount = ($originalPrice * $coupon->discount_amount) / 100;
                } else {
                    $discountAmount = min($coupon->discount_amount, $originalPrice);
                }
                $finalPrice = max(0, $originalPrice - $discountAmount);
                $couponId = $coupon->id;
            }
        }

        return [
            'original_price' => $originalPrice,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice,
            'coupon_id' => $couponId
        ];
    }
}

if (! function_exists('createPlanOrder')) {
    function createPlanOrder($data)
    {
        $plan = Plan::findOrFail($data['plan_id']);
        $billingCycle = $data['billing_cycle'] ?? 'monthly';
        $pricing = calculatePlanPricing($plan, $data['coupon_code'] ?? null, $billingCycle);

        return PlanOrder::create([
            'user_id' => $data['user_id'],
            'plan_id' => $plan->id,
            'coupon_id' => $pricing['coupon_id'],
            'billing_cycle' => $billingCycle,
            'payment_method' => $data['payment_method'],
            'coupon_code' => $data['coupon_code'] ?? null,
            'original_price' => $pricing['original_price'],
            'discount_amount' => $pricing['discount_amount'],
            'final_price' => $pricing['final_price'],
            'payment_id' => $data['payment_id'],
            'status' => $data['status'] ?? 'pending',
            'ordered_at' => now(),
        ]);
    }
}

if (! function_exists('assignPlanToUser')) {
    function assignPlanToUser($user, $plan, $billingCycle)
    {
        // Validate billing cycle
        if (!in_array($billingCycle, ['monthly', 'yearly'])) {
            throw new \InvalidArgumentException('Invalid billing cycle: ' . $billingCycle);
        }

        // Calculate expiration date based on billing cycle
        $expiresAt = $billingCycle === 'yearly' ? now()->addYear() : now()->addMonth();

        \Log::info('Assigning plan ' . $plan->id . ' to user ' . $user->id . ' with billing cycle ' . $billingCycle);

        // Update user with new plan and clear trial status
        $updated = $user->update([
            'plan_id' => $plan->id,
            'plan_expire_date' => $expiresAt,
            'plan_is_active' => 1,
            // Clear trial status when assigning paid plan
            'is_trial' => null,
            'trial_day' => 0,
            'trial_expire_date' => null,
        ]);

        \Log::info('Plan assignment result: ' . ($updated ? 'success' : 'failed'));

        return $updated;
    }
}

if (! function_exists('processPaymentSuccess')) {
    function processPaymentSuccess($data)
    {
        try {

            $plan = Plan::findOrFail($data['plan_id']);
            $user = User::findOrFail($data['user_id']);

            if (!$user) {
                throw new \Exception('User not found for payment success processing');
            }

            if (!$user->email) {
                \Log::warning('User found but email is null', [
                    'user_id' => $user->id,
                    'user_name' => $user->name
                ]);
            }

            $planOrder = createPlanOrder(array_merge($data, ['status' => 'approved']));
            assignPlanToUser($user, $plan, $data['billing_cycle']);

            // Verify the plan was assigned
            $user->refresh();

            // Create referral record if user was referred
            \App\Http\Controllers\ReferralController::createReferralRecord($user);

            return $planOrder;
        } catch (\Exception $e) {
            \Log::error('processPaymentSuccess failed: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}

if (! function_exists('getPaymentGatewaySettings')) {
    function getPaymentGatewaySettings($tenantId = null)
    {
        if (is_null($tenantId)) {
            $tenantId = createdBy();
        } elseif (is_numeric($tenantId)) {
            $tenantId = User::find($tenantId)?->tenant_id ?? $tenantId;
        }

        return [
            'payment_settings' => PaymentSetting::getUserSettings($tenantId),
            'general_settings' => Setting::getUserSettings($tenantId),
            'tenant_id' => $tenantId
        ];
    }
}

if (! function_exists('validatePaymentRequest')) {
    function validatePaymentRequest($request, $additionalRules = [])
    {
        $baseRules = [
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'coupon_code' => 'nullable|string',
        ];

        return $request->validate(array_merge($baseRules, $additionalRules));
    }
}

if (! function_exists('handlePaymentError')) {
    function handlePaymentError($e, $method = 'payment')
    {
        return back()->withErrors(['error' => __('Paymenth processing failed: :message', ['message' => $e->getMessage()])]);
    }
}

if (! function_exists('getPaymentSettingsUserId')) {
    /**
     * Get the correct user ID for payment settings based on context
     *
     * @param int|null $invoiceCreatorId
     * @return int|null
     */
    function getPaymentSettingsUserId($invoiceCreatorId = null)
    {
        // If invoice creator ID is provided, use it
        if ($invoiceCreatorId) {
            return $invoiceCreatorId;
        }

        // If authenticated user is company or superadmin, use their ID
        if (auth()->check() && in_array(auth()->user()->type, ['superadmin', 'company'])) {
            return auth()->id();
        }

        // Fallback to superadmin
        return User::where('type', 'superadmin')->first()?->id;
    }
}

if (! function_exists('getPaymentMethodConfig')) {
    /**
     * Get payment method configuration for a specific gateway
     *
     * @param string $method
     * @param int|null $userId
     * @return array
     */
    function getPaymentMethodConfig($method, $userId = null)
    {
        $tenantIdOrUserId = $userId ?: getPaymentSettingsUserId();
        $settings = getPaymentSettings($tenantIdOrUserId);

        // Handle specific method configurations
        if ($method === 'skrill') {
            return [
                'enabled' => ($settings["is_{$method}_enabled"] ?? '0') === '1',
                'merchant_id' => $settings['skrill_merchant_id'] ?? null,
                'secret_word' => $settings['skrill_secret_word'] ?? null,
            ];
        }

        $apiKey = null;
        if ($method === 'xendit') {
            $apiKey = $settings['xendit_api_key'] ?? $settings['xendit_secret_key'] ?? null;
        } elseif ($method === 'paystack') {
            $apiKey = $settings['paystack_secret_key'] ?? null;
        } else {
            $apiKey = $settings["{$method}_api_key"] ?? $settings["{$method}_secret_key"] ?? null;
        }

        return [
            'enabled' => ($settings["is_{$method}_enabled"] ?? '0') === '1',
            'api_key' => $apiKey,
            'api_key_exists' => !empty($apiKey),
        ];
    }
}

if (! function_exists('defaultSettings')) {
    /**
     * Get default settings for System, Brand, Storage, and Currency configurations
     *
     * @return array
     */
    function defaultSettings()
    {
        return [
            // System Settings
            'DEFAULT_COUNTRY' => 'SA',
            'DEFAULT_LANGUAGE' => 'ar',
            'DATE_FORMAT' => 'Y-m-d',
            'TIME_FORMAT' => 'H:i',
            'CALENDAR_START_DAY' => 'sunday',
            'DEFAULT_TIMEZONE' => 'Asia/Riyadh',
            'ENABLE_EMAIL_VERIFICATION' => true,
            'LANDING_PAGE_ENABLED' => false,
            'DEFAULT_TAX_RATE' => '15',
            'RECAPTCHA_ENABLED' => config('services.recaptcha.enabled'),
            'RECAPTCHA_VERSION' => config('services.recaptcha.version', 'v3'),
            'RECAPTCHA_SITE_KEY' => config('services.recaptcha.site_key', ''),
            'RECAPTCHA_SECRET_KEY' => config('services.recaptcha.secret_key', ''),

            // Brand Settings
            'LOGO_DARK' => '/images/logos/logo-dark.png',
            'LOGO_LIGHT' => '/images/logos/logo-light.png',
            'FAVICON' => '/images/logos/favicon.ico',
            'TITLE_TEXT' => 'Sard app - تطبيق سرد',
            'FOOTER_TEXT' => '© 2026 Sard . All rights reserved. - جميع الحقوق محفوظة لشركة سرد 2026',
            'THEME_COLOR' => 'green',
            'CUSTOM_COLOR' => '#205341',
            'SIDEBAR_VARIANT' => 'inset',
            'SIDEBAR_STYLE' => 'plain',
            'LAYOUT_DIRECTION' => 'left',
            'THEME_MODE' => 'light',

            // Storage Settings
            'STORAGE_TYPE' => config('filesystems.default', 'local'),
            'STORAGE_FILE_TYPES' => 'jpg,png,webp,gif,pdf,doc,docx,txt,csv',
            'STORAGE_MAX_UPLOAD_SIZE' => '2048',
            'AWS_ACCESS_KEY_ID' => config('services.aws.access_key_id', ''),
            'AWS_SECRET_ACCESS_KEY' => config('services.aws.secret_access_key', ''),
            'AWS_DEFAULT_REGION' => config('services.aws.default_region', 'us-east-1'),
            'AWS_BUCKET' => config('services.aws.bucket', ''),
            'AWS_URL' => config('services.aws.url', ''),
            'AWS_ENDPOINT' => config('services.aws.endpoint', ''),
            'WASABI_ACCESS_KEY' => '',
            'WASABI_SECRET_KEY' => '',
            'WASABI_REGION' => 'us-east-1',
            'WASABI_BUCKET' => '',
            'WASABI_URL' => '',
            'WASABI_ROOT' => '',

            // Currency Settings
            'DECIMAL_FORMAT' => '2',
            'DEFAULT_CURRENCY' => 'SAR',
            'DECIMAL_SEPARATOR' => '.',
            'THOUSANDS_SEPARATOR' => ',',
            'FLOAT_NUMBER' => true,
            'CURRENCY_SYMBOL_SPACE' => false,
            'CURRENCY_SYMBOL_POSITION' => 'before',

            // Slack Settings
            'SLACK_ENABLED' => false,
            'SLACK_WEBHOOK_URL' => '',

            // Email Settings
            'EMAIL_PROVIDER' => 'smtp',
            'EMAIL_DRIVER' => 'smtp',
            'EMAIL_HOST' => config('mail.mailers.smtp.host', 'smtp.emailit.com'),
            'EMAIL_PORT' => (string) config('mail.mailers.smtp.port', 587),
            'EMAIL_USERNAME' => config('mail.mailers.smtp.username', 'emailit'),
            'EMAIL_PASSWORD' => config('mail.mailers.smtp.password', ''),
            'EMAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption', 'tls'),
            'EMAIL_FROM_ADDRESS' => config('mail.from.address', 'no-reply@sard.app'),
            'EMAIL_FROM_NAME' => config('mail.from.name', config('app.name', 'Sard')),

            'ENABLE_LOGGING' => true,
            'STRICTLY_NECESSARY_COOKIES' => true,
            'CONTACT_US_URL' => 'https://sard.app',
            'COOKIE_TITLE_EN' => 'Cookie Consent',
            'COOKIE_TITLE_AR' => 'إشعار ملفات تعريف الارتباط',
            'STRICTLY_COOKIE_TITLE_EN' => 'Strictly Necessary Cookies',
            'STRICTLY_COOKIE_TITLE_AR' => 'ملفات تعريف الارتباط الضرورية',
            'COOKIE_DESCRIPTION_EN' => 'We use cookies to improve your browsing experience, analyze website performance, and provide content tailored to your preferences.',
            'COOKIE_DESCRIPTION_AR' => 'نستخدم ملفات تعريف الارتباط لتحسين تجربة التصفح، وتحليل أداء الموقع، وتقديم محتوى يتناسب مع تفضيلاتك.',
            'STRICTLY_COOKIE_DESCRIPTION_EN' => 'These cookies are essential for the proper functioning of the website and cannot be disabled as they enable core features such as security and accessibility.',
            'STRICTLY_COOKIE_DESCRIPTION_AR' => 'تُعد ملفات تعريف الارتباط هذه ضرورية لعمل الموقع بشكل صحيح، ولا يمكن تعطيلها، حيث تُمكّن الميزات الأساسية مثل الأمان وإمكانية الوصول.',
            'CONTACT_US_TITLE_EN' => 'Contact Us',
            'CONTACT_US_TITLE_AR' => 'إشعار ملفات تعريف الارتباط',
            'CONTACT_US_DESCRIPTION_EN' => 'If you have any questions or concerns regarding our cookie policy, please feel free to contact us.',
            'CONTACT_US_DESCRIPTION_AR' => 'إذا كان لديك أي استفسار أو ملاحظات بخصوص سياسة ملفات تعريف الارتباط، يُرجى التواصل معنا.',
        ];
    }
}

if (! function_exists('createDefaultSettings')) {
    /**
     * Create default settings for a user
     *
     * @param string|null $tenant_id
     * @return void
     */
    function createDefaultSettings(?string $tenant_id = null)
    {
        $defaults = defaultSettings();
        $settingsData = [];

        foreach ($defaults as $key => $value) {
            $settingsData[] = [
                'tenant_id' => $tenant_id,
                'key' => $key,
                'value' => is_bool($value) ? ($value ? '1' : '0') : (string)$value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Setting::insert($settingsData);
    }
}


if (! function_exists('formatCurrency')) {
    /**
     * Format amount with company/superadmin currency settings. Options: variant, userId, useCode, currencyCode, html, rtl.
     */
    function formatCurrency($amount, array $options = [])
    {
        $amount = (float) $amount;
        $html = ! empty($options['html']);
        $rtl = ! empty($options['rtl']);
        $fallback = '$' . number_format($amount, 2);

        $userId = $options['userId'] ?? null;
        if (($options['variant'] ?? 'company') === 'superadmin') {
            $superAdmin = User::where('type', 'superadmin')->first();
            $userId = $superAdmin?->tenant_id;
        } elseif ($userId === null && auth()->check()) {
            $userId = auth()->user()->type === 'company' ? auth()->id() : auth()->user()->tenant_id;
        }
        if ($userId === null) {
            return $html ? '<span class="pdf-currency-wrap">' . $fallback . '</span>' : $fallback;
        }

        $s = (array) settings($userId);
        $currencyCode = $options['currencyCode'] ?? $s['defaultCurrency']
            ?? \App\Models\CompanySetting::where('tenant_id', $userId)->where('setting_key', 'currency')->value('setting_value')
            ?? 'USD';

        $position = strtolower(trim((string) ($s['currencySymbolPosition'] ?? 'before')));
        $position = in_array($position, ['before', 'after'], true) ? $position : 'before';
        if ($rtl) {
            $position = $position === 'before' ? 'after' : 'before';
        }

        $space = in_array($s['currencySymbolSpace'] ?? false, [true, '1', 1], true) ? ' ' : '';
        if (($s['floatNumber'] ?? '1') === '0') {
            $amount = floor($amount);
        }
        $num = number_format($amount, (int) ($s['decimalFormat'] ?? 2), '.', $s['thousandsSeparator'] ?? ',');

        $isSar = strtoupper((string) $currencyCode) === 'SAR';
        $symbolPart = ($html && $isSar)
            ? '<svg class="pdf-sar-icon" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m20 19.5-5.5 1.2"/><path d="M14.5 4v11.22a1 1 0 0 0 1.242.97L20 15.2"/><path d="m2.978 19.351 5.549-1.363A2 2 0 0 0 10 16V2"/><path d="M20 10 4 13.5"/></svg>'
            : (($options['useCode'] ?? false) ? $currencyCode : (\App\Models\Currency::where('code', $currencyCode)->first()?->symbol ?? '$'));

        $text = $position === 'after' ? $num . $space . $symbolPart : $symbolPart . $space . $num;

        if ($html) {
            $class = $isSar ? 'pdf-currency-wrap pdf-currency-sar' : 'pdf-currency-wrap';
            return '<span class="' . $class . '">' . ($isSar ? $text : e($text)) . '</span>';
        }
        return $text;
    }
}

if (! function_exists('createdBy')) {
    function createdBy()
    {
        return Auth::user()?->tenant_id;
    }
}
if (! function_exists('getCompanyOwnerId')) {
    /**
     * Get the company owner ID for notifications
     * Always returns the company owner ID regardless of who is performing the action
     *
     * @return int
     */
    function getCompanyOwnerId()
    {
        $user = Auth::user();

        if ($user->type === 'superadmin') {
            return $user->id;
        } elseif ($user->type === 'company') {
            return $user->id;
        } else {
            // For employees, advocates, staff - return the company owner ID for this tenant
            $company = \App\Models\User::where('tenant_id', $user->tenant_id)->where('type', 'company')->first();
            return $company ? $company->id : $user->id;
        }
    }
}

if (! function_exists('isEmailTemplateEnabled')) {
    /**
     * Check if an email template is enabled for a tenant
     *
     * @param EmailTemplateName $templateName
     * @param null $userId
     * @return bool
     */
    function isEmailTemplateEnabled(EmailTemplateName $templateName, $userId = null): bool
    {
        $tenantId = $userId ? \App\Models\User::find($userId)?->tenant_id : tenant('id');
        if (!$tenantId) {
            return false;
        }

        $template = \App\Models\EmailTemplate::where('type', $templateName->value)->first();
        if (!$template) {
            return false;
        }

        $tenantTemplate = \App\Models\TenantEmailTemplate::where('tenant_id', $tenantId)
            ->where('template_id', $template->id)
            ->first();

        return $tenantTemplate ? $tenantTemplate->is_active : false;
    }
}

if (!function_exists('parseBrowserData')) {
    function parseBrowserData(string $userAgent): array
    {
        $browser = 'Unknown';
        $os = 'Unknown';
        $deviceType = 'desktop';

        // Browser detection
        if (preg_match('/Chrome\/([0-9.]+)/', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox\/([0-9.]+)/', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari\/([0-9.]+)/', $userAgent) && !preg_match('/Chrome/', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge\/([0-9.]+)/', $userAgent)) {
            $browser = 'Edge';
        }

        // OS detection
        if (preg_match('/Windows NT/', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac OS X/', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/Linux/', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/', $userAgent)) {
            $os = 'Android';
            $deviceType = 'mobile';
        } elseif (preg_match('/iPhone|iPad/', $userAgent)) {
            $os = 'iOS';
            $deviceType = preg_match('/iPad/', $userAgent) ? 'tablet' : 'mobile';
        }

        return [
            'browser_name' => $browser,
            'os_name' => $os,
            'browser_language' => 'en',
            'device_type' => $deviceType,
        ];
    }
}

if (! function_exists('getCompanyAndUsersId')) {
    function getCompanyAndUsersId()
    {
        $user = Auth::user();
        if ($user->tenant_id === null) {
            return [$user->id];
        }
        if ($user->hasRole(['company'])) {
            $companyUserIds = User::where('tenant_id', $user->tenant_id)->pluck('id')->toArray();
            if (!in_array($user->id, $companyUserIds)) {
                $companyUserIds[] = $user->id;
            }
            return $companyUserIds;
        }
        $companyUserIds = User::where('tenant_id', $user->tenant_id)->pluck('id')->toArray();
        return $companyUserIds;
    }
}

if (! function_exists('getTwilioConfig')) {
    function getTwilioConfig()
    {
        return [
            'twilio_sid' => getSetting('twilio_sid', ''),
            'twilio_token' => getSetting('twilio_token', ''),
            'twilio_from' => getSetting('twilio_from', '')
        ];
    }
}

if (! function_exists('isTwilioEnabled')) {
    /**
     * Check if Twilio is enabled for a user
     *
     * @param int|null $userId
     * @return bool
     */
    function isTwilioEnabled($userId = null)
    {
        if (is_null($userId)) {
            $userId = createdBy();
        }

        return getSetting('twilio_enabled', false, $userId) === '1';
    }
}

if (! function_exists('isNotificationTemplateEnabled')) {
    /**
     * Check if a notification template is enabled for a tenant and specific type
     *
     * @param EmailTemplateName $templateName
     * @param null $userId
     * @param null $type (slack, twilio, email)
     * @return bool
     */
    function isNotificationTemplateEnabled(\App\Enum\EmailTemplateName $templateName, $userId = null, $type = null)
    {
        $tenantId = $userId ? \App\Models\User::find($userId)?->tenant_id : tenant('id');
        if (!$tenantId) {
            return false;
        }

        $templateQuery = \App\Models\NotificationTemplate::where('name', $templateName->value);

        // If type is specified, filter by type in notification template
        if ($type) {
            $templateQuery->where('type', $type);
        }

        $template = $templateQuery->first();
        if (!$template) {
            return false;
        }

        $query = \App\Models\TenantNotificationTemplate::where('tenant_id', $tenantId)
            ->where('template_id', $template->id);

        // If type is specified, also filter by type in tenant template
        if ($type) {
            $query->where('type', $type);
        }

        $tenantTemplate = $query->first();

        return $tenantTemplate ? (bool) $tenantTemplate->is_active : false;
    }
}

if (! function_exists('isNotificationTypeEnabled')) {
    /**
     * Check if a specific notification type (twilio/slack) is enabled for a template and tenant
     *
     * @param string $templateName
     * @param string $type (twilio, slack, email)
     * @param int|null $userId
     * @return bool
     */
    function isNotificationTypeEnabled($templateName, $type, $userId = null)
    {
        $tenantId = $userId ? \App\Models\User::find($userId)?->tenant_id : tenant('id');
        if (!$tenantId) {
            return false;
        }

        return \App\Models\TenantNotificationTemplate::isNotificationActive($templateName, $tenantId, $type);
    }
}

if (! function_exists('setNotificationTypeStatus')) {
    /**
     * Set notification type status for a template and tenant
     *
     * @param string $templateName
     * @param string $type (twilio, slack, email)
     * @param bool $isActive
     * @param int|null $userId
     * @return bool
     */
    function setNotificationTypeStatus($templateName, $type, $isActive, $userId = null)
    {
        $tenantId = $userId ? \App\Models\User::find($userId)?->tenant_id : tenant('id');
        if (!$tenantId) {
            return false;
        }

        return \App\Models\TenantNotificationTemplate::setNotificationStatus($templateName, $tenantId, $type, $isActive);
    }
}

if (! function_exists('createDefaultNotificationSettings')) {
    /**
     * Create default notification settings for a new tenant (company)
     *
     * @param string $tenantId
     * @return void
     */
    function createDefaultNotificationSettings($tenantId)
    {
        $templates = \App\Models\NotificationTemplate::all();
        $types = ['email', 'twilio', 'slack'];

        foreach ($templates as $template) {
            foreach ($types as $type) {
                \App\Models\TenantNotificationTemplate::updateOrCreate(
                    [
                        'tenant_id' => $tenantId,
                        'template_id' => $template->id,
                        'type' => $type
                    ],
                    ['is_active' => false] // Default to disabled
                );
            }
        }
    }
}

if (! function_exists('syncNotificationTemplatesForAllCompanies')) {
    /**
     * Sync notification templates for all existing companies
     *
     * @return void
     */
    function syncNotificationTemplatesForAllCompanies()
    {
        $companies = \App\Models\User::where('type', 'company')->whereNotNull('tenant_id')->get();

        foreach ($companies as $company) {
            \App\Jobs\SeedNotificationTemplates::dispatchSync($company->tenant_id);
        }
    }
}

if (! function_exists('isSlackEnabled')) {
    /**
     * Check if Slack is enabled for a user
     *
     * @param int|null $userId
     * @return bool
     */
    function isSlackEnabled($userId = null)
    {
        if (is_null($userId)) {
            $userId = createdBy();
        }

        return getSetting('slack_enabled', false, $userId) === '1';
    }
}

if (! function_exists('getSlackWebhookUrl')) {
    /**
     * Get Slack webhook URL for a user
     *
     * @param int|null $userId
     * @return string
     */
    function getSlackWebhookUrl($userId = null)
    {
        if (is_null($userId)) {
            $userId = createdBy();
        }

        return getSetting('slack_webhook_url', '', $userId);
    }
}

if (! function_exists('updateSlackSetting')) {
    /**
     * Update or create a Slack setting
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $userId
     * @return \App\Models\Setting
     */
    function updateSlackSetting($key, $value, $userId = null)
    {
        if (is_null($userId)) {
            $userId = createdBy();
        }

        return updateSetting($key, $value, $userId);
    }
}



