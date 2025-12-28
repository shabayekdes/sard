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
        $clientTypes = [
            [
                'name' => json_encode(['en' => 'Individual', 'ar' => 'فرد']),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode(['en' => 'Sole Proprietorship', 'ar' => 'مؤسسة فردية']),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode(['en' => 'Simple Partnership', 'ar' => 'شركة توصية بسيطة']),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode(['en' => 'Partnership', 'ar' => 'شركة تضامنية']),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode(['en' => 'Limited Liability Company', 'ar' => 'شركة ذات مسئولية محدودة']),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode(['en' => 'Public Joint Stock Company', 'ar' => 'شركة مساهمة عامة']),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode(['en' => 'Foreign Company', 'ar' => 'شركة أجنبية']),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode(['en' => 'Gulf Company', 'ar' => 'شركة خليجية']),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode(['en' => 'Closed Joint Stock Company', 'ar' => 'شركة مساهمة مقفلة']),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        ClientType::insert($clientTypes);
    }
}
