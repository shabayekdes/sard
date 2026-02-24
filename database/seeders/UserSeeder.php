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
    }
}
