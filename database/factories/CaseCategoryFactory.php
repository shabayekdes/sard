<?php

namespace Database\Factories;

use App\Models\CaseCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CaseCategoryFactory extends Factory
{
    protected $model = CaseCategory::class;

    public function definition(): array
    {
        $nameEn = $this->faker->randomElement([
            'Criminal Law',
            'Civil Law',
            'Corporate Law',
            'Family Law',
            'Employment Law',
            'Real Estate Law',
            'Intellectual Property',
            'Tax Law',
            'Immigration Law',
            'Contract Law',
            'Personal Injury',
            'Banking Law',
            'Environmental Law',
            'Healthcare Law',
            'Entertainment Law',
        ]);

        $nameAr = $this->faker->randomElement([
            'القانون الجنائي',
            'القانون المدني',
            'قانون الشركات',
            'قانون الأسرة',
            'قانون العمل',
            'قانون العقارات',
            'الملكية الفكرية',
            'قانون الضرائب',
            'قانون الهجرة',
            'قانون العقود',
            'الإصابات الشخصية',
            'قانون البنوك',
            'القانون البيئي',
            'قانون الرعاية الصحية',
            'قانون الترفيه',
        ]);

        return [
            'name' => [
                'en' => $nameEn,
                'ar' => $nameAr,
            ],
            'description' => $this->faker->boolean(70) ? [
                'en' => $this->faker->sentence(),
                'ar' => $this->faker->sentence(),
            ] : null,
            'parent_id' => null,
            'color' => $this->faker->randomElement([
                '#3B82F6', // Blue
                '#10B981', // Green
                '#F59E0B', // Amber
                '#EF4444', // Red
                '#8B5CF6', // Purple
                '#06B6D4', // Cyan
                '#84CC16', // Lime
                '#F97316', // Orange
                '#6B7280', // Gray
                '#EC4899', // Pink
            ]),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'created_by' => function () {
                return User::where('type', 'company')->inRandomOrder()->first()?->id ?? User::factory();
            },
        ];
    }

    /**
     * Indicate that the category should be active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the category should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Set a parent category.
     */
    public function withParent(CaseCategory $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
            'created_by' => $parent->created_by,
        ]);
    }
}

