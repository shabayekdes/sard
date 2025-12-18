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
        // Get company users
        $companyUsers = User::where('type', 'company')->get();

        foreach ($companyUsers as $companyUser) {
            // Create default client types for each company
            $clientTypes = [
                [
                    'name' => 'فرد',
                    'status' => 'active',
                ],
                [
                    'name' => 'مؤسسة فردية',
                    'status' => 'active',
                ],
                [
                    'name' => 'شركة توصية بسيطة',
                    'status' => 'active',
                ],
                [
                    'name' => 'شركة تضامنية',
                    'status' => 'active',
                ],
                [
                    'name' => 'شركة ذات مسئولية محدودة',
                    'status' => 'active',
                ],
                [
                    'name' => 'شركة مساهمة عامة',
                    'status' => 'active',
                ],
                [
                    'name' => 'شركة أجنبية',
                    'status' => 'active',
                ],
                [
                    'name' => 'شركة خليجية',
                    'status' => 'active',
                ],
                [
                    'name' => 'شركة مساهمة مقفلة',
                    'status' => 'active',
                ],
            ];

            foreach ($clientTypes as $clientTypeData) {
                // Check if client type already exists
                $exists = ClientType::where('name', $clientTypeData['name'])
                    ->where('created_by', $companyUser->id)
                    ->exists();

                if (!$exists) {
                    ClientType::create([
                        'name' => $clientTypeData['name'],
                        'status' => $clientTypeData['status'],
                        'created_by' => $companyUser->id,
                    ]);
                }
            }
        }
    }
}
