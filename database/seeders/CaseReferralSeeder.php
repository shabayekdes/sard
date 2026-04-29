<?php

namespace Database\Seeders;

use App\Models\CaseModel;
use App\Models\CaseReferral;
use Illuminate\Database\Seeder;

class CaseReferralSeeder extends Seeder
{
    public function run(): void
    {
        CaseModel::query()->limit(10)->get()->each(function (CaseModel $case): void {
            CaseReferral::factory()->count(2)->create([
                'case_id' => $case->id,
                'tenant_id' => $case->tenant_id,
            ]);
        });
    }
}
