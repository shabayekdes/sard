<?php

namespace Database\Seeders;

use App\Models\ClientType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyUsers = User::where('type', 'company')->get();
        
        foreach ($companyUsers as $companyUser) {
            $availableClientTypes = [
                [
                    'name' => [
                        'en' => 'Individual',
                        'ar' => 'فرد'
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Sole Proprietorship',
                        'ar' => 'مؤسسة فردية'
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Simple Partnership',
                        'ar' => 'شركة توصية بسيطة'
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Partnership',
                        'ar' => 'شركة تضامنية'
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Limited Liability Company',
                        'ar' => 'شركة ذات مسئولية محدودة'
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Public Joint Stock Company',
                        'ar' => 'شركة مساهمة عامة'
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Foreign Company',
                        'ar' => 'شركة أجنبية'
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Gulf Company',
                        'ar' => 'شركة خليجية'
                    ],
                ],
                [
                    'name' => [
                        'en' => 'Closed Joint Stock Company',
                        'ar' => 'شركة مساهمة مقفلة'
                    ],
                ],
            ];
            
            // Create all client types for this company
            foreach ($availableClientTypes as $clientTypeData) {
                // Check if client type already exists for this user
                $existing = ClientType::where('created_by', $companyUser->id)
                    ->whereRaw("JSON_EXTRACT(name, '$.en') = ?", [$clientTypeData['name']['en']])
                    ->first();

                if (! $existing) {
                    ClientType::create([
                        'name' => $clientTypeData['name'],
                        'status' => 'active',
                        'created_by' => $companyUser->id,
                    ]);
                }
            }
        }
    }
}
