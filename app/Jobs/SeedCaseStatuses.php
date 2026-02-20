<?php

namespace App\Jobs;

use App\Models\CaseStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to seed default case statuses for a company
 */
class SeedCaseStatuses implements ShouldQueue
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
        public int $companyUserId
    ) {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Check if case statuses already exist for this company
        if (CaseStatus::where('created_by', $this->companyUserId)->exists()) {
            Log::info("SeedCaseStatuses: Case statuses already exist, skipping", [
                'company_id' => $this->companyUserId
            ]);
            return;
        }

        $caseStatuses = [
            [
                'name' => '{"ar":"مسودة","en":"Draft"}',
                'description' => '{"ar":"تم إنشاء القضية ولم يتم رفعها رسميًا بعد","en":"Case created but not yet officially filed"}',
                'color' => '#6B7280',
                'is_default' => true,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"مقيدة","en":"Filed / Registered"}',
                'description' => '{"ar":"تم قيد الدعوى رسميًا لدى المحكمة","en":"Case officially registered before the court"}',
                'color' => '#3B82F6',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"جلسة جديدة","en":"New Session Scheduled"}',
                'description' => '{"ar":"تم تحديد جلسة جديدة للقضية","en":"A new session has been scheduled"}',
                'color' => '#8B5CF6',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"جاري العمل","en":"In Progress"}',
                'description' => '{"ar":"القضية قيد المتابعة والعمل الداخلي","en":"Case is actively being handled"}',
                'color' => '#3B82F6',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"مرافعة","en":"Under Pleading"}',
                'description' => '{"ar":"تبادل المذكرات والمرافعات قائم بين الأطراف","en":"Pleadings and submissions are ongoing"}',
                'color' => '#6366F1',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"بانتظار إجراء","en":"Pending Action"}',
                'description' => '{"ar":"بانتظار إجراء من المحكمة أو الخصم أو العميل","en":"Awaiting action from court, opponent, or client"}',
                'color' => '#F59E0B',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"قيد المراجعة","en":"Under Review"}',
                'description' => '{"ar":"الحكم أو الإجراء قيد المراجعة الداخلية أو القضائية","en":"Judgment or action under internal or judicial review"}',
                'color' => '#F59E0B',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"مؤجلة","en":"Postponed"}',
                'description' => '{"ar":"تم تأجيل الجلسة إلى موعد لاحق","en":"Hearing postponed to a later date"}',
                'color' => '#F97316',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"موقوفة","en":"Suspended"}',
                'description' => '{"ar":"تم إيقاف القضية مؤقتًا بقرار قضائي أو نظامي","en":"Case temporarily suspended by legal order"}',
                'color' => '#EF4444',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"مشطوبة","en":"Dismissed (Struck Off)"}',
                'description' => '{"ar":"شُطبت الدعوى لسبب إجرائي مثل عدم الحضور","en":"Case struck off due to procedural reasons"}',
                'color' => '#DC2626',
                'is_default' => false,
                'is_closed' => true,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"طلب اعتراض","en":"Appeal Filed"}',
                'description' => '{"ar":"تم تقديم اعتراض على الحكم الابتدائي","en":"Appeal filed against first-instance judgment"}',
                'color' => '#F97316',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"طلب نقض","en":"Cassation Filed"}',
                'description' => '{"ar":"تم رفع طلب نقض أمام المحكمة العليا","en":"Cassation filed before the Supreme Court"}',
                'color' => '#EA580C',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"طلب التماس إعادة النظر","en":"Reconsideration Filed"}',
                'description' => '{"ar":"تم تقديم التماس إعادة نظر أمام ذات الدائرة","en":"Reconsideration request filed before same court"}',
                'color' => '#D97706',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"محكوم بها","en":"Judgment Issued"}',
                'description' => '{"ar":"صدر حكم في القضية","en":"Judgment has been issued in the case"}',
                'color' => '#059669',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"قيد التنفيذ","en":"Under Enforcement"}',
                'description' => '{"ar":"صدر حكم وجارٍ اتخاذ إجراءات التنفيذ","en":"Judgment issued and enforcement in progress"}',
                'color' => '#10B981',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"تم التنفيذ","en":"Executed"}',
                'description' => '{"ar":"تم تنفيذ الحكم بالكامل","en":"Judgment fully executed"}',
                'color' => '#059669',
                'is_default' => false,
                'is_closed' => true,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"مرفوضة","en":"Rejected"}',
                'description' => '{"ar":"صدر حكم برفض الدعوى","en":"Case dismissed by court judgment"}',
                'color' => '#DC2626',
                'is_default' => false,
                'is_closed' => true,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"متنازل عنها","en":"Withdrawn"}',
                'description' => '{"ar":"تم التنازل عن الدعوى من قبل المدعي","en":"Case withdrawn by claimant"}',
                'color' => '#84CC16',
                'is_default' => false,
                'is_closed' => true,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ar":"مغلقة","en":"Closed"}',
                'description' => '{"ar":"تم إغلاق القضية إداريًا بعد اكتمال الإجراءات","en":"Case administratively closed after completion"}',
                'color' => '#6B7280',
                'is_default' => false,
                'is_closed' => true,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        CaseStatus::insert($caseStatuses);

        Log::info("SeedCaseStatuses: Completed", [
            'company_id' => $this->companyUserId,
            'created' => count($caseStatuses)
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SeedCaseStatuses: Job failed", [
            'company_id' => $this->companyUserId,
            'error' => $exception->getMessage()
        ]);
    }
}

