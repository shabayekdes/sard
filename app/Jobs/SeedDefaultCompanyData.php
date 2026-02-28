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
        public Tenant $tenant
    ) {
        // Set queue name if needed
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Dispatch individual jobs for each data type
        // This allows for better error handling and parallel processing
        // Jobs will run in parallel if queue workers are available
        
        // Foundation jobs - should run first (settings, roles, notifications)
        // SeedCompanySettings::dispatch($this->tenant_id);
        SeedCompanyRoles::dispatch($this->tenant->id);
        SeedNotificationTemplates::dispatch($this->tenant->id);
        
        // Data seeding jobs
        SeedExpenseCategories::dispatch($this->tenant->id);
        SeedClientTypes::dispatch($this->tenant->id);
        SeedCaseTypes::dispatch($this->tenant->id);
        SeedCaseCategories::dispatch($this->tenant->id);
        SeedDocumentTypes::dispatch($this->tenant->id);
        SeedDocumentCategories::dispatch($this->tenant->id);
        SeedTaskTypes::dispatch($this->tenant->id);
        SeedResearchTypes::dispatch($this->tenant->id);
        SeedCourtTypes::dispatch($this->tenant->id);
        SeedCircleTypes::dispatch($this->tenant->id);
        SeedCaseStatuses::dispatch($this->tenant->id);
        SeedTaskStatuses::dispatch($this->tenant->id);
        SeedHearingTypes::dispatch($this->tenant->id);
        SeedEventTypes::dispatch($this->tenant->id);
        SeedResearchSources::dispatch($this->tenant->id);

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

