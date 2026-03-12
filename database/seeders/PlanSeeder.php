<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => '{"en":"Free","ar":"مجاني"}',
                'price' => 0,
                'yearly_price' => 0,
                'billing_cycle' => 'yearly',
                'duration' => 'monthly',
                'description' => '{"en":"Basic plan for solo practitioners just getting started.","ar":"خطة أساسية للممارسين الفرديين الذين بدأوا للتو."}',
                'max_users' => 1,
                'max_cases' => 5,
                'max_clients' => 5,
                'enable_branding' => 'on',
                'enable_chatgpt' => 'off',
                'storage_limit' => 1,
                'is_trial' => 'on',
                'trial_day' => 14,
                'is_plan_enable' => 'on',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Professional","ar":"الأحترافية"}',
                'price' => 250,
                'yearly_price' => 2500, // 20% discount for yearly
                'billing_cycle' => 'yearly',
                'duration' => 'monthly',
                'description' => '{"en":"Unlimited users, cases, clients, and storage for large firms.","ar":"عدد غير محدود من المستخدمين والقضايا والعملاء والتخزين للمكاتب الكبيرة."}',
                'max_users' => 3,
                'max_cases' => -1,
                'max_clients' => -1,
                'enable_branding' => 'off',
                'enable_chatgpt' => 'on',
                'storage_limit' => 30,
                'is_trial' => 'on',
                'trial_day' => 14,
                'is_plan_enable' => 'on',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Plan::insert($plans);
    }
}