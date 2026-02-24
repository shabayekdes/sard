<?php

namespace App\Jobs;

use App\Models\ExpenseCategory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to seed default expense categories for a company
 */
class SeedExpenseCategories implements ShouldQueue
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
        public string $tenant_id
    ) {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $expenseCategories = [
            [
                'name' => '{"en":"Office Rent","ar":"إيجار المكتب"}',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Utilities","ar":"فواتير خدمات"}',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Office Supplies","ar":"قرطاسية ومستلزمات مكتبية"}',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Maintenance & Cleaning","ar":"صيانة وتنظيف"}',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Salaries","ar":"رواتب الموظفين"}',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Allowances & Incentives","ar":"بدلات وحوافز"}',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Court Fees","ar":"رسوم محاكم"}',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Case Fees","ar":"رسوم قضايا"}',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Transportation","ar":"مواصلات"}',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Software Subscriptions","ar":"اشتراكات برامج"}',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"en":"Miscellaneous Expenses","ar":"مصاريف متفرقة"}',
                'status' => 'active',
                'tenant_id' => $this->tenant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        ExpenseCategory::insert($expenseCategories);

        Log::info("SeedExpenseCategories: Completed", [
            'company_id' => $this->tenant_id,
            'created' => count($expenseCategories)
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SeedExpenseCategories: Job failed", [
            'company_id' => $this->tenant_id,
            'error' => $exception->getMessage()
        ]);
    }
}

