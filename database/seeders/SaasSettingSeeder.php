<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SaasSettingSeeder extends Seeder
{
    /**
     * Seed SaaS-level settings (tenant_id null) from database/seeders/data/settings.php.
     */
    public function run(): void
    {
        $settings = require database_path('seeders/data/settings.php');

        foreach ($settings as $item) {
            Setting::updateOrCreate(
                [
                    'tenant_id' => null,
                    'key' => $item['key'],
                ],
                $item
            );
        }
    }
}
