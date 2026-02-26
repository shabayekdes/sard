<?php
namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;
use App\Models\Currency;
use App\Models\User;
use App\Models\Setting;
use App\Services\StorageConfigService;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        // Base URL from current request (central or tenant domain) for Inertia/Ziggy/assets
        $baseUrl = $request->getSchemeAndHttpHost();
        $centralDomains = config('tenancy.central_domains', []);
        $isCentralDomain = in_array($request->getHost(), $centralDomains, true);
        $tenantDomain = $isCentralDomain ? null : $request->getHost();
        $tenantId = function_exists('tenant') ? (tenant()?->getTenantKey() ?? null) : null;

        // Skip database queries during installation
        if ($request->is('install/*') || $request->is('update/*') || !file_exists(storage_path('installed'))) {
            $globalSettings = [
                'currencySymbol' => '$',
                'currencyNname' => 'US Dollar',
                'base_url' => $baseUrl,
                'image_url' => $baseUrl,
                'is_demo' => false,//config('app.is_demo', false),
            ];
            $storageSettings = [
                'allowed_file_types' => 'jpg,png,webp,gif',
                'max_file_size_mb' => 2
            ];
        } else {
            // Get system settings
            $settings = sanitizeSettingsForUi(settings());
            // Get currency symbol
            $currencyCode = $settings['defaultCurrency'] ?? 'USD';
            $currency = Currency::where('code', $currencyCode)->first();
            $currencySettings = [];
            if ($currency) {
                $currencySettings = [
                    'currencySymbol' => $currency->symbol,
                    'currencyNname' => $currency->name
                ];
            } else {
                $currencySettings = [
                    'currencySymbol' =>  '$',
                    'currencyNname' => 'US Dollar'
                ];
            }

            // Get storage settings
            $storageSettings = [];
            try {
                $storageSettings = StorageConfigService::getStorageConfig();
            } catch (\Exception $e) {
                // Fallback to default settings if service fails
                $storageSettings = [
                    'allowed_file_types' => 'jpg,png,webp,gif',
                    'max_file_size_mb' => 2
                ];
            }

            // Get super admin currency settings for plans and referrals
            $superAdminCurrencySettings = [];
            try {
                $superAdmin = User::where('type', 'superadmin')->first();
                if ($superAdmin) {
                    $superAdminSettings = Setting::whereNull('tenant_id')
                        ->whereIn('key', ['decimalFormat', 'defaultCurrency', 'thousandsSeparator', 'currencySymbolSpace', 'currencySymbolPosition'])
                        ->pluck('value', 'key')
                        ->toArray();

                    $superAdminCurrencyCode = $superAdminSettings['defaultCurrency'] ?? 'USD';
                    $superAdminCurrency = Currency::where('code', $superAdminCurrencyCode)->first();

                    $superAdminCurrencySettings = [
                        'superAdminCurrencySymbol' => $superAdminCurrency ? $superAdminCurrency->symbol : '$',
                        'superAdminDecimalFormat' => $superAdminSettings['decimalFormat'] ?? '2',
                        'superAdminThousandsSeparator' => $superAdminSettings['thousandsSeparator'] ?? ',',
                        'superAdminCurrencySymbolSpace' => ($superAdminSettings['currencySymbolSpace'] ?? false) === '1',
                        'superAdminCurrencySymbolPosition' => $superAdminSettings['currencySymbolPosition'] ?? 'before',
                    ];
                }
            } catch (\Exception $e) {
                // Fallback to default super admin currency settings
                $superAdminCurrencySettings = [
                    'superAdminCurrencySymbol' => '$',
                    'superAdminDecimalFormat' => '2',
                    'superAdminThousandsSeparator' => ',',
                    'superAdminCurrencySymbolSpace' => false,
                    'superAdminCurrencySymbolPosition' => 'before',
                ];
            }

            // Merge currency settings with other settings
            $globalSettings = array_merge($settings, $currencySettings, $superAdminCurrencySettings);
            $globalSettings['base_url'] = $baseUrl;
            $globalSettings['image_url'] = $baseUrl;
            $globalSettings['is_demo'] = false; //config('app.is_demo', false);

        //     // Add cookie consent setting
        //     $cookieSetting = Setting::where('key', 'strictlyNecessaryCookies')->first();
        //     $globalSettings['strictlyNecessaryCookies'] = $cookieSetting ? (int)$cookieSetting->value : 0;
        //
        }

        return [
            ...parent::share($request),
            'name'  => config('app.name'),
            'base_url'  => $baseUrl,
            'image_url'  => $baseUrl,
            'isCentralDomain' => $isCentralDomain,
            'tenantDomain' => $tenantDomain,
            'tenantId' => $tenantId,
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'csrf_token' => csrf_token(),
            'auth'  => [
                'user'        => $request->user(),
                'roles'       => fn() => $request->user()?->roles->pluck('name'),
                'permissions' => fn() => $request->user()?->getAllPermissions()->pluck('name'),
            ],
            'isImpersonating' => session('impersonated_by') ? true : false,
            'ziggy' => fn(): array => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
                // Force base URL to current request origin so route() generates same-origin URLs (avoids CORS when on tenant domain)
                'url' => $request->getSchemeAndHttpHost(),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error'   => $request->session()->get('error'),
            ],
            'globalSettings' => $globalSettings,
            'storageSettings' => $storageSettings,
            'is_demo' => false, // config('app.is_demo')
        ];
    }
}
