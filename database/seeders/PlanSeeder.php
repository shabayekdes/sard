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
                'max_users' => 50,
                'max_cases' => 100,
                'max_clients' => 50,
                'enable_branding' => 'on',
                'enable_chatgpt' => 'off',
                'storage_limit' => 1,
                'is_trial' => null,
                'trial_day' => 0,
                'is_plan_enable' => 'on',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Starter","ar":"المبتدئ"}',
                'price' => 29.99,
                'yearly_price' => 287.90, // 20% discount for yearly
                'billing_cycle' => 'yearly',
                'duration' => 'monthly',
                'description' => '{"en":"Perfect for small law firms looking to grow their practice.","ar":"مثالية لمكاتب المحاماة الصغيرة التي تسعى للتوسع."}',
                'max_users' => 50,
                'max_cases' => 100,
                'max_clients' => 70,
                'enable_branding' => 'off',
                'enable_chatgpt' => 'off',
                'storage_limit' => 5,
                'is_trial' => 'on',
                'trial_day' => 7,
                'is_plan_enable' => 'on',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Professional","ar":"الاحترافي"}',
                'price' => 79.99,
                'yearly_price' => 767.90, // 20% discount for yearly
                'billing_cycle' => 'yearly',
                'duration' => 'monthly',
                'description' => '{"en":"Ideal for growing law firms with multiple attorneys and advanced needs.","ar":"مناسبة لمكاتب المحاماة النامية مع عدة محامين واحتياجات متقدمة."}',
                'max_users' => 70,
                'max_cases' => 200,
                'max_clients' => 100,
                'enable_branding' => 'off',
                'enable_chatgpt' => 'on',
                'storage_limit' => 50,
                'is_trial' => 'on',
                'trial_day' => 14,
                'is_plan_enable' => 'on',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Enterprise","ar":"المؤسسات"}',
                'price' => 149.99,
                'yearly_price' => 1439.90, // 20% discount for yearly
                'billing_cycle' => 'yearly',
                'duration' => 'monthly',
                'description' => '{"en":"For large law firms with unlimited cases and premium features.","ar":"لمكاتب المحاماة الكبيرة مع قضايا غير محدودة وميزات متقدمة."}',
                'max_users' => 100,
                'max_cases' => 300,
                'max_clients' => 150,
                'enable_branding' => 'off',
                'enable_chatgpt' => 'on',
                'storage_limit' => 70,
                'is_trial' => 'on',
                'trial_day' => 30,
                'is_plan_enable' => 'on',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Unlimited","ar":"غير محدود"}',
                'price' => 299.99,
                'yearly_price' => 2879.90, // 20% discount for yearly
                'billing_cycle' => 'yearly',
                'duration' => 'monthly',
                'description' => '{"en":"Unlimited users, cases, clients, and storage for large firms.","ar":"عدد غير محدود من المستخدمين والقضايا والعملاء والتخزين للمكاتب الكبيرة."}',
                'max_users' => -1,
                'max_cases' => -1,
                'max_clients' => -1,
                'enable_branding' => 'off',
                'enable_chatgpt' => 'on',
                'storage_limit' => -1,
                'is_trial' => 'on',
                'trial_day' => 30,
                'is_plan_enable' => 'on',
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Plan::insert($plans);
    }
}