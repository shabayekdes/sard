<?php

namespace App\Jobs;

use App\Models\CourtType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to seed default court types for a company
 */
class SeedCourtTypes implements ShouldQueue
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
        $courtTypes = [
            [
                'name' => '{"en":"General Court","ar":"المحكمة العامة"}',
                'color' => '#10B981',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Criminal Court","ar":"المحكمة الجزائية"}',
                'color' => '#EF4444',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Personal Status Court","ar":"محكمة الأحوال الشخصية"}',
                'color' => '#8B5CF6',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Execution Court","ar":"المحكمة التنفيذ"}',
                'color' => '#F59E0B',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Court of Appeal","ar":"محكمة الإستئناف"}',
                'color' => '#DC2626',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Notary Public","ar":"كتابة العدل"}',
                'color' => '#3B82F6',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Criminal Court","ar":"محكمة الجزائية"}',
                'color' => '#059669',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Auditing Authority (Endowments and Inheritance Division)","ar":"هيئة التدقيق (لدائرة الأوقاف والمواريث)"}',
                'color' => '#F97316',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Execution Court","ar":"محكمة التنفيذ"}',
                'color' => '#84CC16',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Commercial Court","ar":"المحكمة التجارية"}',
                'color' => '#06B6D4',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Seizure and Execution Court","ar":"محكمة للحجز والتنفيذ"}',
                'color' => '#6B7280',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        CourtType::insert($courtTypes);

        Log::info("SeedCourtTypes: Completed", [
            'company_id' => $this->companyUserId,
            'created' => count($courtTypes)
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SeedCourtTypes: Job failed", [
            'company_id' => $this->companyUserId,
            'error' => $exception->getMessage()
        ]);
    }
}

