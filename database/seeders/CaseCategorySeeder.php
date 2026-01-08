<?php

namespace Database\Seeders;

use App\Models\CaseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class CaseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyUsers = User::where('type', 'company')->get();

        if ($companyUsers->isEmpty()) {
            $this->command->warn('No company users found. Please run UserSeeder first.');
            return;
        }

        foreach ($companyUsers as $companyUser) {
            // Create 5-8 parent categories per company
            $parentCount = rand(5, 8);
            $parentCategories = [];

            for ($i = 0; $i < $parentCount; $i++) {
                $parentCategory = CaseCategory::factory()
                    ->active()
                    ->create([
                        'created_by' => $companyUser->id,
                        'parent_id' => null,
                    ]);

                $parentCategories[] = $parentCategory;
            }

            // Create 2-4 subcategories for some parent categories
            $subcategoryCount = rand(2, 4);
            $selectedParents = collect($parentCategories)->random(min($subcategoryCount, count($parentCategories)));

            foreach ($selectedParents as $parent) {
                $childCount = rand(1, 3);
                for ($j = 0; $j < $childCount; $j++) {
                    CaseCategory::factory()
                        ->active()
                        ->create([
                            'created_by' => $companyUser->id,
                            'parent_id' => $parent->id,
                        ]);
                }
            }

            $totalCategories = CaseCategory::where('created_by', $companyUser->id)->count();
            $this->command->info("Created {$totalCategories} case categories for company user: {$companyUser->name}");
        }
    }
}

