<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to create default notification templates for a company
 */
class SeedNotificationTemplates implements ShouldQueue
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
        public \App\Models\Tenant $tenant
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->createDefaultNotificationSettings();

        $this->safeLog('info', "SeedNotificationTemplates: Completed", [
            'company_id' => $this->tenant->id
        ]);
    }

    /**
     * Create default notification settings for the company
     */
    private function createDefaultNotificationSettings(): void
    {
        $templates = \App\Models\NotificationTemplate::all();
        $types = ['email', 'twilio', 'slack'];

        foreach ($templates as $template) {
            foreach ($types as $type) {
                \App\Models\TenantNotificationTemplate::updateOrCreate(
                    [
                        'tenant_id' => $this->tenant->id,
                        'template_id' => $template->id,
                        'type' => $type
                    ],
                    ['status' => 'inactive'] // Default to disabled
                );
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $this->safeLog('error', "SeedNotificationTemplates: Job failed", [
            'company_id' => $this->tenant->id,
            'error' => $exception->getMessage()
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
            // Ignore logging failures (e.g. storage/logs not writable) so they don't crash the job
        }
    }
}

