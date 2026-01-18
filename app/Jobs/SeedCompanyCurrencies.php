<?php

namespace App\Jobs;

use App\Models\Currency;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to seed default currencies for a company
 */
class SeedCompanyCurrencies implements ShouldQueue
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
        $availableCurrencies = config('currencies.available_currencies', []);
        if (empty($availableCurrencies)) {
            Log::warning('SeedCompanyCurrencies: No currencies configured', [
                'company_id' => $this->companyUserId,
            ]);
            return;
        }

        $now = now();
        $companyCurrencies = [];

        foreach ($availableCurrencies as $currency) {
            $currency['created_by'] = $this->companyUserId;
            $currency['created_at'] = $now;
            $currency['updated_at'] = $now;
            $companyCurrencies[] = $currency;
        }

        Currency::insert($companyCurrencies);

        Log::info('SeedCompanyCurrencies: Completed', [
            'company_id' => $this->companyUserId,
            'created' => count($companyCurrencies),
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SeedCompanyCurrencies: Job failed', [
            'company_id' => $this->companyUserId,
            'error' => $exception->getMessage(),
        ]);
    }
}
