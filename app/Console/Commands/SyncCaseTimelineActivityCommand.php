<?php

namespace App\Console\Commands;

use App\Models\CaseTimeline;
use App\Services\CaseActivityLogger;
use Illuminate\Console\Command;

class SyncCaseTimelineActivityCommand extends Command
{
    protected $signature = 'case-activity:sync-manual-timelines';

    protected $description = 'Backfill case_activity_logs from existing case_timelines (manual events)';

    public function handle(): int
    {
        $n = 0;
        CaseTimeline::query()->orderBy('id')->chunkById(100, function ($rows) use (&$n) {
            foreach ($rows as $timeline) {
                CaseActivityLogger::syncManualTimeline($timeline);
                $n++;
            }
        });

        $this->info("Synced {$n} manual timeline rows into case_activity_logs.");

        return self::SUCCESS;
    }
}
