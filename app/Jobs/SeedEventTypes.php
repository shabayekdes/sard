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
                'name' => 'Milestone',
                'description' => 'Important project milestones',
                'color' => '#10B981',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hearing',
                'description' => 'Court hearings and proceedings',
                'color' => '#EF4444',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Deadline',
                'description' => 'Important deadlines',
                'color' => '#F59E0B',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Meeting',
                'description' => 'Client and team meetings',
                'color' => '#3B82F6',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Filing',
                'description' => 'Document filing events',
                'color' => '#8B5CF6',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Review',
                'description' => 'Case review sessions',
                'color' => '#6B7280',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Consultation',
                'description' => 'Client consultation sessions',
                'color' => '#059669',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Settlement',
                'description' => 'Settlement negotiations',
                'color' => '#DC2626',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Deposition',
                'description' => 'Witness depositions',
                'color' => '#F97316',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Trial',
                'description' => 'Court trial proceedings',
                'color' => '#84CC16',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Discovery',
                'description' => 'Discovery phase events',
                'color' => '#06B6D4',
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

