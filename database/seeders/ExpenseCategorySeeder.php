<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => json_encode([
                    'en' => 'Office Rent',
                    'ar' => 'إيجار المكتب',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode([
                    'en' => 'Utilities',
                    'ar' => 'فواتير خدمات',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode([
                    'en' => 'Office Supplies',
                    'ar' => 'قرطاسية ومستلزمات مكتبية',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode([
                    'en' => 'Maintenance & Cleaning',
                    'ar' => 'صيانة وتنظيف',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode([
                    'en' => 'Salaries',
                    'ar' => 'رواتب الموظفين',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode([
                    'en' => 'Allowances & Incentives',
                    'ar' => 'بدلات وحوافز',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode([
                    'en' => 'Court Fees',
                    'ar' => 'رسوم محاكم',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode([
                    'en' => 'Case Fees',
                    'ar' => 'رسوم قضايا',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode([
                    'en' => 'Transportation',
                    'ar' => 'مواصلات',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode([
                    'en' => 'Software Subscriptions',
                    'ar' => 'اشتراكات برامج',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode([
                    'en' => 'Miscellaneous Expenses',
                    'ar' => 'مصاريف متفرقة',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        ExpenseCategory::insert($categories);
    }
}