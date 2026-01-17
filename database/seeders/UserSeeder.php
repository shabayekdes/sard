<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $isDemo = config('app.is_demo', true);

        // Create Super Admin User
        
        $superAdmin = User::firstOrCreate(
            ['email' => 'esmail@sard.app'],
            [
                'name' => 'Super Admin',
                'email_verified_at' => now(),
                'password' => bcrypt('12345678'),
                'type' => 'superadmin',
                'lang' => 'en'
            ]
        );

        // Assign super admin role
        $superAdmin->assignRole('superadmin');

        // Create default settings for superadmin if not exists
        if (!Setting::where('user_id', $superAdmin->id)->exists()) {
            createDefaultSettings($superAdmin->id);
        }

        $recaptchaSettings = [
            'recaptchaVersion' => 'v3',
            'recaptchaSiteKey' => config('services.recaptcha.site_key', ''),
            'recaptchaSecretKey' => config('services.recaptcha.secret_key', ''),
        ];

        foreach ($recaptchaSettings as $key => $value) {
            Setting::updateOrCreate(
                ['user_id' => $superAdmin->id, 'key' => $key],
                ['value' => (string) $value]
            );
        }
    }
}
