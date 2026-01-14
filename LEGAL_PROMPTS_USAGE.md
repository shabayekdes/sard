# Legal System Prompts Usage Guide

This document explains how to use the reusable legal system prompts for AI operations.

## Overview

The legal prompts are designed to ensure:
- **Factual accuracy** - Never invent facts, dates, or details
- **Missing information identification** - Explicitly state when information is unavailable
- **Bullet point format** - Clear, organized output
- **Document citations** - Cite document names when possible

## Available Prompts

1. **Case Summarization** - Summarize legal cases
2. **Document Summarization** - Summarize legal documents
3. **Drafting** - Draft emails/memos
4. **Timeline Extraction** - Extract chronological information

## Usage Methods

### Method 1: Config File (Recommended)

Access prompts via the config file:

```php
use Illuminate\Support\Facades\Config;

// Get a specific prompt
$prompt = config('legal-prompts.case_summarization');
$prompt = config('legal-prompts.document_summarization');
$prompt = config('legal-prompts.drafting');
$prompt = config('legal-prompts.timeline_extraction');
```

### Method 2: LegalPrompts Class Constants

Use the class constants for type-safe access:

```php
use App\Services\LegalPrompts;

// Direct constant access
$prompt = LegalPrompts::CASE_SUMMARIZATION;
$prompt = LegalPrompts::DOCUMENT_SUMMARIZATION;
$prompt = LegalPrompts::DRAFTING;
$prompt = LegalPrompts::TIMELINE_EXTRACTION;

// Or use the get() method
$prompt = LegalPrompts::get('case_summarization');

// Get all prompts
$allPrompts = LegalPrompts::all();
```

### Method 3: Via AiService

The `AiService` automatically uses these prompts:

```php
use App\Services\AiService;

$aiService = new AiService();

// Case summarization (uses case_summarization prompt)
$summary = $aiService->summarizeCase($case);

// Document summarization (uses document_summarization prompt)
$summary = $aiService->summarizeDocument($documentContent, $documentName);

// Drafting (uses drafting prompt)
$memo = $aiService->draftMemo($subject, $context);

// Timeline extraction (uses timeline_extraction prompt)
$timeline = $aiService->extractTimeline($source, $sourceName);
```

## Prompt Rules

All prompts enforce these rules:

1. **Never invent facts** - Only use information explicitly provided
2. **State missing information** - Clearly identify what's not available
3. **Use bullet points** - Organize output with bullet points (•)
4. **Cite documents** - Reference document names when available

## Example Output Format

### Case Summarization
```
Case Overview:
• Case Number/ID: CASE001
• Case Type: Contract Dispute
• Filing Date: 2024-01-15
• Current Status: Active

Key Facts:
• [Document Name] states that the contract was signed on 2024-01-01
• [Document Name] indicates payment was due on 2024-01-10

Missing Information:
• Opposing party contact information is not available
• Court hearing date is not provided
```

### Document Summarization
```
Document: Employment Contract - John Doe

Document Overview:
• Document Name: Employment Contract
• Document Type: Contract
• Date: 2024-01-01

Key Provisions:
• [Employment Contract] - Section 3 states salary of $100,000/year
• [Employment Contract] - Section 5 indicates 20 days PTO

Missing Information:
• Termination clause details are not mentioned in the document
```

### Timeline Extraction
```
Timeline:
• 2024-01-01 - Contract signed - [Employment Contract]
• 2024-01-10 - Payment due date - [Invoice Document]
• 2024-01-15 - Case filed - [Court Filing]

Missing Dates:
• Contract negotiation start - Date not available
• First payment received - Date not available
```

## Customization

To customize prompts, edit:
- `config/legal-prompts.php` - For config-based access
- `app/Services/LegalPrompts.php` - For constant-based access

Both files contain the same prompts, so keep them in sync if you make changes.

## Integration with OpenAI

These prompts are automatically used by `AiService` when making OpenAI API calls. The service sets:
- Low temperature (0.2) for factual accuracy
- Appropriate max tokens based on operation type
- System prompt from the legal prompts

## See Also

- `app/Services/AiService.php` - Main AI service using these prompts
- `config/openai.php` - OpenAI configuration
- `AI_SERVICE_USAGE.md` - General AI service usage guide


