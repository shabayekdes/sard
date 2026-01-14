<?php

namespace App\Jobs;

use App\Services\AiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Example Job for generating memo drafts asynchronously
 * 
 * This is useful when memo generation might take time
 * and you want to provide a better user experience.
 */
class GenerateMemoDraft implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $subject,
        public string $context = '',
        public array $options = [],
        public ?int $userId = null,
        public ?int $documentId = null, // If saving to a document
    ) {
        $this->userId = $userId ?? auth()->id();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $aiService = new AiService();
            
            // Generate the memo
            $memo = $aiService->draftMemo(
                subject: $this->subject,
                context: $this->context,
                options: $this->options
            );
            
            // Example: Save to database or file
            // You might want to create a Document model entry
            // Document::create([
            //     'title' => $this->subject,
            //     'content' => $memo,
            //     'type' => 'memo',
            //     'created_by' => $this->userId,
            // ]);
            
            // Or send via email/notification
            // Mail::to($user)->send(new MemoDraftReady($memo));
            
            Log::info('Memo draft generated successfully', [
                'subject' => $this->subject,
                'memo_length' => strlen($memo),
                'user_id' => $this->userId,
            ]);
            
        } catch (Exception $e) {
            Log::error('Failed to generate memo draft', [
                'subject' => $this->subject,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Exception $exception): void
    {
        Log::error('Memo generation job failed permanently', [
            'subject' => $this->subject,
            'error' => $exception?->getMessage(),
        ]);
    }
}


