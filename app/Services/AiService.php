<?php

namespace App\Services;

use App\Models\CaseModel;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use OpenAI;
use OpenAI\Client;
use Exception;

class AiService
{
    private ?Client $client = null;
    private string $model;
    private float $temperature;

    /**
     * Initialize the AI service with configuration
     */
    public function __construct(?string $apiKey = null, ?string $model = null)
    {
        $apiKey = $apiKey ?? $this->getApiKey();
        $this->model = $model ?? $this->getDefaultModel();
        $this->temperature = config('openai.legal.temperature', 0.2);

        if (!$apiKey) {
            throw new Exception('OpenAI API key is not configured. Please set OPENAI_API_KEY in your .env file or configure chatgptKey in settings.');
        }

        $this->client = OpenAI::client($apiKey);
    }

    /**
     * Get API key from settings or config
     */
    private function getApiKey(): ?string
    {
        // Try to get from settings table first (for backward compatibility)
        $settingKey = Setting::where('key', 'chatgptKey')->value('value');
        if ($settingKey) {
            return $settingKey;
        }

        // Fall back to config
        return config('openai.api_key');
    }

    /**
     * Get default model from settings or config
     */
    private function getDefaultModel(): string
    {
        // Try to get from settings table first (for backward compatibility)
        $settingModel = Setting::where('key', 'chatgptModel')->value('value');
        if ($settingModel) {
            return $settingModel;
        }

        // Fall back to config
        return config('openai.default_model', 'gpt-4o-mini');
    }

    /**
     * Summarize text content
     *
     * @param string $text The text to summarize
     * @param int $maxLength Maximum length of summary in words (default: 150)
     * @param string|null $focus Optional focus area (e.g., "key facts", "legal issues")
     * @return string The summarized text
     * @throws Exception
     */
    public function summarizeText(string $text, int $maxLength = 150, ?string $focus = null): string
    {
        if (empty(trim($text))) {
            throw new Exception('Text cannot be empty');
        }

        $systemPrompt = config('legal-prompts.document_summarization');
        $userPrompt = $this->buildSummarizePrompt($text, $maxLength, $focus);

        try {
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => $this->temperature,
                'max_tokens' => $this->calculateMaxTokens($maxLength),
            ]);

            $content = $response->choices[0]->message->content ?? '';

            if (empty($content)) {
                throw new Exception('Empty response from OpenAI API');
            }

