# AI Service Usage Guide

This document demonstrates how to use the `AiService` class for OpenAI integration in legal applications.

## Service Class

The main service class is located at: `app/Services/AiService.php`

### Key Features

- **Low temperature defaults** (0.2) to minimize hallucinations
- **Legal-specific system prompts** for accurate, factual responses
- **Easy model swapping** via constructor or setter methods
- **Three main operations**: summarize text, summarize case, draft memo

## Configuration

Configuration is in `config/openai.php`. Key settings:

```php
'default_model' => 'gpt-4o-mini',  // Can be changed easily
'legal' => [
    'temperature' => 0.2,  // Low temperature for factual accuracy
    'prompts' => [
        'summarize_text' => '...',
        'summarize_case' => '...',
        'draft_memo' => '...',
    ],
],
```

## Usage Examples

### 1. Basic Usage in Controller

```php
use App\Services\AiService;

// Summarize text
$aiService = new AiService();
$summary = $aiService->summarizeText(
    text: $text,
    maxLength: 150,
    focus: 'key facts'
);

// Summarize case
$summary = $aiService->summarizeCase($case, [
    'include_documents' => true,
    'include_timeline' => true,
]);

// Draft memo
$memo = $aiService->draftMemo(
    subject: 'Contract Review',
    context: 'Client wants to review employment contract...',
    options: [
        'format' => 'standard',
        'tone' => 'professional',
        'length' => 'medium',
    ]
);
```

### 2. Using Custom Model/Temperature

```php
$aiService = new AiService();

// Use a different model
$aiService->setModel('gpt-4');

// Adjust temperature for specific use case
$aiService->setTemperature(0.3);

$summary = $aiService->summarizeText($text);
```

### 3. Using in Jobs (Async Processing)

```php
use App\Jobs\ProcessCaseSummary;

// Dispatch job for background processing
ProcessCaseSummary::dispatch($caseId, [
    'include_documents' => true,
    'include_timeline' => false,
]);
```

### 4. Error Handling

```php
try {
    $aiService = new AiService();
    $summary = $aiService->summarizeText($text);
} catch (Exception $e) {
    // Handle error (API key missing, rate limit, etc.)
    Log::error('AI Service Error: ' . $e->getMessage());
    return response()->json(['error' => $e->getMessage()], 500);
}
```

## API Key Configuration

The service checks for API key in this order:
1. Settings table (`chatgptKey` key) - for backward compatibility
2. Config file (`config('openai.api_key')`)
3. Environment variable (`OPENAI_API_KEY`)

## Model Selection

Default model can be changed via:
- Environment variable: `OPENAI_DEFAULT_MODEL`
- Settings table: `chatgptModel` key
- Config file: `config('openai.default_model')`
- Runtime: `$aiService->setModel('gpt-4')`

## System Prompts

All prompts are configured in `config/openai.php` under `legal.prompts`. They emphasize:
- Factual accuracy
- No hallucinations
- Professional legal language
- Clear identification of missing information

## See Also

- `app/Http/Controllers/ExampleAiController.php` - Full controller examples
- `app/Jobs/ProcessCaseSummary.php` - Job example for case summaries
- `app/Jobs/GenerateMemoDraft.php` - Job example for memo generation


