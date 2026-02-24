<?php

namespace Database\Seeders;

use App\Models\CaseCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class CaseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companyUsers = User::where('type', 'company')->get();

        if ($companyUsers->isEmpty()) {
            $this->command->warn('No company users found. Please run UserSeeder first.');
            return;
        }

        $seedData = [
            ['seed_id' => 34, 'name' => '{"en":"Personal Status","ar":"أحوال شخصية"}', 'parent_id' => null],
            ['name' => '{"en":"General Classification","ar":"التصنيف العام"}', 'parent_id' => 34],
            ['name' => '{"en":"Endowment and Will Claims","ar":"دعاوى الأوقاف والوصايا"}', 'parent_id' => 34],
            ['name' => '{"en":"Custody, Visitation, and Alimony Claims","ar":"دعاوى الحضانة والزيارة والنفقة"}', 'parent_id' => 34],
            ['name' => '{"en":"Marriage and Separation Claims","ar":"دعاوى النكاح والفرقة"}', 'parent_id' => 34],
            ['name' => '{"en":"Guardianship Claims","ar":"دعاوى الولاية"}', 'parent_id' => 34],
            ['name' => '{"en":"Inheritance Division Claims","ar":"دعاوى قسمة التركات"}', 'parent_id' => 34],
            ['seed_id' => 41, 'name' => '{"en":"Commercial","ar":"تجاري"}', 'parent_id' => null],
            ['name' => '{"en":"Appeal","ar":"الاستئناف"}', 'parent_id' => 41],
            ['name' => '{"en":"Commercial Laws","ar":"الأنظمة التجارية"}', 'parent_id' => 41],
            ['name' => '{"en":"Companies","ar":"الشركات"}', 'parent_id' => 41],
            ['name' => '{"en":"Judicial Requests","ar":"الطلبات القضائيـة"}', 'parent_id' => 41],
            ['name' => '{"en":"Commercial Contracts","ar":"العقود التجارية"}', 'parent_id' => 41],
            ['name' => '{"en":"Intellectual Property","ar":"الملكية الفكرية"}', 'parent_id' => 41],
            ['name' => '{"en":"Urgent Cases","ar":"الدعاوى المستعجلـة"}', 'parent_id' => 41],
            ['seed_id' => 49, 'name' => '{"en":"Execution","ar":"تنفيذ"}', 'parent_id' => null],
            ['name' => '{"en":"Refusal to Accept Bond","ar":"الامتناع عن قبول السند"}', 'parent_id' => 49],
            ['name' => '{"en":"Insolvency or Solvency Claim","ar":"دعوى الإعسار أو الملاءة"}', 'parent_id' => 49],
            ['name' => '{"en":"Formal Disputes","ar":"منازعات شكلية"}', 'parent_id' => 49],
            ['name' => '{"en":"Substantive Disputes","ar":"منازعات غير شكلية"}', 'parent_id' => 49],
            ['seed_id' => 54, 'name' => '{"en":"Criminal","ar":"جزائية"}', 'parent_id' => null],
            ['name' => '{"en":"Private Right","ar":"الحق الخاص"}', 'parent_id' => 54],
            ['name' => '{"en":"Judicial Requests","ar":"الطلبات القضائية"}', 'parent_id' => 54],
            ['name' => '{"en":"Hudud (Islamic Penalties)","ar":"حدود"}', 'parent_id' => 54],
            ['name' => '{"en":"Retribution","ar":"قصاص"}', 'parent_id' => 54],
            ['name' => '{"en":"Financial Claim","ar":"مطالبة مالية"}', 'parent_id' => 54],
            ['seed_id' => 60, 'name' => '{"en":"General","ar":"عامة"}', 'parent_id' => null],
            ['name' => '{"en":"General Other","ar":"عامة أخرى"}', 'parent_id' => 60],
            ['name' => '{"en":"Urgent Cases","ar":"الدعاوى المستعجلة"}', 'parent_id' => 60],
            ['name' => '{"en":"Real Estate","ar":"عقارية"}', 'parent_id' => 60],
            ['name' => '{"en":"Financial","ar":"مالية"}', 'parent_id' => 60],
            ['name' => '{"en":"Traffic","ar":"مروري"}', 'parent_id' => 60],
            ['name' => '{"en":"Medical","ar":"طبي"}', 'parent_id' => 60],
            ['name' => '{"en":"Appeal","ar":"الاستئناف"}', 'parent_id' => 60],
            ['seed_id' => 68, 'name' => '{"en":"Labor","ar":"عمالية"}', 'parent_id' => null],
            ['name' => '{"en":"Incidental and Urgent Requests","ar":"الطلبات العارضة والعاجلة"}', 'parent_id' => 68],
            ['name' => '{"en":"Financial Rights","ar":"حقوق مالية"}', 'parent_id' => 68],
            ['name' => '{"en":"Employment Rights","ar":"حقوق وظيفية"}', 'parent_id' => 68],
            ['name' => '{"en":"Documents","ar":"مستندات ووثائق"}', 'parent_id' => 68],
            ['name' => '{"en":"Compensation Requests","ar":"طلبات تعويض"}', 'parent_id' => 68],
            ['name' => '{"en":"Appeal","ar":"الاستئناف"}', 'parent_id' => 68],
            ['seed_id' => 75, 'name' => '{"en":"Terminations","ar":"إنهاءات"}', 'parent_id' => null],
            ['name' => '{"en":"Guardianships","ar":"الولايات"}', 'parent_id' => 75],
            ['name' => '{"en":"Social Proofs","ar":"إثباتات اجتماعية"}', 'parent_id' => 75],
            ['name' => '{"en":"Endowments and Wills","ar":"الأوقاف والوصايا"}', 'parent_id' => 75],
            ['name' => '{"en":"Authorizations","ar":"الأذونات"}', 'parent_id' => 75],
            ['name' => '{"en":"Deed Amendments","ar":"تعديلات الصكوك"}', 'parent_id' => 75],
            ['name' => '{"en":"Endowments and Inheritance Terminations in Al-Ahsa and Al-Qatif","ar":"إنهاءات الأوقاف والمواريث في الأحساء والقطيف"}', 'parent_id' => 75],
        ];

        foreach ($companyUsers as $companyUser) {
            $parentIdMap = [];

            foreach ($seedData as $item) {
                if ($item['parent_id'] !== null) {
                    continue;
                }

                $name = json_decode($item['name'], true) ?? ['en' => $item['name']];
                $existing = CaseCategory::query()
                    ->where('tenant_id', $companyUser->tenant_id)
                    ->whereNull('parent_id')
                    ->where('name->en', $name['en'] ?? $item['name'])
                    ->first();

                $category = $existing ?: CaseCategory::create([
                    'name' => $name,
                    'parent_id' => null,
                    'tenant_id' => $companyUser->tenant_id,
                    'status' => 'active',
                ]);

                if (!empty($item['seed_id'])) {
                    $parentIdMap[$item['seed_id']] = $category->id;
                }
            }

            foreach ($seedData as $item) {
                if ($item['parent_id'] === null) {
                    continue;
                }

                $parentId = $parentIdMap[$item['parent_id']] ?? null;
                if (!$parentId) {
                    continue;
                }

                $name = json_decode($item['name'], true) ?? ['en' => $item['name']];
                $exists = CaseCategory::query()
                    ->where('tenant_id', $companyUser->tenant_id)
                    ->where('parent_id', $parentId)
                    ->where('name->en', $name['en'] ?? $item['name'])
                    ->exists();

                if (!$exists) {
                    CaseCategory::create([
                        'name' => $name,
                        'parent_id' => $parentId,
                        'tenant_id' => $companyUser->tenant_id,
                        'status' => 'active',
                    ]);
                }
            }

            $totalCategories = CaseCategory::where('tenant_id', $companyUser->tenant_id)->count();
            $this->command->info("Created {$totalCategories} case categories for company user: {$companyUser->name}");
        }
    }
}

