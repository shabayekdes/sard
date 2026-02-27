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