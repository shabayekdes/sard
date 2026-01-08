<?php

namespace Database\Factories;

use App\Models\CaseModel;
use App\Models\CaseType;
use App\Models\CaseStatus;
use App\Models\CaseCategory;
use App\Models\Client;
use App\Models\Court;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CaseModelFactory extends Factory
{
    protected $model = CaseModel::class;

    public function definition(): array
    {
        $filingDate = $this->faker->optional(0.8)->dateTimeBetween('-1 year', 'now');
        $expectedCompletion = $filingDate 
            ? $this->faker->dateTimeBetween($filingDate, '+1 year')
            : null;

        $caseTitles = [
            'Contract Dispute Resolution',
            'Corporate Merger Review',
            'Employment Law Case',
            'Personal Injury Claim',
            'Real Estate Transaction',
            'Intellectual Property Dispute',
            'Family Law Matter',
            'Criminal Defense Case',
            'Tax Law Consultation',
            'Immigration Case',
            'Commercial Litigation',
            'Labor Dispute',
            'Property Rights Case',
            'Trademark Infringement',
            'Breach of Contract',
            'Medical Malpractice',
            'Product Liability',
            'Environmental Compliance',
            'Securities Fraud',
            'Antitrust Investigation',
        ];

        return [
            'title' => $this->faker->randomElement($caseTitles),
            'description' => $this->faker->optional(0.9)->paragraph(rand(2, 5)),
            'file_number' => $this->faker->optional(0.7)->bothify('FN-####-####'),
            'attributes' => $this->faker->optional(0.6)->randomElement(['petitioner', 'respondent']),
            'client_id' => function () {
                return Client::inRandomOrder()->first()?->id ?? Client::factory();
            },
            'case_type_id' => function () {
                return CaseType::inRandomOrder()->first()?->id ?? CaseType::factory();
            },
            'case_status_id' => function () {
                return CaseStatus::inRandomOrder()->first()?->id ?? CaseStatus::factory();
            },
            'case_category_id' => $this->faker->optional(0.7)->passthrough(function () {
                return CaseCategory::whereNull('parent_id')->inRandomOrder()->first()?->id;
            }),
            'case_subcategory_id' => function (array $attributes) {
                if (!empty($attributes['case_category_id'])) {
                    return CaseCategory::where('parent_id', $attributes['case_category_id'])
                        ->inRandomOrder()
                        ->first()?->id;
                }
                return null;
            },
            'court_id' => $this->faker->optional(0.6)->passthrough(function () {
                return Court::inRandomOrder()->first()?->id;
            }),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'filing_date' => $filingDate,
            'expected_completion_date' => $expectedCompletion,
            'estimated_value' => $this->faker->optional(0.7)->randomFloat(2, 1000, 500000),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'created_by' => function () {
                return User::where('type', 'company')->inRandomOrder()->first()?->id ?? User::factory();
            },
        ];
    }

    /**
     * Indicate that the case should be active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the case should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Set the priority level.
     */
    public function priority(string $priority): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => $priority,
        ]);
    }

    /**
     * Set high priority.
     */
    public function highPriority(): static
    {
        return $this->priority('high');
    }

    /**
     * Set low priority.
     */
    public function lowPriority(): static
    {
        return $this->priority('low');
    }

    /**
     * Set medium priority.
     */
    public function mediumPriority(): static
    {
        return $this->priority('medium');
    }
}

