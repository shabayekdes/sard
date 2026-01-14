# Case Creation from Prompt Guide

This guide explains how to use AI prompts to create cases from natural language descriptions.

## Overview

The `CaseCreationService` uses AI to extract structured case information from natural language prompts, making it easier to create cases by describing them in plain language.

## Features

- **Natural Language Input**: Describe your case in plain language
- **Structured Extraction**: AI extracts title, description, priority, facts, dates, etc.
- **Saudi Law Support**: Automatically applies Saudi legal context when relevant
- **Missing Information Detection**: Identifies what information is still needed
- **Safe Parsing**: Only uses information explicitly mentioned, no hallucinations

## Usage

### Basic Usage

```php
use App\Services\CaseCreationService;

$service = new CaseCreationService();

$result = $service->generateFromPrompt(
    "I need to file a contract dispute case for my client ABC Company. 
     The opposing party is XYZ Corp. We signed the contract on 2024-01-15 
     and payment was due on 2024-02-01 but they haven't paid. 
     This is urgent and needs to be filed in Riyadh Commercial Court.",
    [
        'client_id' => 123,
        'court_id' => 45,
    ]
);

// Result contains:
// - parsed: Structured case information
// - raw_response: Full AI response
// - suggestions: What's still needed
```

### Example Prompts

**English:**
```
"Contract dispute case for client John Smith against ABC Corporation. 
Contract signed January 15, 2024. Payment of $50,000 was due February 1 
but not received. Need to file in Riyadh Commercial Court. High priority."
```

**Arabic:**
```
"دعوى نزاع عقدي للعميل أحمد محمد ضد شركة XYZ. تم التوقيع على العقد 
في 15 يناير 2024. المبلغ المستحق 200,000 ريال كان مستحق الدفع في 
1 فبراير ولكن لم يتم الدفع. المحكمة: المحكمة التجارية بالرياض. أولوية عالية."
```

### Create Case from Generated Information

```php
$service = new CaseCreationService();

// Step 1: Generate information from prompt
$result = $service->generateFromPrompt($userPrompt, $context);

// Step 2: Create the case
$case = $service->createCase($result['parsed'], [
    'client_id' => 123,
    'case_type_id' => 5,
    'case_status_id' => 1,
    'court_id' => 45,
    'created_by' => auth()->id(),
]);
```

## What Gets Extracted

The AI extracts:

- **Case Title**: Clear, concise title
- **Description**: Detailed case description
- **Suggested Case Type**: If identifiable from description
- **Suggested Priority**: low, medium, or high
- **Key Facts**: Important facts as bullet points
- **Opposing Party**: If mentioned
- **Important Dates**: Filing dates, deadlines, etc.
- **Missing Information**: What additional details are needed

## Example Response

```json
{
    "success": true,
    "data": {
        "raw_response": "Case Title: Contract Dispute - ABC Company vs XYZ Corp\n\nDescription: ...",
        "parsed": {
            "title": "Contract Dispute - ABC Company vs XYZ Corp",
            "description": "Contract dispute case...",
            "suggested_case_type": "Contract Dispute",
            "suggested_priority": "high",
            "key_facts": [
                "Contract signed on 2024-01-15",
                "Payment of $50,000 due on 2024-02-01",
                "Payment not received"
            ],
            "opposing_party": "XYZ Corp",
            "important_dates": [
                "Contract signed: 2024-01-15",
                "Payment due: 2024-02-01"
            ],
            "missing_information": [
                "Contract document reference",
                "Payment terms details"
            ]
        },
        "suggestions": [
            "Case type selection is required",
            "Court selection is required"
        ]
    }
}
```

## API Endpoints

### Generate Case Information
```
POST /api/cases/generate-from-prompt
Body: {
    "prompt": "Case description...",
    "client_id": 123,  // optional
    "court_id": 45,    // optional
    "case_type_id": 5  // optional
}
```

### Create Case from Prompt
```
POST /api/cases/create-from-prompt
Body: {
    "prompt": "Case description...",
    "client_id": 123,        // required
    "case_type_id": 5,       // required
    "case_status_id": 1,     // required
    "court_id": 45           // required
}
```

## Integration with Forms

You can integrate this into your case creation form:

1. User types case description in a textarea
2. Click "Generate from Description" button
3. AI extracts structured information
4. Form fields auto-populate with extracted data
5. User reviews and adjusts
6. Submit to create case

## Safety Features

- **No Hallucinations**: Only uses information explicitly mentioned
- **Missing Info Detection**: Clearly identifies what's missing
- **Validation**: Still requires manual selection of required fields (client, type, status, court)
- **Review Step**: Generated information is suggested, not automatically applied

## See Also

- `app/Services/CaseCreationService.php` - Main service class
- `app/Http/Controllers/ExampleCaseCreationController.php` - Example usage
- `app/Services/LegalPrompts.php` - Legal prompts used
- `app/Services/AiService.php` - AI service layer

