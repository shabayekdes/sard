# Case Creation Chat UI Guide

## Overview

The Case Creation Chat UI allows users to create cases by describing them in natural language. The AI extracts structured information and helps create the case with minimal manual input.

## Access

Navigate to: `/cases/create-chat`

Or add a button in your cases list:
```tsx
<Button onClick={() => router.visit(route('cases.create-chat'))}>
  Create Case with AI
</Button>
```

## Features

### 1. **Natural Language Input**
- Describe your case in plain English or Arabic
- AI automatically extracts structured information
- Supports both languages seamlessly

### 2. **Context Selectors**
- Optional pre-selection of Client, Court, and Case Type
- Helps AI provide more accurate suggestions
- Can be set before or during the conversation

### 3. **Real-time Extraction**
- AI analyzes your description immediately
- Extracts: title, description, priority, facts, dates, opposing party
- Identifies missing information

### 4. **Review & Create Dialog**
- Review all extracted information
- Fill in required fields (Client, Case Type, Status, Court)
- Create case with one click

## Usage Flow

1. **Optional**: Select context (Client, Court, Case Type) from dropdowns
2. **Describe your case** in the chat input
3. **AI extracts** structured information
4. **Review** extracted information in the dialog
5. **Fill required fields** (Client, Case Type, Status, Court)
6. **Click "Create Case"** to save

## Example Prompts

### English:
```
"Contract dispute case for client ABC Company against XYZ Corp. 
Contract signed January 15, 2024. Payment of $50,000 due February 1 
but not received. Need to file in Riyadh Commercial Court. High priority."
```

### Arabic:
```
"دعوى نزاع عقدي للعميل أحمد محمد ضد شركة XYZ. تم التوقيع على العقد 
في 15 يناير 2024. المبلغ 200,000 ريال مستحق في 1 فبراير ولكن لم يتم الدفع. 
المحكمة: المحكمة التجارية بالرياض. أولوية عالية."
```

## What Gets Extracted

- **Title**: Clear, concise case title
- **Description**: Detailed case description
- **Suggested Priority**: low, medium, or high
- **Key Facts**: Important facts as bullet points
- **Opposing Party**: If mentioned
- **Important Dates**: Filing dates, deadlines, etc.
- **Missing Information**: What additional details are needed

## API Endpoints

### Generate Case Information
```
POST /cases/generate-from-prompt
Body: {
    "prompt": "Case description...",
    "client_id": 123,  // optional
    "court_id": 45,    // optional
    "case_type_id": 5  // optional
}
```

### Create Case
```
POST /cases/create-from-prompt
Body: {
    "prompt": "Case description...",
    "client_id": 123,        // required
    "case_type_id": 5,       // required
    "case_status_id": 1,     // required
    "court_id": 45           // required
}
```

## Safety Features

- **No Hallucinations**: Only uses information explicitly mentioned
- **Missing Info Detection**: Clearly identifies what's missing
- **Validation**: Requires manual selection of required fields
- **Review Step**: Generated information is suggested, not automatically applied
- **Permission Checks**: All operations respect user permissions

## Integration

### Add to Navigation Menu

```tsx
<MenuItem onClick={() => router.visit(route('cases.create-chat'))}>
  Create Case with AI
</MenuItem>
```

### Add Button to Cases List

```tsx
<Button onClick={() => router.visit(route('cases.create-chat'))}>
  <MessageSquare className="h-4 w-4 mr-2" />
  Create with AI
</Button>
```

## Technical Details

- **Component**: `resources/js/pages/cases/create-chat.tsx`
- **Controller**: `app/Http/Controllers/CaseController.php`
- **Service**: `app/Services/CaseCreationService.php`
- **Routes**: Added to `routes/web.php`

## Permissions Required

- `create-cases` - To access the chat interface and create cases

## See Also

- `CASE_CREATION_FROM_PROMPT.md` - Service layer documentation
- `app/Services/CaseCreationService.php` - Main service class
- `app/Services/LegalPrompts.php` - Legal prompts used

