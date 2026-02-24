<?php

namespace App\Providers;

use App\Helpers\AssetHelper;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AssetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register a custom Blade directive for our asset helper
        Blade::directive('dynamicAsset', function ($expression) {
            return "<?php echo App\\Helpers\\AssetHelper::asset($expression); ?>";
        });
        
        // Register a custom Blade directive for Vite assets
        Blade::directive('dynamicVite', function ($expression) {
            return "<?php echo App\\Helpers\\AssetHelper::viteAsset($expression); ?>";
        });

        // Format money using company currency; for SAR uses Lucide Saudi Riyal icon (HTML). Output is unescaped.
        // Usage: @money($amount) or @money($amount, $invoice->tenant_id) or @money($amount, $invoice->tenant_id, true) for RTL.
        Blade::directive('money', function ($expression) {
            return "<?php \$__e = [{$expression}]; echo formatCurrency(\$__e[0], array_merge(['html' => true], isset(\$__e[1]) ? ['userId' => \$__e[1]] : [], isset(\$__e[2]) ? ['rtl' => (bool)\$__e[2]] : [])); ?>";
        });

        // Format money as plain text (symbol + number, escaped). Use when HTML is not desired.
        // Usage: @moneyText($amount) or @moneyText($amount, $userId)
        Blade::directive('moneyText', function ($expression) {
            return "<?php \$__e = [{$expression}]; echo e(formatCurrency(\$__e[0], isset(\$__e[1]) ? ['userId' => \$__e[1]] : [])); ?>";
        });
    }
}