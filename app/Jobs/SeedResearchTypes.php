<?php

namespace App\Jobs;

use App\Models\ResearchType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to seed default research types for a company
 */
class SeedResearchTypes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = 30;

    public function __construct(
        public int $companyUserId
    ) {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        $researchTypes = [
            ['code' => 'RT01', 'name' => ['ar' => 'بحث قانوني عام', 'en' => 'General Legal Research'], 'description' => ['ar' => 'بحث في الأنظمة واللوائح والمواد النظامية وتحليلها لدعم قضية أو استشارة', 'en' => 'Research on laws, regulations, and legal provisions to support a case or opinion']],
            ['code' => 'RT02', 'name' => ['ar' => 'تحليل السوابق القضائية', 'en' => 'Case Law & Precedent Analysis'], 'description' => ['ar' => 'دراسة الأحكام السابقة والاجتهادات القضائية وتحليل اتجاهات القضاء', 'en' => 'Analysis of court precedents, judgments, and judicial trends']],
            ['code' => 'RT03', 'name' => ['ar' => 'بحث فقهي ونظامي', 'en' => 'Jurisprudential & Statutory Research'], 'description' => ['ar' => 'دراسة الآراء الفقهية وتفسير النصوص النظامية في القضايا ذات الطابع الشرعي', 'en' => 'Study of jurisprudential opinions and statutory interpretation']],
            ['code' => 'RT04', 'name' => ['ar' => 'بحث مقارن', 'en' => 'Comparative Law Research'], 'description' => ['ar' => 'مقارنة الأنظمة السعودية بأنظمة دول أخرى أو مقارنة آراء فقهية مختلفة', 'en' => 'Comparative analysis between Saudi law and other legal systems']],
            ['code' => 'RT05', 'name' => ['ar' => 'بحث امتثال وتنظيم', 'en' => 'Regulatory & Compliance Research'], 'description' => ['ar' => 'تحليل متطلبات الامتثال للأنظمة واللوائح التنظيمية', 'en' => 'Regulatory compliance and legal requirements analysis']],
            ['code' => 'RT06', 'name' => ['ar' => 'بحث العناية القانونية الواجبة', 'en' => 'Legal Due Diligence'], 'description' => ['ar' => 'فحص وتحليل الوضع القانوني لشركة أو صفقة قبل الإتمام', 'en' => 'Legal review and investigation prior to transactions']],
            ['code' => 'RT07', 'name' => ['ar' => 'بحث دستوري وإداري', 'en' => 'Constitutional & Administrative Research'], 'description' => ['ar' => 'دراسة الأنظمة الأساسية، مبادئ المشروعية، والطعن في القرارات الإدارية', 'en' => 'Research on constitutional principles and administrative legality']],
            ['code' => 'RT08', 'name' => ['ar' => 'بحث جنائي', 'en' => 'Criminal Law Research'], 'description' => ['ar' => 'دراسة النصوص الجزائية والعقوبات وتحليل أركان الجرائم', 'en' => 'Research on criminal law provisions and elements of crimes']],
            ['code' => 'RT09', 'name' => ['ar' => 'بحث عمالي', 'en' => 'Labor Law Research'], 'description' => ['ar' => 'تحليل أنظمة العمل واللوائح التنفيذية وقضايا الفصل والمطالبات', 'en' => 'Research on labor law regulations and employment disputes']],
            ['code' => 'RT10', 'name' => ['ar' => 'بحث تجاري وشركات', 'en' => 'Commercial & Corporate Research'], 'description' => ['ar' => 'دراسة أنظمة الشركات، العقود التجارية، والإفلاس', 'en' => 'Research on corporate law, commercial contracts, and insolvency']],
            ['code' => 'RT11', 'name' => ['ar' => 'بحث تنفيذي', 'en' => 'Enforcement Law Research'], 'description' => ['ar' => 'دراسة نظام التنفيذ وإجراءاته والدفوع المتعلقة بالتنفيذ', 'en' => 'Research on enforcement procedures and objections']],
            ['code' => 'RT12', 'name' => ['ar' => 'بحث ملكية فكرية', 'en' => 'Intellectual Property Research'], 'description' => ['ar' => 'بحث في حقوق المؤلف، العلامات التجارية، وبراءات الاختراع', 'en' => 'Research on IP law including trademarks and patents']],
            ['code' => 'RT13', 'name' => ['ar' => 'بحث قانون دولي', 'en' => 'International Law Research'], 'description' => ['ar' => 'دراسة الاتفاقيات الدولية، التحكيم الدولي، والعلاقات العابرة للحدود', 'en' => 'Research on international treaties and cross-border matters']],
            ['code' => 'RT14', 'name' => ['ar' => 'إعداد رأي قانوني', 'en' => 'Legal Opinion Preparation'], 'description' => ['ar' => 'إعداد مذكرة رأي قانوني مبنية على بحث وتحليل شامل', 'en' => 'Drafting formal legal opinion based on research']],
            ['code' => 'RT15', 'name' => ['ar' => 'بحث تشريعي', 'en' => 'Legislative Analysis'], 'description' => ['ar' => 'دراسة التعديلات النظامية والمشاريع التشريعية الجديدة', 'en' => 'Analysis of new or amended legislation']],
        ];

        $existingCodes = ResearchType::where('created_by', $this->companyUserId)->pluck('code')->flip()->all();

        $toInsert = [];
        foreach ($researchTypes as $row) {
            $code = $row['code'];
            if (isset($existingCodes[$code])) {
                continue;
            }
            $toInsert[] = [
                'code' => $code,
                'name' => json_encode($row['name']),
                'description' => json_encode($row['description']),
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $existingCodes[$code] = true;
        }

        if (! empty($toInsert)) {
            ResearchType::insert($toInsert);
        }

        Log::info('SeedResearchTypes: Completed', [
            'company_id' => $this->companyUserId,
            'created' => count($toInsert),
            'total' => count($researchTypes),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SeedResearchTypes: Job failed', [
            'company_id' => $this->companyUserId,
            'error' => $exception->getMessage(),
        ]);
    }
}
