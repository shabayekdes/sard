<?php

namespace App\Jobs;

use App\Models\User;
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
        public int $companyUserId
    ) {
        // Set queue name if needed
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $companyUser = User::find($this->companyUserId);

        if (!$companyUser || $companyUser->type !== 'company') {
            Log::warning("SeedDefaultCompanyData: Company user not found or invalid", [
                'user_id' => $this->companyUserId
            ]);
            return;
        }

        Log::info("SeedDefaultCompanyData: Starting to seed default data for company", [
            'company_id' => $this->companyUserId
        ]);

        // Dispatch individual jobs for each data type
        // This allows for better error handling and parallel processing
        // Jobs will run in parallel if queue workers are available
        
        // Foundation jobs - should run first (settings, roles, notifications)
        SeedCompanySettings::dispatch($this->companyUserId);
        SeedCompanyRoles::dispatch($this->companyUserId);
        SeedNotificationTemplates::dispatch($this->companyUserId);
        
        // Data seeding jobs
        SeedExpenseCategories::dispatch($this->companyUserId);
        SeedClientTypes::dispatch($this->companyUserId);
        SeedCaseTypes::dispatch($this->companyUserId);
        SeedCaseCategories::dispatch($this->companyUserId);
        SeedDocumentTypes::dispatch($this->companyUserId);
        SeedDocumentCategories::dispatch($this->companyUserId);
        SeedTaskTypes::dispatch($this->companyUserId);
        SeedResearchTypes::dispatch($this->companyUserId);
        SeedCourtTypes::dispatch($this->companyUserId);
        SeedCircleTypes::dispatch($this->companyUserId);
        SeedCaseStatuses::dispatch($this->companyUserId);
        SeedTaskStatuses::dispatch($this->companyUserId);
        SeedHearingTypes::dispatch($this->companyUserId);
        SeedEventTypes::dispatch($this->companyUserId);
        SeedResearchSources::dispatch($this->companyUserId);

        // ============================================
        // TO ADD NEW DATA TYPE:
        // ============================================
        // 1. Create a new job file (e.g., SeedCourtTypes.php)
        // 2. Add the dispatch call below:
        //    SeedCourtTypes::dispatch($this->companyUserId);
        // 3. That's it! The job will run automatically when a company is created
        // ============================================
        
        // Example additions (uncomment when ready):
        // SeedCourtTypes::dispatch($this->companyUserId);
        // SeedHearingTypes::dispatch($this->companyUserId);

        Log::info("SeedDefaultCompanyData: All seeding jobs dispatched", [
            'company_id' => $this->companyUserId
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SeedDefaultCompanyData: Job failed", [
            'company_id' => $this->companyUserId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}

