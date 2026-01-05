<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $companyUsers = User::where('type', 'company')->get();
        
        foreach ($companyUsers as $companyUser) {
            $availableCategories = [
                [
                    'name' => [
                        'en' => 'Office Rent',
                        'ar' => 'إيجار المكتب',
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Utilities',
                        'ar' => 'فواتير خدمات',
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Office Supplies',
                        'ar' => 'قرطاسية ومستلزمات مكتبية',
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Maintenance & Cleaning',
                        'ar' => 'صيانة وتنظيف',
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Salaries',
                        'ar' => 'رواتب الموظفين',
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Allowances & Incentives',
                        'ar' => 'بدلات وحوافز',
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Court Fees',
                        'ar' => 'رسوم محاكم',
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Case Fees',
                        'ar' => 'رسوم قضايا',
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Transportation',
                        'ar' => 'مواصلات',
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Software Subscriptions',
                        'ar' => 'اشتراكات برامج',
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Miscellaneous Expenses',
                        'ar' => 'مصاريف متفرقة',
                    ],
                ],
            ];
            
            // Create all expense categories for this company
            foreach ($availableCategories as $categoryData) {
                // Check if category already exists for this user
                $existing = ExpenseCategory::where('created_by', $companyUser->id)
                    ->whereRaw("JSON_EXTRACT(name, '$.en') = ?", [$categoryData['name']['en']])
                    ->first();

                if (! $existing) {
                    ExpenseCategory::create([
                        'name' => $categoryData['name'],
                        'status' => 'active',
                        'created_by' => $companyUser->id,
                    ]);
                }
            }
        }
    }
}