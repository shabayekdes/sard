<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncNotificationTemplates extends Command
{
    protected $signature = 'notifications:sync-templates';
    protected $description = 'Sync notification templates for all existing companies';

    public function handle()
    {
        $this->info('Notification template title and content are now stored on notification_templates (translatable). No per-company lang sync needed.');
    }
}
