<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Main job to seed all default data for a new company
 * This job dispatches individual jobs for each data type
 */
class SeedDefaultCompanyData implements ShouldQueue
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
    public $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Tenant $tenant
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Dispatch individual jobs for each data type
        // This allows for better error handling and parallel processing
        // Jobs will run in parallel if queue workers are available
        
        $tenant = $this->tenant;

        // Helper to dispatch without stopping others if one fails (e.g. with sync queue)
        $dispatch = function (string $jobClass) use ($tenant): void {
            try {
                $jobClass::dispatch($tenant);
            } catch (\Throwable $e) {
                $this->safeLog('error', "SeedDefaultCompanyData: Failed to dispatch or run {$jobClass}", [
                    'tenant_id' => $tenant->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        };

        // Foundation jobs - should run first (settings, roles, notifications)
        $dispatch(SeedCompanyRoles::class);
        $dispatch(SeedNotificationTemplates::class);

        // Data seeding jobs
        $dispatch(SeedExpenseCategories::class);
        $dispatch(SeedClientTypes::class);
        $dispatch(SeedCaseTypes::class);
        $dispatch(SeedCaseCategories::class);
        $dispatch(SeedDocumentTypes::class);
        $dispatch(SeedDocumentCategories::class);
        $dispatch(SeedTaskTypes::class);
        $dispatch(SeedResearchTypes::class);
        $dispatch(SeedCourtTypes::class);
        $dispatch(SeedCircleTypes::class);
        $dispatch(SeedCaseStatuses::class);
        $dispatch(SeedTaskStatuses::class);
        $dispatch(SeedHearingTypes::class);
        $dispatch(SeedEventTypes::class);
        $dispatch(SeedResearchSources::class);

        // ============================================
        // TO ADD NEW DATA TYPE:
        // ============================================
        // 1. Create a new job file (e.g., SeedCourtTypes.php)
        // 2. Add the dispatch call below:
        //    SeedCourtTypes::dispatch($this->tenant_id);
        // 3. That's it! The job will run automatically when a company is created
        // ============================================
        
        // Example additions (uncomment when ready):
        // SeedCourtTypes::dispatch($this->tenant_id);
        // SeedHearingTypes::dispatch($this->tenant_id);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->safeLog('error', "SeedDefaultCompanyData: Job failed", [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }

    /**
     * Log without throwing if the log stream is not writable (e.g. permission denied).
     */
    private function safeLog(string $level, string $message, array $context = []): void
    {
        try {
            Log::log($level, $message, $context);
        } catch (\Throwable $e) {
            // Ignore logging failures so they don't crash the job
        }
    }
}

