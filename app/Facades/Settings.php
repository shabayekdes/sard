<?php

namespace App\Facades;

use App\Services\SettingService;
use Illuminate\Support\Facades\Facade;

class Settings extends Facade
{
    /**
     * Never cache the resolved instance. Each facade call resolves a fresh SettingService.
     * Stops group('mail') in TenancySetting from affecting later Settings::… calls.
     */
    protected static $cached = false;

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return SettingService::class;
    }
}