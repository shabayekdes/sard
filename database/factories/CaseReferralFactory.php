<?php

namespace Database\Factories;

use App\Models\CaseModel;
use App\Models\CaseReferral;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CaseReferral>
 */
class CaseReferralFactory extends Factory
{
    protected $model = CaseReferral::class;

    public function definition(): array
    {
        return [
            'case_id' => CaseModel::factory(),
            'tenant_id' => null,
            'stage' => $this->faker->randomElement([
                'amicable_settlement',
                'reconciliation',
                'first_instance',
                'appeal',
                'supreme_court',
                'execution',
            ]),
            'referral_date' => $this->faker->date(),
            'reminder_enabled' => false,
            'reminder_duration' => null,
            'stage_data' => [],
            'attachments' => [],
            'notes' => $this->faker->optional()->sentence(),
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}
