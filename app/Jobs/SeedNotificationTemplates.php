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
        public int $companyUserId
    ) {
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $languages = json_decode(file_get_contents(resource_path('lang/language.json')), true);
        $langCodes = collect($languages)->pluck('code')->toArray();

        $templates = \App\Models\NotificationTemplate::all();

        foreach ($templates as $template) {
            foreach ($langCodes as $langCode) {
                $existingContent = \App\Models\NotificationTemplateLang::where('parent_id', $template->id)
                    ->where('lang', $langCode)
                    ->where('created_by', $this->companyUserId)
                    ->first();

                if ($existingContent) {
                    continue;
                }

                $globalContent = \App\Models\NotificationTemplateLang::where('parent_id', $template->id)
                    ->where('lang', $langCode)
                    ->where('created_by', 1)
                    ->first();

                if ($globalContent) {
                    \App\Models\NotificationTemplateLang::create([
                        'parent_id' => $template->id,
                        'lang' => $langCode,
                        'title' => $globalContent->title,
                        'content' => $globalContent->content,
                        'created_by' => $this->companyUserId
                    ]);
                }
            }
        }

        // Also create default notification settings
        $this->createDefaultNotificationSettings();

        Log::info("SeedNotificationTemplates: Completed", [
            'company_id' => $this->companyUserId
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
                \App\Models\UserNotificationTemplate::updateOrCreate(
                    [
                        'user_id' => $this->companyUserId,
                        'template_id' => $template->id,
                        'type' => $type
                    ],
                    ['is_active' => false] // Default to disabled
                );
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SeedNotificationTemplates: Job failed", [
            'company_id' => $this->companyUserId,
            'error' => $exception->getMessage()
        ]);
    }
}

