# Case Creation Chat - Test Examples

## English Examples

### Example 1: Contract Dispute
```
Contract dispute case for client ABC Company against XYZ Corporation. 
We signed a service agreement on January 15, 2024. The contract value was $50,000 
and payment was due on February 1, 2024, but XYZ Corp has not paid. 
We need to file this in Riyadh Commercial Court. This is high priority 
as we need to recover the funds quickly. The opposing party's lawyer is 
Ahmed Al-Saud.
```

### Example 2: Labor Case
```
Labor dispute case for client Mohammed Ali. He was terminated from his position 
at Tech Solutions Inc. on March 10, 2024 without proper notice or severance. 
He worked there for 5 years. We need to file a wrongful termination claim 
in the Labor Court in Jeddah. This is medium priority. The case involves 
unpaid overtime and benefits.
```

### Example 3: Commercial Case
```
Commercial case for client Saudi Trading Company. We have a dispute with 
Global Imports Ltd regarding a shipment of goods that arrived damaged on 
April 5, 2024. The shipment value was 200,000 SAR. We need to file in 
Dammam Commercial Court. High priority due to financial impact. 
The goods were supposed to be delivered on March 20, 2024.
```

### Example 4: Real Estate Dispute
```
Real estate dispute for client Fatima Al-Rashid. She purchased a property 
in Riyadh from Property Developers LLC on February 1, 2024. The property 
has structural defects that were not disclosed. Purchase price was 1.5 million SAR. 
We need to file in Riyadh Real Estate Court. Medium priority. 
The defects were discovered on March 15, 2024.
```

### Example 5: Simple Case
```
I need to file a case for my client Ahmed Hassan. It's a contract breach 
case against ABC Corporation. The contract was signed on January 1, 2024. 
Payment of 100,000 SAR is overdue. File in Riyadh Commercial Court. 
High priority.
```

## Arabic Examples

### مثال 1: نزاع عقدي
```
دعوى نزاع عقدي للعميل شركة ABC ضد شركة XYZ. تم التوقيع على اتفاقية خدمة 
في 15 يناير 2024. قيمة العقد 50,000 دولار وكان الدفع مستحق في 1 فبراير 2024 
ولكن شركة XYZ لم تدفع. نحتاج لتقديم الدعوى في المحكمة التجارية بالرياض. 
هذه أولوية عالية لأننا نحتاج لاسترداد الأموال بسرعة. محامي الطرف الآخر 
هو أحمد السعود.
```

### مثال 2: قضية عمل
```
دعوى نزاع عمل للعميل محمد علي. تم فصله من منصبه في شركة Tech Solutions 
في 10 مارس 2024 دون إشعار مناسب أو تعويض. عمل هناك لمدة 5 سنوات. 
نحتاج لتقديم دعوى فصل تعسفي في محكمة العمل بجدة. أولوية متوسطة. 
القضية تتضمن ساعات عمل إضافية غير مدفوعة والمزايا.
```

### مثال 3: قضية تجارية
```
قضية تجارية للعميل شركة التجارة السعودية. لدينا نزاع مع شركة Global Imports 
بخصوص شحنة بضائع وصلت تالفة في 5 أبريل 2024. قيمة الشحنة 200,000 ريال. 
نحتاج لتقديم الدعوى في المحكمة التجارية بالدمام. أولوية عالية بسبب التأثير المالي. 
كان من المفترض تسليم البضائع في 20 مارس 2024.
```

### مثال 4: نزاع عقاري
```
نزاع عقاري للعميلة فاطمة الراشد. اشترت عقار في الرياض من شركة Property Developers 
في 1 فبراير 2024. العقار به عيوب إنشائية لم يتم الكشف عنها. سعر الشراء 
1.5 مليون ريال. نحتاج لتقديم الدعوى في محكمة العقارات بالرياض. أولوية متوسطة. 
تم اكتشاف العيوب في 15 مارس 2024.
```

### مثال 5: قضية بسيطة
```
أحتاج لتقديم دعوى لعميلي أحمد حسن. قضية إخلال بالعقد ضد شركة ABC. 
تم التوقيع على العقد في 1 يناير 2024. مبلغ 100,000 ريال متأخر الدفع. 
تقديم الدعوى في المحكمة التجارية بالرياض. أولوية عالية.
```

## What to Test

### Test 1: Client Extraction
- ✅ Does it extract client name correctly?
- ✅ Does it show in "Suggested Client" field?
- ✅ Are matching clients highlighted in dropdown?

### Test 2: Court Extraction
- ✅ Does it extract court name?
- ✅ Does it handle Arabic court names?
- ✅ Are matching courts highlighted?

### Test 3: Case Type Extraction
- ✅ Does it identify case type from description?
- ✅ Does it suggest appropriate case type?
- ✅ Are matching case types highlighted?

### Test 4: Other Information
- ✅ Title extraction
- ✅ Description extraction
- ✅ Priority detection (high/medium/low)
- ✅ Opposing party extraction
- ✅ Date extraction
- ✅ Key facts extraction

### Test 5: Missing Information
- ✅ Does it identify what's missing?
- ✅ Does it show "Missing Information" section?

## Expected AI Extractions

### From Example 1 (Contract Dispute):
- **Title**: "Contract Dispute - ABC Company vs XYZ Corporation"
- **Client**: "ABC Company"
- **Court**: "Riyadh Commercial Court"
- **Case Type**: "Contract Dispute" or "Commercial Case"
- **Priority**: "high"
- **Opposing Party**: "XYZ Corporation"
- **Key Facts**: 
  - Contract signed January 15, 2024
  - Value: $50,000
  - Payment due February 1, 2024
  - Payment not received
- **Important Dates**: 
  - Contract signed: January 15, 2024
  - Payment due: February 1, 2024

## Tips for Testing

1. **Start Simple**: Use Example 5 first to test basic extraction
2. **Test Arabic**: Try Arabic examples to verify bilingual support
3. **Test Missing Info**: Try a minimal description to see missing information detection
4. **Test Edge Cases**: 
   - No client mentioned
   - No court mentioned
   - Unclear case type
   - Multiple dates mentioned

## Common Issues to Watch For

- ❌ Client name not extracted
- ❌ Court name not recognized
- ❌ Case type not identified
- ❌ Priority not detected
- ❌ Dates not extracted
- ❌ Information hallucinated (AI adding info not in prompt)

## Success Criteria

✅ All mentioned information is extracted correctly
✅ No information is invented/hallucinated
✅ Missing information is clearly identified
✅ Suggested values help user select correct options
✅ Both English and Arabic work correctly

