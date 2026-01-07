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
        $caseStatuses = [
            [
                'name' => 'New',
                'description' => 'Newly created case',
                'color' => '#6B7280',
                'is_default' => true,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'In Progress',
                'description' => 'Case is being worked on',
                'color' => '#3B82F6',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Under Review',
                'description' => 'Case under review',
                'color' => '#F59E0B',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'On Hold',
                'description' => 'Case temporarily paused',
                'color' => '#EF4444',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Completed',
                'description' => 'Case successfully completed',
                'color' => '#10B981',
                'is_default' => false,
                'is_closed' => true,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Closed',
                'description' => 'Case closed',
                'color' => '#6B7280',
                'is_default' => false,
                'is_closed' => true,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Pending',
                'description' => 'Case pending further action',
                'color' => '#8B5CF6',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Settled',
                'description' => 'Case settled out of court',
                'color' => '#059669',
                'is_default' => false,
                'is_closed' => true,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dismissed',
                'description' => 'Case dismissed by court',
                'color' => '#DC2626',
                'is_default' => false,
                'is_closed' => true,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Appealed',
                'description' => 'Case under appeal',
                'color' => '#F97316',
                'is_default' => false,
                'is_closed' => false,
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Withdrawn',
                'description' => 'Case withdrawn by client',
                'color' => '#84CC16',
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

