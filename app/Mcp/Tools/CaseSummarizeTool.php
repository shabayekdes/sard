<?php

namespace App\Mcp\Tools;

use App\Models\CaseModel;
use App\Models\CaseNote;
use App\Models\CaseDocument;
use App\Services\AiService;
use App\Services\LegalPrompts;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * MCP Tool: Case Summarize
 * 
 * Summarizes a legal case by:
 * - Loading case data
 * - Loading related notes and latest documents
 * - Assembling context safely
 * - Calling AI service with case summarization prompt
 * - Caching summary on the case
 * - Returning summary with source counts
 */
class CaseSummarizeTool
{
    private AiService $aiService;
    private const CACHE_TTL = 3600; // 1 hour cache

    public function __construct(?AiService $aiService = null)
    {
        $this->aiService = $aiService ?? new AiService();
    }

    /**
     * Execute the case summarization tool
     *
     * @param int $caseId The case ID to summarize
     * @param bool $forceRefresh Force refresh even if cached
     * @return array Summary data with source counts
     * @throws Exception
     */
    public function execute(int $caseId, bool $forceRefresh = false): array
    {
        try {
            // Load case with relationships
            $case = $this->loadCase($caseId);

            // Check cache if not forcing refresh
            if (!$forceRefresh && $case->ai_summary) {
                $sourceCounts = $this->getSourceCounts($case);
                return [
                    'summary' => $case->ai_summary,
                    'source_counts' => $sourceCounts,
                    'cached' => true,
                    'case_id' => $case->id,
                    'case_number' => $case->case_id ?? $case->case_number,
                ];
            }

            // Load notes and documents
            $notes = $this->loadCaseNotes($case);
            $documents = $this->loadLatestDocuments($case);

            // Assemble context safely
            $context = $this->assembleContext($case, $notes, $documents);

            // Get jurisdiction for language awareness
            $jurisdiction = $this->getJurisdiction($case);

            // Generate summary using AI service
            $summary = $this->generateSummary($context, $jurisdiction, $notes, $documents);

            // Cache summary on case
            $this->cacheSummary($case, $summary);

            // Get source counts
            $sourceCounts = $this->getSourceCounts($case, $notes, $documents);

            return [
                'summary' => $summary,
                'source_counts' => $sourceCounts,
                'cached' => false,
                'case_id' => $case->id,
                'case_number' => $case->case_id ?? $case->case_number,
            ];

        } catch (Exception $e) {
            Log::error('MCP Case Summarize Tool Error', [
                'case_id' => $caseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new Exception('Failed to summarize case: ' . $e->getMessage());
        }
    }

    /**
     * Load case with necessary relationships
     */
    private function loadCase(int $caseId): CaseModel
    {
        $case = CaseModel::with([
            'client',
            'caseType',
            'caseCategory',
            'caseStatus',
            'court',
            'creator',
        ])->findOrFail($caseId);

        return $case;
    }

    /**
     * Load case notes
     */
    private function loadCaseNotes(CaseModel $case): array
    {
        $notes = CaseNote::whereJsonContains('case_ids', (string)$case->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(20) // Limit to most recent 20 notes
            ->get(['id', 'note_id', 'title', 'content', 'note_type', 'note_date', 'created_at']);

        return $notes->map(function ($note) {
            return [
                'id' => $note->id,
                'note_id' => $note->note_id,
                'title' => $note->title,
                'content' => $note->content,
                'type' => $note->note_type,
                'date' => $note->note_date?->format('Y-m-d'),
                'created_at' => $note->created_at?->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }

    /**
     * Load latest documents for the case
     */
    private function loadLatestDocuments(CaseModel $case): array
    {
        $documents = CaseDocument::where('case_id', $case->id)
            ->where('status', 'active')
            ->with('documentType')
            ->orderBy('created_at', 'desc')
            ->limit(15) // Limit to most recent 15 documents
            ->get(['id', 'document_id', 'document_name', 'description', 'document_date', 'document_type_id', 'created_at']);

        return $documents->map(function ($doc) {
            return [
                'id' => $doc->id,
                'document_id' => $doc->document_id,
                'name' => $doc->document_name,
                'description' => $doc->description,
                'type' => $doc->documentType?->name ?? 'Unknown',
                'date' => $doc->document_date?->format('Y-m-d'),
                'created_at' => $doc->created_at?->format('Y-m-d H:i:s'),
            ];
        })->toArray();
    }

    /**
     * Assemble context safely for AI processing
     */
    private function assembleContext(CaseModel $case, array $notes, array $documents): array
    {
        $context = [
            'case_id' => $case->case_id ?? 'Not provided',
            'case_number' => $case->case_number ?? 'Not provided',
            'file_number' => $case->file_number ?? 'Not provided',
            'title' => $case->title ?? 'Not provided',
            'description' => $case->description ?? 'No description available',
            'status' => $case->caseStatus?->name ?? 'Unknown',
            'type' => $case->caseType?->name ?? 'Unknown',
            'category' => $case->caseCategory?->name ?? null,
            'priority' => $case->priority ?? 'normal',
            'filing_date' => $case->filing_date?->format('Y-m-d') ?? 'Not provided',
            'expected_completion_date' => $case->expected_completion_date?->format('Y-m-d') ?? 'Not provided',
            'client' => [
                'name' => $case->client?->name ?? 'Not specified',
                'id' => $case->client_id ?? null,
            ],
            'court' => [
                'name' => $case->court?->name ?? null,
                'id' => $case->court_id ?? null,
            ],
            'opposing_party' => $case->opposing_party ?? null,
            'court_details' => $case->court_details ?? null,
        ];

        // Add notes if available
        if (!empty($notes)) {
            $context['notes'] = $notes;
            $context['notes_count'] = count($notes);
        } else {
            $context['notes'] = [];
            $context['notes_count'] = 0;
        }

        // Add documents if available
        if (!empty($documents)) {
            $context['documents'] = $documents;
            $context['documents_count'] = count($documents);
        } else {
            $context['documents'] = [];
            $context['documents_count'] = 0;
        }

        return $context;
    }

    /**
     * Get jurisdiction information for language and legal system awareness
     */
    private function getJurisdiction(CaseModel $case): ?string
    {
        $jurisdiction = null;
        
        if ($case->court) {
            $jurisdiction = $case->court->name;
            
            // Check if court has jurisdiction field
            if ($case->court->jurisdiction) {
                $jurisdiction .= ' (' . $case->court->jurisdiction . ')';
            }
        }

        if (!$jurisdiction && $case->court_details) {
            $jurisdiction = $case->court_details;
        }
        
        // Detect Saudi jurisdiction
        if ($jurisdiction && (
            stripos($jurisdiction, 'Saudi') !== false ||
            stripos($jurisdiction, 'السعودية') !== false ||
            stripos($jurisdiction, 'المملكة') !== false ||
            stripos($jurisdiction, 'محكمة') !== false
        )) {
            $jurisdiction = 'Saudi Arabia - ' . $jurisdiction;
        }

        return $jurisdiction;
    }

    /**
     * Generate summary using AI service
     */
    private function generateSummary(array $context, ?string $jurisdiction, array $notes, array $documents): string
    {
        // Build the prompt with context
        $caseDataJson = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        $userPrompt = "Please provide a comprehensive summary of the following legal case. ";
        
        if ($jurisdiction) {
            $userPrompt .= "This case is in the jurisdiction of: {$jurisdiction}. ";
            
            // Add Saudi law awareness if jurisdiction is Saudi
            if (stripos($jurisdiction, 'Saudi') !== false || stripos($jurisdiction, 'السعودية') !== false) {
                $userPrompt .= "Apply knowledge of Saudi legal system, Sharia law principles, and Saudi court procedures. ";
                $userPrompt .= "Use appropriate Saudi legal terminology when relevant. ";
            }
        }
        
        $userPrompt .= "Include key facts, current status, important dates, and any critical legal issues.\n\n";
        $userPrompt .= "Case Information:\n{$caseDataJson}\n\n";
        
        // Add note about documents if none exist
        if (empty($documents)) {
            $userPrompt .= "IMPORTANT: No documents are associated with this case. Please note this in your summary.\n\n";
        }
        
        // Add note about notes if none exist
        if (empty($notes)) {
            $userPrompt .= "IMPORTANT: No case notes are available for this case. Please note this in your summary.\n\n";
        }

        // Use the case summarization prompt from LegalPrompts
        $systemPrompt = LegalPrompts::CASE_SUMMARIZATION;

        // Use AiService's summarizeCase method but with custom context
        // We'll call the AI directly since we have custom context assembly
        $client = $this->aiService->getClient();
        $model = $this->aiService->getModel();
        
        $response = $client->chat()->create([
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'temperature' => config('openai.legal.temperature', 0.2),
            'max_tokens' => config('openai.legal.max_tokens.case_summary', 2000),
        ]);

        $summary = $response->choices[0]->message->content ?? '';

        if (empty($summary)) {
            throw new Exception('Empty response from AI service');
        }

        return trim($summary);
    }

    /**
     * Cache summary on the case
     */
    private function cacheSummary(CaseModel $case, string $summary): void
    {
        $case->ai_summary = $summary;
        $case->ai_summary_updated_at = now();
        $case->save();
    }

    /**
     * Get source counts
     */
    private function getSourceCounts(CaseModel $case, ?array $notes = null, ?array $documents = null): array
    {
        if ($notes === null) {
            $notes = $this->loadCaseNotes($case);
        }

        if ($documents === null) {
            $documents = $this->loadLatestDocuments($case);
        }

        return [
            'notes' => count($notes),
            'documents' => count($documents),
            'total_sources' => count($notes) + count($documents),
        ];
    }
}

