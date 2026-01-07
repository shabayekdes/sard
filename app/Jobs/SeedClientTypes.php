<?php

namespace App\Jobs;

use App\Models\ClientType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to seed default client types for a company
 */
class SeedClientTypes implements ShouldQueue
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
        $clientTypes = [
            [
                'name' => '{"en":"Individual","ar":"فرد"}',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Sole Proprietorship","ar":"مؤسسة فردية"}',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Simple Partnership","ar":"شركة توصية بسيطة"}',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Partnership","ar":"شركة تضامنية"}',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Limited Liability Company","ar":"شركة ذات مسئولية محدودة"}',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Public Joint Stock Company","ar":"شركة مساهمة عامة"}',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Foreign Company","ar":"شركة أجنبية"}',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Gulf Company","ar":"شركة خليجية"}',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Closed Joint Stock Company","ar":"شركة مساهمة مقفلة"}',
                'status' => 'active',
                'created_by' => $this->companyUserId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        ClientType::insert($clientTypes);

        Log::info("SeedClientTypes: Completed", [
            'company_id' => $this->companyUserId,
            'created' => count($clientTypes)
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SeedClientTypes: Job failed", [
            'company_id' => $this->companyUserId,
            'error' => $exception->getMessage()
        ]);
    }
}

