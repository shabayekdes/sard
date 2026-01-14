<?php

namespace App\Jobs;

use App\Models\CaseModel;
use App\Services\AiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Example Job demonstrating AiService usage in background processing
 * 
 * This job can be dispatched to process case summaries asynchronously,
 * which is useful for large cases or when you want to avoid blocking
 * the user's request.
 */
class ProcessCaseSummary implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $caseId,
        public array $options = []
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $case = CaseModel::findOrFail($this->caseId);
            
            // Initialize AI service
            $aiService = new AiService();
            
            // Generate case summary
            $summary = $aiService->summarizeCase($case, $this->options);
            
            // Store the summary (example: you might want to save it to a case_notes table)
            // CaseNote::create([
            //     'case_id' => $case->id,
            //     'note' => $summary,
            //     'type' => 'ai_summary',
            //     'created_by' => auth()->id(),
            // ]);
            
            // Or update a summary field on the case
            // $case->update(['ai_summary' => $summary]);
            
            Log::info('Case summary generated successfully', [
                'case_id' => $this->caseId,
                'summary_length' => strlen($summary),
            ]);
            
        } catch (Exception $e) {
            Log::error('Failed to process case summary', [
                'case_id' => $this->caseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Exception $exception): void
    {
        Log::error('Case summary job failed permanently', [
            'case_id' => $this->caseId,
            'error' => $exception?->getMessage(),
        ]);
        
        // You might want to notify the user or admin here
        // Notification::send($user, new CaseSummaryFailed($this->caseId));
    }
}


