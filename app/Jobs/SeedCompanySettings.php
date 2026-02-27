<?php

namespace App\Jobs;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job to copy settings from superadmin to company user
 */
class SeedCompanySettings implements ShouldQueue
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
        $superAdmin = User::where('type', 'superadmin')->first();
        if (!$superAdmin) {
            // If no superadmin, create default settings
            createDefaultSettings($this->tenant_id);
            Log::info("SeedCompanySettings: No superadmin found, created default settings", [
                'company_id' => $this->tenant_id
            ]);
            return;
        }

        // Settings to copy from superadmin (system settings only, not theme settings)
        $settingsToCopy = [
            'defaultCountry', 'defaultLanguage', 'dateFormat', 'timeFormat', 'calendarStartDay',
            'defaultTimezone', 'ENABLE_EMAIL_VERIFICATION', 'landingPageEnabled',
            'logoDark', 'logoLight', 'favicon', 'titleText', 'footerText'
        ];

        $superAdminSettings = Setting::where('tenant_id', $superAdmin->id)
            ->whereIn('key', $settingsToCopy)
            ->get();

        $settingsData = [];

        // Only copy existing superadmin settings
        foreach ($superAdminSettings as $setting) {
            $settingsData[] = [
                'tenant_id' => $this->tenant_id,
                'key' => $setting->key,
                'value' => $setting->value,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Setting::insertOrIgnore($settingsData);

        Log::info("SeedCompanySettings: Completed", [
            'company_id' => $this->tenant_id,
            'copied' => count($settingsData)
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SeedCompanySettings: Job failed", [
            'company_id' => $this->tenant_id,
            'error' => $exception->getMessage()
        ]);
    }
}

