<?php

namespace Database\Seeders;

use App\Models\CaseType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaseTypeSeeder extends Seeder
{
    public function run(): void
    {
        $companyUsers = User::where('type', 'company')->get();
        
        foreach ($companyUsers as $companyUser) {
            $availableCaseTypes = [
                [
                    'name' => [
                        'en' => 'Case Registration',
                        'ar' => 'تسجيل قضية'
                    ],
                    'description' => [
                        'en' => 'Formally creating and registering the case',
                        'ar' => 'إنشاء وقيد القضية رسميًا'
                    ],
                    'color' => '#3B82F6'
                ],
                [
                    'name' => [
                        'en' => 'Case Filing',
                        'ar' => 'قيد الدعوى'
                    ],
                    'description' => [
                        'en' => 'Accepting the case and assigning it an official number',
                        'ar' => 'قبول الدعوى وإعطاؤها رقم رسمي'
                    ],
                    'color' => '#10B981'
                ],
                [
                    'name' => [
                        'en' => 'Session Scheduled',
                        'ar' => 'تحديد جلسة'
                    ],
                    'description' => [
                        'en' => 'Setting a date for a court session',
                        'ar' => 'تحديد موعد جلسة قضائية'
                    ],
                    'color' => '#F59E0B'
                ],
                [
                    'name' => [
                        'en' => 'Session Held',
                        'ar' => 'انعقاد جلسة'
                    ],
                    'description' => [
                        'en' => 'Holding the session',
                        'ar' => 'عقد الجلسة'
                    ],
                    'color' => '#8B5CF6'
                ],
                [
                    'name' => [
                        'en' => 'Deadline',
                        'ar' => 'موعد نهائي'
                    ],
                    'description' => [
                        'en' => 'Mandatory date for submitting an action (memorandum / document)',
                        'ar' => 'تاريخ إلزامي لتقديم إجراء (مذكرة / مستند)'
                    ],
                    'color' => '#EF4444'
                ],
                [
                    'name' => [
                        'en' => 'Memorandum Submitted',
                        'ar' => 'تقديم مذكرة'
                    ],
                    'description' => [
                        'en' => 'Submitting a memorandum or response',
                        'ar' => 'تقديم مذكرة أو رد'
                    ],
                    'color' => '#06B6D4'
                ],
                [
                    'name' => [
                        'en' => 'Judgment Issued',
                        'ar' => 'نطق بالحكم'
                    ],
                    'description' => [
                        'en' => 'Issuing the judgment',
                        'ar' => 'صدور الحكم'
                    ],
                    'color' => '#059669'
                ],
                [
                    'name' => [
                        'en' => 'Meeting',
                        'ar' => 'اجتماع'
                    ],
                    'description' => [
                        'en' => 'Internal meeting or with the client regarding the case',
                        'ar' => 'اجتماع داخلي أو مع العميل بخصوص القضية'
                    ],
                    'color' => '#84CC16'
                ],
            ];
            
            // Create all case types for this company
            foreach ($availableCaseTypes as $caseTypeData) {
                // Check if case type already exists for this user
                $existing = CaseType::where('created_by', $companyUser->id)
                    ->whereRaw("JSON_EXTRACT(name, '$.en') = ?", [$caseTypeData['name']['en']])
                    ->first();

                if (! $existing) {
                    CaseType::create([
                        'name' => $caseTypeData['name'],
                        'description' => $caseTypeData['description'],
                        'color' => $caseTypeData['color'],
                        'status' => 'active',
                        'created_by' => $companyUser->id,
                    ]);
                }
            }
        }
    }
}