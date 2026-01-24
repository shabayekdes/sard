<?php

namespace Database\Seeders;

use App\Models\PaymentSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSettingSeeder extends Seeder
{
    public function run(): void
    {
        $methods = config('payment_methods', []);

        $keys = [];

        foreach ($methods as $key => $method) {
            // Skip if not enabled
            if (! $method['enabled']) {
                continue;
            }

            if (is_array($method)) {
                foreach ($method as $subKey => $subValue) {
                    $fullKey = $key . '_' . $subKey;
                    $keys[$fullKey] = $subValue;
                }

            }
        }

        // Create for superadmin (user_id = 1)
        foreach ($keys as $key => $value) {
            PaymentSetting::firstOrCreate([
                'key' => $key,
                'user_id' => 1
            ], [
                'value' => $value
            ]);
        }

        // Create for all company users
        $companyUsers = User::where('type', 'company')->get();
        foreach ($companyUsers as $companyUser) {
            foreach ($keys as $key => $value) {
                PaymentSetting::firstOrCreate([
                    'key' => $key,
                    'user_id' => $companyUser->id
                ], [
                    'value' => $value
                ]);
            }
        }
    }
}
