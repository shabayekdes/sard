# Case Creation Examples - Real Database Data

This document contains example prompts generated from actual data in your database. Use these to test the case creation feature.

## How to Generate Examples

Run the artisan command to generate examples from your database:

```bash
php artisan cases:generate-examples --limit=10
```

This will:
- Query your actual clients, courts, and case types
- Generate realistic case creation prompts
- Save examples to `storage/app/case_creation_examples.txt`

## Example Prompts (Generated from Database)

### Example 1: Contract Dispute
```
Create a new case for client [CLIENT_NAME]. Contract dispute case against ABC Corporation. 
We signed a service agreement on [DATE]. 
The contract value was 150,000 SAR and payment was due on [DATE], 
but ABC Corporation has not paid. We need to file this in [COURT_NAME]. 
This is high priority as we need to recover the funds quickly.
```

### Example 2: Labor Case
```
Create a case for client [CLIENT_NAME]. Labor dispute case. 
Employee was terminated from Tech Solutions Inc. on [DATE] 
without proper notice or severance. He worked there for 3 years. 
We need to file a wrongful termination claim in [COURT_NAME]. 
This is medium priority. The case involves unpaid overtime and benefits.
```

### Example 3: Commercial Dispute
```
New case for client [CLIENT_NAME]. Commercial dispute with Global Imports Ltd 
regarding a shipment of goods that arrived damaged on [DATE]. 
The shipment value was 250,000 SAR. We need to file in [COURT_NAME]. 
High priority due to financial impact. The goods were supposed to be delivered on [DATE].
```

### Example 4: Real Estate Dispute
```
Create a case for client [CLIENT_NAME]. Real estate dispute. 
Client purchased a property from Property Developers LLC on [DATE]. 
The property has structural defects that were not disclosed. Purchase price was 2.5 million SAR. 
We need to file in [COURT_NAME]. Medium priority. 
The defects were discovered on [DATE].
```

## Using Real Data

The generated examples use:
- **Real client names** from your `clients` table
- **Real court names** from your `courts` table  
- **Real case types** from your `case_types` table
- **Realistic dates** (relative to current date)
- **Realistic amounts** in SAR

## Testing Tips

1. **Copy a generated example** from the command output
2. **Paste it into the chat interface** at `/chat`
3. **Watch the AI extract** client, court, and case type
4. **Verify the case is created** correctly
5. **Check the case details** match what was described

## Arabic Examples

The system also supports Arabic prompts. Example:

```
إنشاء قضية جديدة للعميل [CLIENT_NAME]. نزاع عقدي ضد شركة ABC. 
تم التوقيع على العقد في [DATE]. مبلغ 150,000 ريال مستحق في [DATE] 
ولكن لم يتم الدفع. تقديم الدعوى في [COURT_NAME]. أولوية عالية.
```

## What Gets Extracted

From each prompt, the AI should extract:
- ✅ Client name → Matches to database client
- ✅ Court name → Matches to database court
- ✅ Case type → Matches to database case type
- ✅ Title → Generated from description
- ✅ Description → Full case description
- ✅ Priority → high/medium/low
- ✅ Opposing party → If mentioned
- ✅ Important dates → Contract dates, deadlines
- ✅ Key facts → Important information

## Troubleshooting

If case creation fails, check:
1. **Client exists** in database with matching name
2. **Court exists** in database with matching name
3. **Case type exists** in database
4. **User has permission** to create cases
5. **All required fields** are provided

## See Also

- `CASE_CREATION_TEST_EXAMPLES.md` - Generic test examples
- `app/Console/Commands/GenerateCaseCreationExamples.php` - Command to generate examples
- `app/Mcp/Tools/CaseCreateTool.php` - MCP tool for case creation

