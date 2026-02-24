<?php

namespace App\Jobs;

use App\Models\Tenant;
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
        public string $tenant_id
    ) {
        // Set queue name if needed
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tenant = Tenant::find($this->tenant_id);

        if (!$tenant) {
            Log::warning("SeedDefaultCompanyData: Company user not found or invalid", [
                'user_id' => $this->tenant_id
            ]);
            return;
        }

        Log::info("SeedDefaultCompanyData: Starting to seed default data for company", [
            'company_id' => $this->tenant_id
        ]);

        // Dispatch individual jobs for each data type
        // This allows for better error handling and parallel processing
        // Jobs will run in parallel if queue workers are available
        
        // Foundation jobs - should run first (settings, roles, notifications)
        SeedCompanySettings::dispatch($this->tenant_id);
        SeedCompanyRoles::dispatch($this->tenant_id);
        SeedNotificationTemplates::dispatch($this->tenant_id);
        
        // Data seeding jobs
        SeedExpenseCategories::dispatch($this->tenant_id);
        SeedClientTypes::dispatch($this->tenant_id);
        SeedCaseTypes::dispatch($this->tenant_id);
        SeedCaseCategories::dispatch($this->tenant_id);
        SeedDocumentTypes::dispatch($this->tenant_id);
        SeedDocumentCategories::dispatch($this->tenant_id);
        SeedTaskTypes::dispatch($this->tenant_id);
        SeedResearchTypes::dispatch($this->tenant_id);
        SeedCourtTypes::dispatch($this->tenant_id);
        SeedCircleTypes::dispatch($this->tenant_id);
        SeedCaseStatuses::dispatch($this->tenant_id);
        SeedTaskStatuses::dispatch($this->tenant_id);
        SeedHearingTypes::dispatch($this->tenant_id);
        SeedEventTypes::dispatch($this->tenant_id);
        SeedResearchSources::dispatch($this->tenant_id);

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

        Log::info("SeedDefaultCompanyData: All seeding jobs dispatched", [
            'company_id' => $this->tenant_id
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SeedDefaultCompanyData: Job failed", [
            'company_id' => $this->tenant_id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}

