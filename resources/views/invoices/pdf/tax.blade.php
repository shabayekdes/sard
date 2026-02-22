<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>فاتورة ضريبية مبسطة</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        {!! file_get_contents(public_path('css/pdf-invoice.css')) !!}
    </style>
</head>
<body>
@php
    $formatNum = fn ($value) => number_format((float) $value, 2);
    $formatMoney = fn ($value) => formatCurrencyForCompany((float) $value, $invoice->created_by);
    $paymentsTotal = $invoice->payments?->sum('amount') ?? 0;
    $amountDue = ($totals['grand_total'] ?? 0) - $paymentsTotal;
    $dueDateFormatted = $invoice->due_date ? $invoice->due_date->format('Y-m-d') : $issued_at->format('Y-m-d');
    $invoiceDateFormatted = $issued_at->format('Y-m-d');
    $taxRate = (float) config('invoice_pdf.default_vat_rate', 15);
@endphp
<div class="pag-container">
    <!-- Top card: title + invoice number, case, dates (same as show.tsx first card) -->
    <div class="pdf-card pdf-card-header-block">
        <div class="pdf-card-content">
            <div class="flex justify-content-between align-items-start gap-3">
                <div class="pdf-header-left">
                    @if (($customer['business_type'] ?? '') === 'b2b')
                        <h1 class="pdf-main-title">فاتورة ضريبية</h1>
                    @else
                        <h1 class="pdf-main-title">فاتورة ضريبية مبسطة</h1>
                    @endif
                    <p class="pdf-meta-line"><strong>رقم الفاتورة:</strong> {{ $invoice->invoice_number }}</p>
                    @if ($invoice->case)
                        <p class="pdf-meta-line"><strong>عنوان
                                القضية:</strong> {{ $invoice->case->case_id ? $invoice->case->case_id . ' - ' . $invoice->case->title : $invoice->case->title }}
                        </p>
                    @endif
                    <p class="pdf-meta-line pdf-dates">
                        <span><strong>تاريخ الفاتورة:</strong> {{ $invoiceDateFormatted }}</span>
                        <span><strong>تاريخ الاستحقاق:</strong> {{ $dueDateFormatted }}</span>
                    </p>
                </div>
                @if (!empty($branding['logo_url']))
                    <img class="logo-img logo-top-right" src="{{ $branding['logo_url'] }}" alt="الشعار" />
                @else
                    <img class="logo-img logo-top-right" src="/images/logo.svg" alt="الشعار" />
                @endif
            </div>
        </div>
    </div>

    <!-- فاتورة من / فاتورة الى -->
    <div class="pdf-two-cards flex gap-3 mb-6">
        <div class="pdf-card flex-grow-1">
            <div class="pdf-card-content">
                <h3 class="pdf-card-title">فاتورة من</h3>
                <p class="pdf-card-name">{{ $seller['name'] ?: '-' }}</p>
                <p class="pdf-card-row"><span class="pdf-label">العنوان:</span> {{ $seller['address'] ?: '-' }}</p>
                <p class="pdf-card-row"><span class="pdf-label">رقم الهاتف:</span> {{ $seller['phone'] ?: '-' }}</p>
                <p class="pdf-card-row"><span class="pdf-label">البريد الإلكتروني:</span> {{ $seller['email'] ?: '-' }}
                </p>
                <p class="pdf-card-row"><span class="pdf-label">الرقم الضريبي:</span> {{ $seller['tax_number'] ?: '-' }}
                </p>
                <p class="pdf-card-row"><span class="pdf-label">السجل التجاري:</span> {{ $seller['cr'] ?: '-' }}</p>
            </div>
        </div>
        <div class="pdf-card flex-grow-1">
            <div class="pdf-card-content">
                <h3 class="pdf-card-title">فاتورة الى</h3>
                <p class="pdf-card-name">{{ $customer['name'] ?: '-' }}</p>
                <p class="pdf-card-row"><span class="pdf-label">العنوان:</span> {{ $customer['address'] ?: '-' }}</p>
                <p class="pdf-card-row"><span class="pdf-label">رقم الهاتف:</span> {{ $customer['phone'] ?: '-' }}</p>
                <p class="pdf-card-row"><span
                            class="pdf-label">البريد الإلكتروني:</span> {{ $customer['email'] ?: '-' }}</p>
                @if (($customer['business_type'] ?? '') === 'b2b')
                    <p class="pdf-card-row"><span
                                class="pdf-label">الرقم الضريبي:</span> {{ $customer['vat_number'] ?: '-' }}</p>
                    <p class="pdf-card-row"><span
                                class="pdf-label">السجل التجاري:</span> {{ $customer['cr_number'] ?: '-' }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- جدول البنود -->
    <div class="mb-6">
        <table class="pdf-table pdf-table-rtl">
            <thead>
            <tr>
                <th class="pdf-th pdf-th-desc">البيان</th>
                <th class="pdf-th pdf-th-num">الكمية</th>
                <th class="pdf-th pdf-th-num">سعر الوحدة</th>
                <th class="pdf-th pdf-th-num">الإجمالي الفرعي<br>بدون الضريبة</th>
                <th class="pdf-th pdf-th-num">الضريبة</th>
                <th class="pdf-th pdf-th-num">الإجمالي شامل<br>الضريبة</th>
            </tr>
            </thead>
            <tbody>
            @if ($items && $items->count() > 0)
                @foreach ($items as $item)
                    <tr class="pdf-tr">
                        <td class="pdf-td pdf-td-desc">{{ $item['description'] }}</td>
                        <td class="pdf-td pdf-td-num">{{ $formatNum($item['quantity']) }}</td>
                        <td class="pdf-td pdf-td-num">{{ $formatMoney($item['unit_price']) }}</td>
                        <td class="pdf-td pdf-td-num">{{ $formatMoney($item['taxable_amount']) }}</td>
                        <td class="pdf-td pdf-td-num">{{ $formatMoney($item['vat_amount']) }}</td>
                        <td class="pdf-td pdf-td-num">{{ $formatMoney($item['line_total']) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="pdf-td text-center text-gray-500">لا توجد بنود</td>
                </tr>
            @endif
            </tbody>
        </table>
    </div>

    <!-- الإجماليات + رمز QR -->
    <div class="pdf-card pdf-totals-card">
        <div class="pdf-card-content pdf-totals-qr-row flex justify-content-between align-items-end gap-4">
            <div class="pdf-qr-wrap pdf-qr-next-to-totals">
                <img class="pdf-qr-img" src="{{ $qr_code }}" alt="رمز QR" />
            </div>
            <div class="pdf-totals-wrap pdf-totals-rtl">
                <div class="pdf-totals-row">
                    <span class="pdf-totals-label">المجموع الفرعي</span>
                    <span class="pdf-totals-value">{{ $formatMoney($totals['subtotal']) }}</span>
                </div>
                <div class="pdf-totals-row">
                    <span class="pdf-totals-label">قيمة الضريبة ({{ (int) $taxRate }}%)</span>
                    <span class="pdf-totals-value">{{ $formatMoney($totals['vat_total']) }}</span>
                </div>
                <div class="pdf-totals-divider"></div>
                <div class="pdf-totals-row pdf-totals-row-main">
                    <span class="pdf-totals-label">إجمالي الفاتورة (شامل الضريبة)</span>
                    <span class="pdf-totals-value">{{ $formatMoney($totals['grand_total']) }}</span>
                </div>
                <div class="pdf-totals-divider"></div>
                <div class="pdf-totals-row">
                    <span class="pdf-totals-label">المبلغ المدفوع</span>
                    <span class="pdf-totals-value">{{ $formatMoney($paymentsTotal) }}</span>
                </div>
                <div class="pdf-totals-row">
                    <span class="pdf-totals-label">المبلغ المتبقي</span>
                    <span class="pdf-totals-value">{{ $formatMoney($amountDue) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- الشروط والملاحظات -->
    <div class="pdf-two-cards flex gap-3 mb-6">
        <div class="pdf-card flex-grow-1">
            <div class="pdf-card-header pdf-card-header-small">
                <h4 class="pdf-card-title-small">الشروط</h4>
            </div>
            <div class="pdf-card-content">
                <p class="pdf-muted-text">{{ $terms ?? 'صافي 30 يوماً. تطبق رسوم تأخير بنسبة 1.5% شهرياً.' }}</p>
            </div>
        </div>
        <div class="pdf-card flex-grow-1">
            <div class="pdf-card-header pdf-card-header-small">
                <h4 class="pdf-card-title-small">ملاحظات</h4>
            </div>
            <div class="pdf-card-content">
                <p class="pdf-muted-text">{{ $invoice->notes ?: 'نشكركم على تعاملكم. يرجى السداد في الموعد المحدد.' }}</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
