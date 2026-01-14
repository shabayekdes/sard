# Case Creation Examples - Real Database Data

## Quick Start

Run this command to generate examples from your actual database:

```bash
php artisan cases:generate-examples --limit=10
```

For a specific user's data:
```bash
php artisan cases:generate-examples --limit=10 --user-id=2
```

## Generated Examples (From Your Database)

The command generates realistic prompts using:
- ✅ **Real client names** from your database
- ✅ **Real court names** from your database
- ✅ **Real case types** from your database
- ✅ **Realistic dates** and amounts

## Example Output

Based on your database, here are example prompts you can use:

### Example 1: Contract Dispute
```
Create a new case for client Lisa Anderson. Contract dispute case against ABC Corporation. 
We signed a service agreement on November 12, 2025. 
The contract value was 150,000 SAR and payment was due on December 12, 2025, 
but ABC Corporation has not paid. We need to file this in Family Court North #8. 
This is high priority as we need to recover the funds quickly.
```

### Example 2: Commercial Dispute
```
New case for client William Garcia. Commercial dispute with Global Imports Ltd 
regarding a shipment of goods that arrived damaged on December 22, 2025. 
The shipment value was 250,000 SAR. We need to file in Commercial Court Plaza #9. 
High priority due to financial impact. The goods were supposed to be delivered on November 12, 2025.
```

### Example 3: Simple Case Filing
```
I need to file a case for my client Jennifer Martinez. It's a Case Filing case 
against XYZ Corporation. The contract was signed on September 12, 2025. 
Payment of 100,000 SAR is overdue. File in Criminal Court East #1. High priority.
```

### Example 4: Labor Dispute
```
Create a case for client Robert Taylor. Labor dispute case. 
Employee was terminated from Tech Solutions Inc. on December 12, 2025 
without proper notice or severance. He worked there for 3 years. 
We need to file a wrongful termination claim in Superior Court Main #5. 
This is medium priority. The case involves unpaid overtime and benefits.
```

## How to Use

1. **Run the command** to generate examples:
   ```bash
   php artisan cases:generate-examples --limit=5
   ```

2. **Copy a prompt** from the output

3. **Paste in chat** at `/chat` or `/cases/create-chat`

4. **Watch AI extract** information and create the case

5. **Verify** the case was created correctly

## Testing in Main Chat

You can use these examples in the main chat interface:

1. Go to `/chat`
2. Type: `Create a case for client [CLIENT_NAME]...`
3. The AI will detect it's a case creation request
4. Case will be created automatically

## Testing in Case Creation Chat

1. Go to `/cases/create-chat`
2. Paste one of the generated examples
3. Review extracted information
4. Select required fields if needed
5. Create the case

## What the AI Extracts

From each prompt, the system extracts:
- **Client**: Matches to database client by name
- **Court**: Matches to database court by name
- **Case Type**: Identifies from description
- **Title**: Generated from description
- **Description**: Full case details
- **Priority**: high/medium/low
- **Opposing Party**: If mentioned
- **Dates**: Contract dates, deadlines
- **Key Facts**: Important information

## Files Generated

- **Console Output**: Examples displayed in terminal
- **Text File**: `storage/app/case_creation_examples.txt` - All examples saved

## Notes

- Examples use **real data** from your database
- Client names, courts, and case types are **actual values**
- Dates are **relative to current date** (realistic)
- Amounts are in **SAR** (Saudi Riyal)
- Examples work in **both English and Arabic**

## See Also

- `CASE_CREATION_TEST_EXAMPLES.md` - Generic test examples
- `CASE_CREATION_FROM_PROMPT.md` - Service documentation
- `app/Console/Commands/GenerateCaseCreationExamples.php` - Command source

