# MCP Case Summarize Tool Usage Guide

This document explains how to use the MCP Case Summarize Tool for generating AI-powered case summaries.

## Overview

The `CaseSummarizeTool` is an MCP (Model Context Protocol) tool that:
- Loads case data with relationships
- Loads related notes and latest documents
- Assembles context safely
- Calls AI service with case summarization prompt
- Caches summary on the case
- Returns summary with source counts

## Features

- **No Hallucinations**: Uses `LegalPrompts::CASE_SUMMARIZATION` which enforces factual accuracy
- **Document Awareness**: Explicitly mentions if no documents exist
- **Jurisdiction-Aware**: Includes jurisdiction information for language awareness
- **Caching**: Caches summaries on the case model to avoid redundant API calls
- **Source Tracking**: Returns counts of notes and documents used

## Usage

### Basic Usage

```php
use App\Mcp\Tools\CaseSummarizeTool;

$tool = new CaseSummarizeTool();
$result = $tool->execute($caseId);

// Result structure:
// [
//     'summary' => '...',
//     'source_counts' => [
//         'notes' => 5,
//         'documents' => 12,
//         'total_sources' => 17,
//     ],
//     'cached' => false,
//     'case_id' => 123,
//     'case_number' => 'CASE001',
// ]
```

### Force Refresh

```php
// Force refresh even if cached
$result = $tool->execute($caseId, forceRefresh: true);
```

### In a Controller

```php
use App\Mcp\Tools\CaseSummarizeTool;

public function summarizeCase(Request $request, int $caseId)
{
    $tool = new CaseSummarizeTool();
    $result = $tool->execute($caseId, $request->boolean('force_refresh'));
    
    return response()->json($result);
}
```

## What Gets Loaded

### Case Data
- Basic case information (ID, number, title, description)
- Client information
- Case type, category, status
- Court information
- Important dates (filing, expected completion)
- Opposing party information

### Notes
- Up to 20 most recent case notes
- Notes where `case_ids` JSON contains the case ID
- Only active notes are included

### Documents
- Up to 15 most recent case documents
- Documents directly linked to the case via `case_id`
- Only active documents are included
- Includes document type information

## Context Assembly

The tool safely assembles context by:
- Handling null values gracefully
- Providing "Not provided" or "Not specified" for missing data
- Limiting note/document counts to prevent token overflow
- Including metadata about source availability

## AI Prompt

The tool uses `LegalPrompts::CASE_SUMMARIZATION` which:
- Enforces no hallucinations
- Requires bullet point format
- Demands document citations
- Explicitly states missing information

## Caching

Summaries are cached on the case model:
- Field: `ai_summary` (text)
- Timestamp: `ai_summary_updated_at`
- Cache is checked before generating new summary
- Use `forceRefresh: true` to bypass cache

## Source Counts

The tool returns source counts:
- `notes`: Number of notes used
- `documents`: Number of documents used
- `total_sources`: Sum of notes and documents

## Error Handling

The tool throws exceptions for:
- Case not found
- AI service errors
- Empty AI responses
- Configuration issues

Always wrap in try-catch:

```php
try {
    $result = $tool->execute($caseId);
} catch (Exception $e) {
    // Handle error
    Log::error('Case summarize failed', ['error' => $e->getMessage()]);
}
```

## Database Migration

Run the migration to add caching fields:

```bash
php artisan migrate
```

This adds:
- `ai_summary` (text, nullable)
- `ai_summary_updated_at` (timestamp, nullable)

## Example Response

```json
{
    "success": true,
    "data": {
        "summary": "Case Overview:\n• Case Number/ID: CASE001\n• Case Type: Contract Dispute\n...",
        "source_counts": {
            "notes": 5,
            "documents": 12,
            "total_sources": 17
        },
        "cached": false,
        "case_id": 123,
        "case_number": "CASE001"
    }
}
```

## See Also

- `app/Mcp/Tools/CaseSummarizeTool.php` - The tool implementation
- `app/Services/LegalPrompts.php` - Legal system prompts
- `app/Services/AiService.php` - AI service layer
- `app/Http/Controllers/ExampleMcpCaseSummarizeController.php` - Example usage


