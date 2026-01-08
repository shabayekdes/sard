<?php

namespace Database\Seeders;

use App\Models\CaseModel;
use App\Models\CaseType;
use App\Models\CaseStatus;
use App\Models\CaseCategory;
use App\Models\Client;
use App\Models\Court;
use App\Models\User;
use Illuminate\Database\Seeder;

class CaseSeeder extends Seeder
{
    public function run(): void
    {
        $companyUsers = User::where('type', 'company')->get();

        if ($companyUsers->isEmpty()) {
            $this->command->warn('No company users found. Please run UserSeeder first.');
            return;
        }

        foreach ($companyUsers as $companyUser) {
            $caseTypes = CaseType::where('created_by', $companyUser->id)->get();
            $caseStatuses = CaseStatus::where('created_by', $companyUser->id)->get();
            $clients = Client::where('created_by', $companyUser->id)->get();
            $courts = Court::where('created_by', $companyUser->id)->get();
            $caseCategories = CaseCategory::where('created_by', $companyUser->id)->get();
            $parentCategories = $caseCategories->whereNull('parent_id');
            $subcategories = $caseCategories->whereNotNull('parent_id');

            if ($caseTypes->count() > 0 && $caseStatuses->count() > 0 && $clients->count() > 0) {
                // Create 8-12 cases per company
                $caseCount = rand(8, 12);

                for ($i = 0; $i < $caseCount; $i++) {
                    // Randomly select a parent category (70% chance)
                    $selectedCategory = null;
                    $selectedSubcategory = null;
                    
                    if ($parentCategories->count() > 0 && rand(1, 10) <= 7) {
                        $selectedCategory = $parentCategories->random();
                        
                        // 50% chance to also select a subcategory if available
                        if ($selectedCategory && $subcategories->where('parent_id', $selectedCategory->id)->count() > 0) {
                            if (rand(1, 10) <= 5) {
                                $selectedSubcategory = $subcategories->where('parent_id', $selectedCategory->id)->random();
                            }
                        }
                    }

                    CaseModel::factory()
                        ->active()
                        ->create([
                            'client_id' => $clients->random()->id,
                            'case_type_id' => $caseTypes->random()->id,
                            'case_status_id' => $caseStatuses->random()->id,
                            'case_category_id' => $selectedCategory?->id,
                            'case_subcategory_id' => $selectedSubcategory?->id,
                            'court_id' => $courts->count() > 0 && rand(1, 10) <= 6 ? $courts->random()->id : null,
                            'created_by' => $companyUser->id,
                        ]);
                }

                $totalCases = CaseModel::where('created_by', $companyUser->id)->count();
                $this->command->info("Created {$caseCount} cases for company user: {$companyUser->name} (Total: {$totalCases})");
            } else {
                $this->command->warn("Skipping case creation for {$companyUser->name} - missing required data (case types, statuses, or clients)");
            }
        }
    }
}
