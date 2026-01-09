<?php

namespace App\Jobs;

use App\Models\EventType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to seed default event types for a company
 */
class SeedEventTypes implements ShouldQueue
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
        $eventTypes = [
            [
                'name' => '{"en":"Case Registration","ar":"تسجيل قضية"}',
                'description' => '{"en":"Creating and registering the case officially","ar":"إنشاء وقيد القضية رسميًا"}',
                'color' => '#10B981',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Case Filing","ar":"قيد الدعوى"}',
                'description' => '{"en":"Accepting the case and giving it an official number","ar":"قبول الدعوى وإعطاؤها رقم رسمي"}',
                'color' => '#EF4444',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Session Scheduled","ar":"تحديد جلسة"}',
                'description' => '{"en":"Setting a date for a court session","ar":"تحديد موعد جلسة قضائية"}',
                'color' => '#3B82F6',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Session Held","ar":"انعقاد جلسة"}',
                'description' => '{"en":"Holding the session","ar":"عقد الجلسة"}',
                'color' => '#8B5CF6',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Deadline","ar":"موعد نهائي"}',
                'description' => '{"en":"Mandatory date for submitting an action (memorandum / document)","ar":"تاريخ إلزامي لتقديم إجراء (مذكرة / مستند)"}',
                'color' => '#F59E0B',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Memorandum Submitted","ar":"تقديم مذكرة"}',
                'description' => '{"en":"Submitting a memorandum or response","ar":"تقديم مذكرة أو رد"}',
                'color' => '#6B7280',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Judgment Issued","ar":"نطق بالحكم"}',
                'description' => '{"en":"Issuing the judgment","ar":"صدور الحكم"}',
                'color' => '#DC2626',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Meeting","ar":"اجتماع"}',
                'description' => '{"en":"Internal meeting or with the client regarding the case","ar":"اجتماع داخلي أو مع العميل بخصوص القضية"}',
                'color' => '#059669',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        EventType::insert($eventTypes);

        Log::info("SeedEventTypes: Completed", [
            'company_id' => $this->companyUserId,
            'created' => count($eventTypes)
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SeedEventTypes: Job failed", [
            'company_id' => $this->companyUserId,
            'error' => $exception->getMessage()
        ]);
    }
}

