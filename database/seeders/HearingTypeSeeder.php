<?php

namespace Database\Seeders;

use App\Models\HearingType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HearingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (User::where('type', 'company')->get() as $user) {
        
            $availableHearingTypes = [
                [
                    'name' => [
                        'en' => 'First Hearing',
                        'ar' => 'جلسة أولى'
                    ],
                    'description' => [
                        'en' => 'First hearing in a case to establish basic facts and procedures',
                        'ar' => 'أول جلسة يتم فيها نظر الدعوى، التحقق من أطرافها، تسجيل الطلبات، وتحديد مسار القضية.'
                    ],
                    'duration_estimate' => 60,
                    'status' => 'active',
                ],
                [
                    'name' => [
                        'en' => 'Pleading Hearing',
                        'ar' => 'جلسة مرافعة'
                    ],
                    'description' => [
                        'en' => 'Hearing where each party presents their arguments and pleadings orally or in writing',
                        'ar' => 'جلسة يقدم فيها كل طرف دفوعه ومرافعاته شفهيًا أو كتابيًا.'
                    ],
                    'duration_estimate' => 60,
                    'status' => 'active',
                ],
                [
                    'name' => [
                        'en' => 'Response Hearing',
                        'ar' => 'جلسة ردود'
                    ],
                    'description' => [
                        'en' => 'Hearing for responding to memorandums or arguments submitted by the other party',
                        'ar' => 'جلسة للرد على المذكرات أو الدفوع المقدمة من الطرف الآخر.'
                    ],
                    'duration_estimate' => 60,
                    'status' => 'active',
                ],
                [
                    'name' => [
                        'en' => 'Evidence Hearing',
                        'ar' => 'جلسة إثبات'
                    ],
                    'description' => [
                        'en' => 'Hearing dedicated to presenting evidence such as documents, witnesses, or evidence',
                        'ar' => 'جلسة مخصصة لتقديم الأدلة مثل المستندات أو الشهود أو القرائن.'
                    ],
                    'duration_estimate' => 60,
                    'status' => 'active',
                ],
                [
                    'name' => [
                        'en' => 'Witness Hearing',
                        'ar' => 'جلسة سماع شهود'
                    ],
                    'description' => [
                        'en' => 'Hearing where witness statements are heard and they are questioned by the judge or parties',
                        'ar' => 'يتم فيها سماع أقوال الشهود ومناقشتهم من القاضي أو الأطراف.'
                    ],
                    'duration_estimate' => 60,
                    'status' => 'active',
                ],
                [
                    'name' => [
                        'en' => 'Expert Hearing',
                        'ar' => 'جلسة خبرة'
                    ],
                    'description' => [
                        'en' => 'Hearing to discuss the expert report or assign them a technical task (accounting, engineering, etc.)',
                        'ar' => 'جلسة لمناقشة تقرير الخبير أو تكليفه بمهمة فنية (محاسبية، هندسية، إلخ).'
                    ],
                    'duration_estimate' => 60,
                    'status' => 'active',
                ],
                [
                    'name' => [
                        'en' => 'Settlement Hearing',
                        'ar' => 'جلسة صلح'
                    ],
                    'description' => [
                        'en' => 'Aimed at attempting settlement between parties before continuing with the case',
                        'ar' => 'تهدف لمحاولة الصلح بين الأطراف قبل الاستمرار في نظر القضية.'
                    ],
                    'duration_estimate' => 60,
                    'status' => 'active',
                ],
                [
                    'name' => [
                        'en' => 'Judgment Hearing',
                        'ar' => 'جلسة نطق بالحكم'
                    ],
                    'description' => [
                        'en' => 'The hearing where the judgment is issued or the date for pronouncing it is set',
                        'ar' => 'الجلسة التي يتم فيها إصدار الحكم أو تحديد موعد النطق به.'
                    ],
                    'duration_estimate' => 60,
                    'status' => 'active',
                ],
                [
                    'name' => [
                        'en' => 'Follow-up Hearing',
                        'ar' => 'جلسة استكمال'
                    ],
                    'description' => [
                        'en' => 'Hearing to complete missing procedures such as submitting documents or additional responses',
                        'ar' => 'جلسة لاستكمال إجراءات ناقصة مثل تقديم مستندات أو ردود إضافية.'
                    ],
                    'duration_estimate' => 60,
                    'status' => 'active',
                ],
                [
                    'name' => [
                        'en' => 'Administrative Hearing',
                        'ar' => 'جلسة إدارية'
                    ],
                    'description' => [
                        'en' => 'Procedural hearing to organize the file or correct data without entering into the subject matter',
                        'ar' => 'جلسة إجرائية لتنظيم الملف أو تصحيح بيانات دون الدخول في الموضوع.'
                    ],
                    'duration_estimate' => 60,
                    'status' => 'active',
                ],
                [
                    'name' => [
                        'en' => 'Appeal Hearing',
                        'ar' => 'جلسة استئناف'
                    ],
                    'description' => [
                        'en' => 'Hearing to consider the appeal of the judgment before the Court of Appeal',
                        'ar' => 'جلسة لنظر الطعن في الحكم أمام محكمة الاستئناف.'
                    ],
                    'duration_estimate' => 60,
                    'status' => 'active',
                ],
                [
                    'name' => [
                        'en' => 'Review Hearing',
                        'ar' => 'جلسة تدقيق'
                    ],
                    'description' => [
                        'en' => 'Reviewing the case without pleading (often in appeal)',
                        'ar' => 'نظر القضية تدقيقًا دون مرافعة (غالبًا في الاستئناف).'
                    ],
                    'duration_estimate' => 60,
                    'status' => 'active',
                ],
                [
                    'name' => [
                        'en' => 'Enforcement Hearing',
                        'ar' => 'جلسة تنفيذ'
                    ],
                    'description' => [
                        'en' => 'Hearing related to judgment enforcement procedures (service suspension, seizure, etc.)',
                        'ar' => 'جلسة متعلقة بإجراءات تنفيذ الحكم (إيقاف خدمات، حجز، إلخ).'
                    ],
                    'duration_estimate' => 60,
                    'status' => 'active',
                ],
            ];
            
            // Create all hearing types for this company
            foreach ($availableHearingTypes as $typeData) {
                $existing = HearingType::where('created_by', $user->id)
                    ->whereJsonContains('name->en', $typeData['name']['en'])
                    ->first();
                
                if (!$existing) {
                    HearingType::create([
                        ...$typeData,
                        'created_by' => $user->id,
                    ]);
                }
            }
        }
    }
}