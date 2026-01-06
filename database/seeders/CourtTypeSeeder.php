<?php

namespace Database\Seeders;

use App\Models\CourtType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourtTypeSeeder extends Seeder
{
    public function run(): void
    {
        $companyUsers = User::where('type', 'company')->get();

        foreach ($companyUsers as $companyUser) {

            // Create court types per company
            $availableCourtTypes = [
                [
                    'name' => ['en' => 'General Court', 'ar' => 'المحكمة العامة'],
                    'color' => '#10B981'
                ],
                [
                    'name' => ['en' => 'Criminal Court', 'ar' => 'المحكمة الجزائية'],
                    'color' => '#EF4444'
                ],
                [
                    'name' => ['en' => 'Personal Status Court', 'ar' => 'محكمة الأحوال الشخصية'],
                    'color' => '#8B5CF6'
                ],
                [
                    'name' => ['en' => 'Execution Court', 'ar' => 'المحكمة التنفيذ'],
                    'color' => '#F59E0B'
                ],
                [
                    'name' => ['en' => 'Court of Appeal', 'ar' => 'محكمة الإستئناف'],
                    'color' => '#DC2626'
                ],
                [
                    'name' => ['en' => 'Notary Public', 'ar' => 'كتابة العدل'],
                    'color' => '#3B82F6'
                ],
                [
                    'name' => ['en' => 'Criminal Court', 'ar' => 'محكمة الجزائية'],
                    'color' => '#059669'
                ],
                [
                    'name' => ['en' => 'Auditing Authority (Endowments and Inheritance Division)', 'ar' => 'هيئة التدقيق (لدائرة الأوقاف والمواريث)'],
                    'color' => '#F97316'
                ],
                [
                    'name' => ['en' => 'Execution Court', 'ar' => 'محكمة التنفيذ'],
                    'color' => '#84CC16'
                ],
                [
                    'name' => ['en' => 'Commercial Court', 'ar' => 'المحكمة التجارية'],
                    'color' => '#06B6D4'
                ],
                [
                    'name' => ['en' => 'Seizure and Execution Court', 'ar' => 'محكمة للحجز والتنفيذ'],
                    'color' => '#6B7280'
                ],
            ];

            // Create all court types for this company
            foreach ($availableCourtTypes as $type) {
                $existing = CourtType::where('created_by', $companyUser->id)
                    ->whereJsonContains('name->en', $type['name']['en'])
                    ->first();
                
                if (!$existing) {
                    CourtType::create([
                        'name' => $type['name'],
                        'color' => $type['color'],
                        'status' => 'active',
                        'created_by' => $companyUser->id
                    ]);
                }
            }
        }
    }
}
