<?php

namespace App\Jobs;

use App\Models\CaseType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to seed default case types for a company
 */
class SeedCaseTypes implements ShouldQueue
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
        $caseTypes = [
            [
                'name' => '{"en":"Case Registration","ar":"تسجيل قضية"}',
                'description' => '{"en":"Formally creating and registering the case","ar":"إنشاء وقيد القضية رسميًا"}',
                'color' => '#3B82F6',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Case Filing","ar":"قيد الدعوى"}',
                'description' => '{"en":"Accepting the case and assigning it an official number","ar":"قبول الدعوى وإعطاؤها رقم رسمي"}',
                'color' => '#10B981',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Session Scheduled","ar":"تحديد جلسة"}',
                'description' => '{"en":"Setting a date for a court session","ar":"تحديد موعد جلسة قضائية"}',
                'color' => '#F59E0B',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Session Held","ar":"انعقاد جلسة"}',
                'description' => '{"en":"Holding the session","ar":"عقد الجلسة"}',
                'color' => '#8B5CF6',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Deadline","ar":"موعد نهائي"}',
                'description' => '{"en":"Mandatory date for submitting an action (memorandum / document)","ar":"تاريخ إلزامي لتقديم إجراء (مذكرة / مستند)"}',
                'color' => '#EF4444',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Memorandum Submitted","ar":"تقديم مذكرة"}',
                'description' => '{"en":"Submitting a memorandum or response","ar":"تقديم مذكرة أو رد"}',
                'color' => '#06B6D4',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Judgment Issued","ar":"نطق بالحكم"}',
                'description' => '{"en":"Issuing the judgment","ar":"صدور الحكم"}',
                'color' => '#059669',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Meeting","ar":"اجتماع"}',
                'description' => '{"en":"Internal meeting or with the client regarding the case","ar":"اجتماع داخلي أو مع العميل بخصوص القضية"}',
                'color' => '#84CC16',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        CaseType::insert($caseTypes);

        Log::info("SeedCaseTypes: Completed", [
            'company_id' => $this->tenant_id,
            'created' => count($caseTypes)
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SeedCaseTypes: Job failed", [
            'company_id' => $this->tenant_id,
            'error' => $exception->getMessage()
        ]);
    }
}