            return trim($content);
        } catch (Exception $e) {
            Log::error('AI Service - Summarize Text Error', [
                'error' => $e->getMessage(),
                'text_length' => strlen($text),
            ]);
            throw new Exception('Failed to summarize text: ' . $e->getMessage());
        }
    }

    /**
     * Summarize a legal case
     *
     * @param CaseModel $case The case model to summarize
     * @param array $options Optional parameters (include_documents, include_timeline, etc.)
     * @return string The case summary
     * @throws Exception
     */
    public function summarizeCase(CaseModel $case, array $options = []): string
    {
        $includeDocuments = $options['include_documents'] ?? false;
        $includeTimeline = $options['include_timeline'] ?? false;
        $includeTeam = $options['include_team'] ?? false;

        $caseData = $this->buildCaseContext($case, $includeDocuments, $includeTimeline, $includeTeam);
        $systemPrompt = config('legal-prompts.text_summarization');
        $userPrompt = $this->buildCaseSummaryPrompt($caseData);

        try {
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => $this->temperature,
                'max_tokens' => config('openai.legal.max_tokens.case_summary', 2000),
            ]);

            $content = $response->choices[0]->message->content ?? '';

            if (empty($content)) {
                throw new Exception('Empty response from OpenAI API');
            }

            return trim($content);
        } catch (Exception $e) {
            Log::error('AI Service - Summarize Case Error', [
                'error' => $e->getMessage(),
                'case_id' => $case->id,
            ]);
            throw new Exception('Failed to summarize case: ' . $e->getMessage());
        }
    }

    /**
     * Draft a legal memo
     *
     * @param string $subject The memo subject/topic
     * @param string $context Additional context or background information
     * @param array $options Optional parameters (format, tone, length, etc.)
     * @return string The drafted memo
     * @throws Exception
     */
    public function draftMemo(string $subject, string $context = '', array $options = []): string
    {
        if (empty(trim($subject))) {
            throw new Exception('Memo subject cannot be empty');
        }

        $format = $options['format'] ?? 'standard';
        $tone = $options['tone'] ?? 'professional';
        $length = $options['length'] ?? 'medium'; // short, medium, long
        $includeRecommendations = $options['include_recommendations'] ?? true;

        $systemPrompt = config('legal-prompts.drafting');
        $userPrompt = $this->buildMemoPrompt($subject, $context, $format, $tone, $length, $includeRecommendations);

        try {
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => $this->temperature,
                'max_tokens' => $this->getMemoMaxTokens($length),
            ]);

            $content = $response->choices[0]->message->content ?? '';

            if (empty($content)) {
                throw new Exception('Empty response from OpenAI API');
            }

            return trim($content);
        } catch (Exception $e) {
            Log::error('AI Service - Draft Memo Error', [
                'error' => $e->getMessage(),
                'subject' => $subject,
            ]);
            throw new Exception('Failed to draft memo: ' . $e->getMessage());
        }
    }

    /**
     * Build case context for summarization
     */
    private function buildCaseContext(CaseModel $case, bool $includeDocuments, bool $includeTimeline, bool $includeTeam): array
    {
        $context = [
            'case_id' => $case->case_id,
            'case_number' => $case->case_number,
            'title' => $case->title,
            'description' => $case->description,
            'status' => $case->caseStatus?->name ?? 'Unknown',
            'type' => $case->caseType?->name ?? 'Unknown',
            'category' => $case->caseCategory?->name ?? null,
            'priority' => $case->priority ?? 'normal',
            'filing_date' => $case->filing_date?->format('Y-m-d'),
            'expected_completion_date' => $case->expected_completion_date?->format('Y-m-d'),
            'client' => $case->client?->name ?? 'Unknown',
            'court' => $case->court?->name ?? null,
            'opposing_party' => $case->opposing_party,
        ];

        if ($includeTeam && $case->teamMembers) {
            $context['team_members'] = $case->teamMembers->map(function ($member) {
                return $member->user?->name ?? 'Unknown';
            })->toArray();
        }

        if ($includeTimeline && $case->timeEntries) {
            $context['recent_activities'] = $case->timeEntries()
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($entry) {
                    return [
                        'date' => $entry->date?->format('Y-m-d'),
                        'description' => $entry->description,
                        'hours' => $entry->hours,
                    ];
                })
                ->toArray();
        }

        return $context;
    }

    /**
     * Build summarize text prompt
     */
    private function buildSummarizePrompt(string $text, int $maxLength, ?string $focus): string
    {
        $prompt = "Please summarize the following text in approximately {$maxLength} words.";

        if ($focus) {
            $prompt .= " Focus on: {$focus}.";
        }

        $prompt .= "\n\nText to summarize:\n\n{$text}";

        return $prompt;
    }

    /**
     * Build case summary prompt
     */
    private function buildCaseSummaryPrompt(array $caseData): string
    {
        $prompt = "Please provide a comprehensive summary of the following legal case. Include key facts, current status, important dates, and any critical legal issues.\n\n";
        $prompt .= "Case Information:\n";
        $prompt .= json_encode($caseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return $prompt;
    }

    /**
     * Build memo draft prompt
     */
    private function buildMemoPrompt(
        string $subject,
        string $context,
        string $format,
        string $tone,
        string $length,
        bool $includeRecommendations
    ): string {
        $prompt = "Draft a legal memo on the following subject: {$subject}\n\n";

        if (!empty($context)) {
            $prompt .= "Context and background information:\n{$context}\n\n";
        }

        $prompt .= "Requirements:\n";
        $prompt .= "- Format: {$format}\n";
        $prompt .= "- Tone: {$tone}\n";
        $prompt .= "- Length: {$length}\n";

        if ($includeRecommendations) {
            $prompt .= "- Include actionable recommendations\n";
        }

        $prompt .= "\nPlease structure the memo with appropriate sections (e.g., Issue, Analysis, Conclusion, Recommendations if applicable).";

        return $prompt;
    }

    /**
     * Calculate max tokens based on word count
     */
    private function calculateMaxTokens(int $wordCount): int
    {
        // Rough estimate: 1 token ≈ 0.75 words
        return (int) ceil($wordCount / 0.75) + 100; // Add buffer
    }

    /**
     * Get max tokens for memo based on length
     */
    private function getMemoMaxTokens(string $length): int
    {
        return match ($length) {
            'short' => 500,
            'medium' => 1500,
            'long' => 3000,
            default => 1500,
        };
    }

    /**
     * Set custom temperature (for specific use cases)
     */
    public function setTemperature(float $temperature): self
    {
        if ($temperature < 0 || $temperature > 2) {
            throw new Exception('Temperature must be between 0 and 2');
        }
        $this->temperature = $temperature;
        return $this;
    }

    /**
     * Summarize a legal document
     *
     * @param string $documentContent The document content to summarize
     * @param string|null $documentName Optional document name for citation
     * @return string The document summary
     * @throws Exception
     */
    public function summarizeDocument(string $documentContent, ?string $documentName = null): string
    {
        if (empty(trim($documentContent))) {
            throw new Exception('Document content cannot be empty');
        }

        $systemPrompt = config('legal-prompts.document_summarization');
        $userPrompt = $this->buildDocumentSummaryPrompt($documentContent, $documentName);

        try {
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => $this->temperature,
                'max_tokens' => config('openai.legal.max_tokens.case_summary', 2000),
            ]);

            $content = $response->choices[0]->message->content ?? '';

            if (empty($content)) {
                throw new Exception('Empty response from OpenAI API');
            }

            return trim($content);
        } catch (Exception $e) {
            Log::error('AI Service - Summarize Document Error', [
                'error' => $e->getMessage(),
                'document_name' => $documentName,
            ]);
            throw new Exception('Failed to summarize document: ' . $e->getMessage());
        }
    }

    /**
     * Extract timeline from text or case data
     *
     * @param string|array $source Text content or structured data to extract timeline from
     * @param string|null $sourceName Optional source name for citation (e.g., document name, case number)
     * @return string The extracted timeline
     * @throws Exception
     */
    public function extractTimeline($source, ?string $sourceName = null): string
    {
        if (is_array($source)) {
            $source = json_encode($source, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        if (empty(trim($source))) {
            throw new Exception('Source content cannot be empty');
        }

        $systemPrompt = config('legal-prompts.timeline_extraction');
        $userPrompt = $this->buildTimelineExtractionPrompt($source, $sourceName);

        try {
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => $this->temperature,
                'max_tokens' => config('openai.legal.max_tokens.case_summary', 2000),
            ]);

            $content = $response->choices[0]->message->content ?? '';

            if (empty($content)) {
                throw new Exception('Empty response from OpenAI API');
            }

            return trim($content);
        } catch (Exception $e) {
            Log::error('AI Service - Extract Timeline Error', [
                'error' => $e->getMessage(),
                'source_name' => $sourceName,
            ]);
            throw new Exception('Failed to extract timeline: ' . $e->getMessage());
        }
    }

    /**
     * Build document summary prompt
     */
    private function buildDocumentSummaryPrompt(string $documentContent, ?string $documentName): string
    {
        $prompt = "Please summarize the following legal document";
        
        if ($documentName) {
            $prompt .= " (Document Name: {$documentName})";
        }
        
        $prompt .= ".\n\nDocument Content:\n\n{$documentContent}";

        return $prompt;
    }

    /**
     * Build timeline extraction prompt
     */
    private function buildTimelineExtractionPrompt(string $source, ?string $sourceName): string
    {
        $prompt = "Please extract and organize all chronological information from the following";
        
        if ($sourceName) {
            $prompt .= " (Source: {$sourceName})";
        }
        
        $prompt .= ".\n\nSource Content:\n\n{$source}";

        return $prompt;
    }

    /**
     * Set custom model (for specific use cases)
     */
    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Get the OpenAI client
     */
    public function getClient(): Client
    {
        if (!$this->client) {
            throw new Exception('AI Service client not initialized');
        }
        return $this->client;
    }

    /**
     * Get the current model
     */
    public function getModel(): string
    {
        return $this->model;
    }
}

