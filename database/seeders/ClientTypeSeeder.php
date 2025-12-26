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
                    'created_by' => $companyUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'مؤسسة فردية',
                    'status' => 'active',
                    'created_by' => $companyUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'شركة توصية بسيطة',
                    'status' => 'active',
                    'created_by' => $companyUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'شركة تضامنية',
                    'status' => 'active',
                    'created_by' => $companyUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'شركة ذات مسئولية محدودة',
                    'status' => 'active',
                    'created_by' => $companyUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'شركة مساهمة عامة',
                    'status' => 'active',
                    'created_by' => $companyUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'شركة أجنبية',
                    'status' => 'active',
                    'created_by' => $companyUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'شركة خليجية',
                    'status' => 'active',
                    'created_by' => $companyUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'شركة مساهمة مقفلة',
                    'status' => 'active',
                    'created_by' => $companyUser->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            ClientType::insert($clientTypes);
        }
    }
}
