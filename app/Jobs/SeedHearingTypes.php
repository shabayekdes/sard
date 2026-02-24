<?php

namespace App\Jobs;

use App\Models\HearingType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to seed default hearing types for a company
 */
class SeedHearingTypes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 30;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $tenant_id
    ) {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get the current max ID to generate unique type_ids
        $maxId = HearingType::max('id') ?? 0;
        
        $hearingTypes = [
            [
                'type_id' => 'HT' . str_pad($maxId + 1, 6, '0', STR_PAD_LEFT),
                'name' => '{"en":"First Hearing","ar":"جلسة أولى"}',
                'description' => '{"en":"First hearing in a case to establish basic facts and procedures","ar":"أول جلسة يتم فيها نظر الدعوى، التحقق من أطرافها، تسجيل الطلبات، وتحديد مسار القضية."}',
                'duration_estimate' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_id' => 'HT' . str_pad($maxId + 2, 6, '0', STR_PAD_LEFT),
                'name' => '{"en":"Pleading Hearing","ar":"جلسة مرافعة"}',
                'description' => '{"en":"Hearing where each party presents their arguments and pleadings orally or in writing","ar":"جلسة يقدم فيها كل طرف دفوعه ومرافعاته شفهيًا أو كتابيًا."}',
                'duration_estimate' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_id' => 'HT' . str_pad($maxId + 3, 6, '0', STR_PAD_LEFT),
                'name' => '{"en":"Response Hearing","ar":"جلسة ردود"}',
                'description' => '{"en":"Hearing for responding to memorandums or arguments submitted by the other party","ar":"جلسة للرد على المذكرات أو الدفوع المقدمة من الطرف الآخر."}',
                'duration_estimate' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_id' => 'HT' . str_pad($maxId + 4, 6, '0', STR_PAD_LEFT),
                'name' => '{"en":"Evidence Hearing","ar":"جلسة إثبات"}',
                'description' => '{"en":"Hearing dedicated to presenting evidence such as documents, witnesses, or evidence","ar":"جلسة مخصصة لتقديم الأدلة مثل المستندات أو الشهود أو القرائن."}',
                'duration_estimate' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_id' => 'HT' . str_pad($maxId + 5, 6, '0', STR_PAD_LEFT),
                'name' => '{"en":"Witness Hearing","ar":"جلسة سماع شهود"}',
                'description' => '{"en":"Hearing where witness statements are heard and they are questioned by the judge or parties","ar":"يتم فيها سماع أقوال الشهود ومناقشتهم من القاضي أو الأطراف."}',
                'duration_estimate' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_id' => 'HT' . str_pad($maxId + 6, 6, '0', STR_PAD_LEFT),
                'name' => '{"en":"Expert Hearing","ar":"جلسة خبرة"}',
                'description' => '{"en":"Hearing to discuss the expert report or assign them a technical task (accounting, engineering, etc.)","ar":"جلسة لمناقشة تقرير الخبير أو تكليفه بمهمة فنية (محاسبية، هندسية، إلخ)."}',
                'duration_estimate' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_id' => 'HT' . str_pad($maxId + 7, 6, '0', STR_PAD_LEFT),
                'name' => '{"en":"Settlement Hearing","ar":"جلسة صلح"}',
                'description' => '{"en":"Aimed at attempting settlement between parties before continuing with the case","ar":"تهدف لمحاولة الصلح بين الأطراف قبل الاستمرار في نظر القضية."}',
                'duration_estimate' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_id' => 'HT' . str_pad($maxId + 8, 6, '0', STR_PAD_LEFT),
                'name' => '{"en":"Judgment Hearing","ar":"جلسة نطق بالحكم"}',
                'description' => '{"en":"The hearing where the judgment is issued or the date for pronouncing it is set","ar":"الجلسة التي يتم فيها إصدار الحكم أو تحديد موعد النطق به."}',
                'duration_estimate' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_id' => 'HT' . str_pad($maxId + 9, 6, '0', STR_PAD_LEFT),
                'name' => '{"en":"Follow-up Hearing","ar":"جلسة استكمال"}',
                'description' => '{"en":"Hearing to complete missing procedures such as submitting documents or additional responses","ar":"جلسة لاستكمال إجراءات ناقصة مثل تقديم مستندات أو ردود إضافية."}',
                'duration_estimate' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_id' => 'HT' . str_pad($maxId + 10, 6, '0', STR_PAD_LEFT),
                'name' => '{"en":"Administrative Hearing","ar":"جلسة إدارية"}',
                'description' => '{"en":"Procedural hearing to organize the file or correct data without entering into the subject matter","ar":"جلسة إجرائية لتنظيم الملف أو تصحيح بيانات دون الدخول في الموضوع."}',
                'duration_estimate' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_id' => 'HT' . str_pad($maxId + 11, 6, '0', STR_PAD_LEFT),
                'name' => '{"en":"Appeal Hearing","ar":"جلسة استئناف"}',
                'description' => '{"en":"Hearing to consider the appeal of the judgment before the Court of Appeal","ar":"جلسة لنظر الطعن في الحكم أمام محكمة الاستئناف."}',
                'duration_estimate' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_id' => 'HT' . str_pad($maxId + 12, 6, '0', STR_PAD_LEFT),
                'name' => '{"en":"Review Hearing","ar":"جلسة تدقيق"}',
                'description' => '{"en":"Reviewing the case without pleading (often in appeal)","ar":"نظر القضية تدقيقًا دون مرافعة (غالبًا في الاستئناف)."}',
                'duration_estimate' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_id' => 'HT' . str_pad($maxId + 13, 6, '0', STR_PAD_LEFT),
                'name' => '{"en":"Enforcement Hearing","ar":"جلسة تنفيذ"}',
                'description' => '{"en":"Hearing related to judgment enforcement procedures (service suspension, seizure, etc.)","ar":"جلسة متعلقة بإجراءات تنفيذ الحكم (إيقاف خدمات، حجز، إلخ)."}',
                'duration_estimate' => 60,
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        HearingType::insert($hearingTypes);

        Log::info("SeedHearingTypes: Completed", [
            'company_id' => $this->tenant_id,
            'created' => count($hearingTypes)
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SeedHearingTypes: Job failed", [
            'company_id' => $this->tenant_id,
            'error' => $exception->getMessage()
        ]);
    }
}

