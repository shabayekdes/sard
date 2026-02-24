<?php

namespace App\Jobs;

use App\Models\TaskStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to seed default task statuses for a company
 */
class SeedTaskStatuses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = 30;

    public function __construct(
        public string $tenant_id
    ) {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        $now = now();
        $defaultStatuses = [
            [
                'name' => '{"en":"Not Started","ar":"لم تبدأ"}',
                'color' => '#6B7280',
                'is_completed' => false,
                'tenant_id' => $this->tenant_id,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => '{"en":"In Progress","ar":"قيد التنفيذ"}',
                'color' => '#3B82F6',
                'is_completed' => false,
                'tenant_id' => $this->tenant_id,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => '{"en":"Awaiting Information","ar":"بانتظار معلومات"}',
                'color' => '#8B5CF6',
                'is_completed' => false,
                'tenant_id' => $this->tenant_id,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => '{"en":"Pending Approval","ar":"بانتظار اعتماد"}',
                'color' => '#8B5CF6',
                'is_completed' => false,
                'tenant_id' => $this->tenant_id,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => '{"en":"Under Review","ar":"قيد المراجعة"}',
                'color' => '#F59E0B',
                'is_completed' => false,
                'tenant_id' => $this->tenant_id,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => '{"en":"On Hold","ar":"متوقفة مؤقتًا"}',
                'color' => '#EF4444',
                'is_completed' => false,
                'tenant_id' => $this->tenant_id,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => '{"en":"Blocked","ar":"معطّلة"}',
                'color' => '#EC4899',
                'is_completed' => false,
                'tenant_id' => $this->tenant_id,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => '{"en":"Deferred","ar":"مؤجلة"}',
                'color' => '#F97316',
                'is_completed' => false,
                'tenant_id' => $this->tenant_id,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => '{"en":"Completed","ar":"مكتملة"}',
                'color' => '#10B981',
                'is_completed' => true,
                'tenant_id' => $this->tenant_id,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => '{"en":"Cancelled","ar":"ملغاة"}',
                'color' => '#DC2626',
                'is_completed' => false,
                'tenant_id' => $this->tenant_id,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => '{"en":"Archived","ar":"مؤرشفة"}',
                'color' => '#6B7280',
                'is_completed' => true,
                'tenant_id' => $this->tenant_id,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ];

        TaskStatus::insert($defaultStatuses);

        Log::info('SeedTaskStatuses: Completed', [
            'company_id' => $this->tenant_id,
            'count' => count($defaultStatuses),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SeedTaskStatuses: Job failed', [
            'company_id' => $this->tenant_id,
            'error' => $exception->getMessage(),
        ]);
    }
}
