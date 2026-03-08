<?php

namespace Database\Seeders;

use App\Enums\BusinessType;
use App\Enums\TenantCity;
use App\Events\TenantVerified;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Stancl\Tenancy\Events\TenantCreated;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get default plan
        $defaultPlan = Plan::where('is_default', true)->first();
        $plans = Plan::all();
        
        // Company names
        $companyNames = [
            'Demo Company',
            'Dev Industries',
            'Acme Corporation',
        ];
        
        // Create company users
        foreach ($companyNames as $index => $companyName) {
            $email = strtolower(str_replace(' ', '', $companyName)) . '@example.com';
            
            // Skip if user already exists
            if (User::where('email', $email)->exists()) {
                continue;
            }

            $plan = $plans->random();

            $tenant = Tenant::create();
            $tenant->update([
                'name' => $companyName,
                'email' => $email,
                'phone' => $faker->phoneNumber(),
                'city' => $faker->randomElement(TenantCity::cases())->value,
                'company_name' => $companyName,
                'business_type' => $faker->randomElement(BusinessType::cases())->value,
                'plan_id' => $plan->id,
                'plan_expire_date' => now()->addYear(),
                'plan_is_active' => 1,
                'requested_plan' => 0,
                'storage_limit' => 0,
                'storage_used' => 0,
                'is_trial' => null,
                'trial_day' => 0,
                'trial_expire_date' => null,
            ]);
            $tenant->createDomain(str($companyName)->lower()->before(' ')->append('.' . config('app.domain'))->toString());

            // Create user (plan lives on tenant, not user)
            $user = User::create([
                'name' => $companyName,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'type' => 'company',
                'lang' => $faker->randomElement(['en', 'es', 'fr', 'de']),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'tenant_id' => $tenant->id,
            ]);

            event(new TenantVerified($tenant));
            // Assign company role
            $user->assignRole('company');
        }
        
        $this->command->info('Created ' . count($companyNames) . ' company users successfully!');
    }
}