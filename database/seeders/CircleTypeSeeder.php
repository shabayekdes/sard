<?php

namespace Database\Seeders;

use App\Models\CircleType;
use App\Models\User;
use Illuminate\Database\Seeder;

class CircleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $companyUsers = User::where('type', 'company')->get();

        // Circle types data
        $circleTypesData = [
            [
                'id' => 1,
                'name' => ['en' => 'Final Court', 'ar' => 'الدائرة الانتهائية']
            ],
            [
                'id' => 2,
                'name' => ['en' => 'Final Court (Title Deeds)', 'ar' => 'الدائرة الانتهائية (حجج الإستحكام)']
            ],
            [
                'id' => 3,
                'name' => ['en' => 'Juvenile Court', 'ar' => 'الدائرة الأحداث']
            ],
            [
                'id' => 4,
                'name' => ['en' => 'Personal Status Court', 'ar' => 'الدائرة الأحوال الشخصية']
            ],
            [
                'id' => 5,
                'name' => ['en' => 'Execution Court', 'ar' => 'الدائرة التنفيذ']
            ],
            [
                'id' => 6,
                'name' => ['en' => 'Criminal Court', 'ar' => 'الدائرة الجزائية']
            ],
            [
                'id' => 7,
                'name' => ['en' => 'Execution Court (Seizure and Enforcement Division)', 'ar' => 'الدائرة التنفيذ (دائرة الحجز والتنفيذ)']
            ],
            [
                'id' => 8,
                'name' => ['en' => 'Criminal Court (First Quintuple Division)', 'ar' => 'الدائرة الجزائية (الدائرة الخماسية الأولى)']
            ],
            [
                'id' => 9,
                'name' => ['en' => 'Judicial Court', 'ar' => 'الدائرة القضائية']
            ],
            [
                'id' => 10,
                'name' => ['en' => 'Civil Court', 'ar' => 'الدائرة الحقوقية']
            ],
            [
                'id' => 11,
                'name' => ['en' => 'Judicial Court (Criminal Division)', 'ar' => 'الدائرة القضائية (الدائرة الجزائية)']
            ],
            [
                'id' => 12,
                'name' => ['en' => 'Judicial Court (Qisas and Hudud Divisions)', 'ar' => 'الدائرة القضائية (دوائر القصاص والحدود)']
            ],
            [
                'id' => 13,
                'name' => ['en' => 'Traffic Court', 'ar' => 'الدائرة المرورية']
            ],
            [
                'id' => 14,
                'name' => ['en' => 'Endowments and Wills Court', 'ar' => 'دائرة الأوقاف والوصايا']
            ],
            [
                'id' => 15,
                'name' => ['en' => 'Juvenile and Girls\' Courts', 'ar' => 'دوائر الأحداث والفتيات']
            ],
            [
                'id' => 16,
                'name' => ['en' => 'Personal Status Divisions', 'ar' => 'دوائر الأحوال الشخصية']
            ],
            [
                'id' => 17,
                'name' => ['en' => 'First Notary Public', 'ar' => 'كتابة العدل الأولى']
            ],
            [
                'id' => 18,
                'name' => ['en' => 'Judicial Court', 'ar' => 'الدائرة القضائية']
            ],
            [
                'id' => 19,
                'name' => ['en' => 'Traffic Court', 'ar' => 'الدائرة المرورية']
            ],
            [
                'id' => 20,
                'name' => ['en' => 'Judicial Court (Endowments and Inheritance Division)', 'ar' => 'الدائرة القضائية (دائرة الأوقاف والمواريث)']
            ],
            [
                'id' => 21,
                'name' => ['en' => 'Second Notary Public', 'ar' => 'كتابة العدل الثانية']
            ],
            [
                'id' => 22,
                'name' => ['en' => 'Criminal Court (First Joint Division)', 'ar' => 'الدائرة الجزائية (المشتركة الأولى)']
            ],
            [
                'id' => 23,
                'name' => ['en' => 'Criminal Court (Second Joint Division)', 'ar' => 'الدائرة الجزائية (المشتركة الثانية)']
            ],
            [
                'id' => 24,
                'name' => ['en' => 'Judicial Court (Third Joint Division)', 'ar' => 'الدائرة القضائية (المشتركة الثالثة)']
            ],
            [
                'id' => 25,
                'name' => ['en' => 'Criminal Court (First Individual Division)', 'ar' => 'الدائرة الجزائية (الفردية الأولى)']
            ],
            [
                'id' => 26,
                'name' => ['en' => 'Criminal Court (Second Individual Division)', 'ar' => 'الدائرة الجزائية (الفردية الثانية)']
            ],
            [
                'id' => 27,
                'name' => ['en' => 'Criminal Court (First Triple Division)', 'ar' => 'الدائرة الجزائية (الدائرة الثلاثية الأولى)']
            ],
            [
                'id' => 28,
                'name' => ['en' => 'Criminal Court (Third Triple Division)', 'ar' => 'الدائرة الجزائية (الدائرة الثلاثية الثالثة)']
            ],
            [
                'id' => 29,
                'name' => ['en' => 'Criminal Court (Second Triple Division)', 'ar' => 'الدائرة الجزائية (الدائرة الثلاثية الثانية)']
            ],
            [
                'id' => 30,
                'name' => ['en' => 'Judicial Court (Juvenile Division)', 'ar' => 'الدائرة القضائية (دائرة الأحداث)']
            ],
            [
                'id' => 31,
                'name' => ['en' => 'Judicial Court (Women\'s Prison Division)', 'ar' => 'الدائرة القضائية (سجن النساء)']
            ],
            [
                'id' => 32,
                'name' => ['en' => 'Notary Public', 'ar' => 'كتابة العدل']
            ],
        ];

        // Color palette for circle types
        $colors = [
            '#3B82F6', '#10B981', '#EF4444', '#8B5CF6', '#F59E0B',
            '#DC2626', '#059669', '#F97316', '#84CC16', '#06B6D4',
            '#6B7280', '#EC4899', '#14B8A6', '#A855F7', '#F43F5E',
            '#0EA5E9', '#22C55E', '#EAB308', '#FB923C', '#9333EA',
            '#C026D3', '#6366F1', '#8B5CF6', '#EC4899', '#F43F5E',
            '#10B981', '#059669', '#0D9488', '#14B8A6', '#2DD4BF',
            '#06B6D4', '#0891B2', '#0EA5E9', '#0284C7', '#0369A1'
        ];

        foreach ($companyUsers as $companyUser) {
            // Create all circle types for this company
            foreach ($circleTypesData as $index => $circleTypeData) {
                $existing = CircleType::where('created_by', $companyUser->id)
                    ->whereJsonContains('name->en', $circleTypeData['name']['en'])
                    ->first();
                
                if (!$existing) {
                    CircleType::create([
                        'name' => $circleTypeData['name'],
                        'description' => null,
                        'color' => $colors[$index % count($colors)],
                        'status' => 'active',
                        'created_by' => $companyUser->id
                    ]);
                }
            }
        }
    }
}

