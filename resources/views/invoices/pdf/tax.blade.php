<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tax Invoice - فاتورة ضريبية</title>
    <style>
        {!! file_get_contents(public_path('css/pdf-invoice.css')) !!}
    </style>
</head>
<body>
@php
    $format = fn ($value) => number_format((float) $value, 2);
    $paymentsTotal = $invoice->payments?->sum('amount') ?? 0;
    $lastPayment = $invoice->payments?->sortByDesc('payment_date')->first();
    $paymentLabel = $lastPayment
        ? 'Payment on ' . \Carbon\Carbon::parse($lastPayment->payment_date)->format('Y-m-d H:i')
            . ' using ' . ($lastPayment->payment_method ?? '-')
        : null;
@endphp
<div class="pag-container">
    <!-- header -->
    <header class="invoice-header flex justify-content-between align-items-center gap-3">
        @if (!empty($branding['logo_url']))
            <img class="logo-img" src="{{ $branding['logo_url'] }}" alt="logo image" />
        @else
            <img class="logo-img" src="/images/logo.svg" alt="logo image" />
        @endif
        <h1 class="invoice-title flex flex-wrap text-right justify-content-end column-gap-1 m-0">
            <span>Simplified Tax Invoice</span>
            -
            <span>فاتورة ضريبية مبسطة</span>
        </h1>
    </header>
    <!-- end header -->

    <!-- invoice info -->
    <div class="invoice-info flex gap-3">
        <table class="w-6">
            <tbody>
            <tr>
                <td>CR NO</td>
                <td class="text-center break-word px-2">{{ $seller['registration_number'] ?: '-' }}</td>
                <td class="text-right">رقم السجل التجاري</td>
            </tr>
            <tr>
                <td>Tax No</td>
                <td class="text-center break-word px-2">{{ $seller['vat_number'] ?: '-' }}</td>
                <td class="text-right">الرقم الضريبي</td>
            </tr>
            <tr>
                <td>Invoice No</td>
                <td class="text-center break-word px-2">{{ $invoice->invoice_number }}</td>
                <td class="text-right">رقم الفاتورة</td>
            </tr>
            <tr>
                <td>Date</td>
                <td class="text-center break-word px-2">{{ $issued_at->format('Y-m-d') }}</td>
                <td class="text-right">تاريخ إصدار الفاتورة</td>
            </tr>
            </tbody>
        </table>
        <div class="invoice-info-divider"></div>
        <table class="w-6">
            <tbody>
            <tr>
                <td>Center</td>
                <td class="text-center break-word px-2">{{ $seller['name'] }}</td>
                <td class="text-right">مركز</td>
            </tr>
            <tr>
                <td>Branch</td>
                <td class="text-center break-word px-2">Main</td>
                <td class="text-right">فرع</td>
            </tr>
            <tr>
                <td>Address</td>
                <td class="text-center break-word px-2">{{ $seller['address'] ?: '-' }}</td>
                <td class="text-right">العنوان</td>
            </tr>
            <tr>
                <td>Mobile</td>
                <td class="text-center break-word px-2">{{ $seller['phone'] ?: '-' }}</td>
                <td class="text-right">موبايل</td>
            </tr>
            </tbody>
        </table>
    </div>
    <!-- end invoice info -->

    <!-- customer info -->
    <div class="customer-info flex gap-2 align-items-start">
        <div class="w-6 table-container">
            <table class="app-table">
                <thead>
                <tr>
                    <th colspan="3">
                        <div class="flex gap-2 align-items-center justify-content-between table-header">
                            <span>Case Information</span>
                            <span class="text-right">بيانات القضية</span>
                        </div>
                    </th>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <td>Case ID</td>
                    <td class="text-center break-word px-2">{{ $invoice->case?->case_id ?: '-' }}</td>
                    <td class="text-right">رقم القضية</td>
                </tr>
                <tr>
                    <td>Case No.</td>
                    <td class="text-center break-word px-2">{{ $invoice->case?->case_number ?: '-' }}</td>
                    <td class="text-right">رقم الدعوى</td>
                </tr>
                <tr>
                    <td>Title</td>
                    <td class="text-center break-word px-2">{{ $invoice->case?->title ?: '-' }}</td>
                    <td class="text-right">عنوان القضية</td>
                </tr>
                <tr>
                    <td>File No.</td>
                    <td class="text-center break-word px-2">{{ $invoice->case?->file_number ?: '-' }}</td>
                    <td class="text-right">رقم الملف</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="w-6 table-container">
            <table class="app-table">
                <thead>
                <tr>
                    <th colspan="3" class="table-header">
                        <div class="flex gap-2 align-items-center justify-content-between">
                            <span>Customer Information</span>
                            <span class="text-right">بيانات العميل</span>
                        </div>
                    </th>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <td>Name</td>
                    <td class="text-center break-word px-2">{{ $customer['name'] ?: '-' }}</td>
                    <td class="text-right">الأسم</td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td class="text-center break-word px-2">{{ $customer['phone'] ?: '-' }}</td>
                    <td class="text-right">رقم الهاتف</td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td class="text-center break-word px-2">{{ $customer['address'] ?: '-' }}</td>
                    <td class="text-right">العنوان</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- end customer info -->

    <!-- line items -->
    <div class="mb-8">
        <table class="w-full border border-gray-200 items-table-rtl">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 desc-right text-sm font-semibold text-gray-900 border-b">Description</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-900 border-b">Qty</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900 border-b">Rate</th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900 border-b">Amount</th>
                </tr>
            </thead>
            <tbody>
                @if ($items && $items->count() > 0)
                    @foreach ($items as $item)
                        <tr class="border-b {{ ($item['type'] ?? '') === 'expense' ? 'bg-orange-50' : '' }}">
                            <td class="px-4 py-4 text-sm text-gray-900 desc-right">
                                <div class="space-y-1">
                                    <div>{{ $item['description'] }}</div>
                                    @if (($item['type'] ?? '') === 'expense')
                                        <div class="text-xs text-orange-600 flex items-center">
                                            <span class="bg-orange-100 px-2 py-1 rounded text-orange-700 font-medium">Expense</span>
                                            @if (!empty($item['expense_date']))
                                                <span class="ml-2">{{ \Carbon\Carbon::parse($item['expense_date'])->format('Y-m-d') }}</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if (($item['type'] ?? '') === 'time')
                                        <div class="text-xs text-blue-600 flex items-center">
                                            <span class="bg-blue-100 px-2 py-1 rounded text-blue-700 font-medium">Time Entry</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900 text-center">{{ $format($item['quantity']) }}</td>
                            <td class="px-4 py-4 text-sm text-gray-900 text-right">{{ $format($item['unit_price']) }} {{ $currency_code }}</td>
                            <td class="px-4 py-4 text-sm text-gray-900 text-right">{{ $format($item['line_total']) }} {{ $currency_code }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-500">No items found</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    <!-- end line items -->

    <!-- products summary -->
    <div class="summary-info flex gap-2">
        <div class="qr-container table-container flex align-items-center justify-content-center p-2">
            <img src="{{ $qr_code }}" alt="qr code image" class="w-full" />
        </div>

        <div class="flex-grow-1">
            <div class="table-container">
                <table class="app-table">
                    <tbody>
                    <tr>
                        <td class="text-right"><span class="sar-symbol">{{ $format($totals['subtotal']) }}</span></td>
                        <th class="text-right">
                            (Amount excl. VAT) الاجمالي غير شامل ضريبة القيمة المضافة
                        </th>
                    </tr>
                    <tr>
                        <td class="text-right"><span class="sar-symbol">0</span></td>
                        <th class="text-right">(Discount) الخصم</th>
                    </tr>
                    <tr>
                        <td class="text-right"><span class="sar-symbol">{{ $format($totals['vat_total']) }}</span></td>
                        <th class="text-right">(VAT) ضريبة القمية المضافة</th>
                    </tr>
                    <tr>
                        <td class="text-right"><span class="sar-symbol">{{ $format($totals['grand_total']) }}</span></td>
                        <th class="text-right">
                            (Amount including VAT) الإجمالي شامل الضريبة
                        </th>
                    </tr>
                    <tr>
                        <td class="text-right"><span class="sar-symbol">{{ $format($paymentsTotal) }}</span></td>
                        <th class="text-right">(Payments) المدفوعات</th>
                    </tr>
                    @if ($paymentLabel)
                        <tr>
                            <th colspan="2" class="text-right font-semibold">
                                {{ $paymentLabel }}
                            </th>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-right">{{ $paymentLabel ?: '-' }}</td>
                        <td class="text-right">
                            شروط الدفع (Payment Terms)
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- end products summary -->

    <!-- thanks -->
    <div class="terms-conditions-header flex gap-2 text-center">
        <div class="thanks-message w-6 flex flex-column align-items-center justify-content-center">
            <p class="m-0">شكرا لزيارتكم</p>
            <p class="m-0">Thank you for your visit</p>
        </div>
        <div class="w-6 flex flex-column align-items-center justify-content-center">
            <p class="m-0">ملاحظات الفاتورة (Invoice Notes)</p>
            <p class="m-0">{{ $invoice->notes ?: '-' }}</p>
        </div>
    </div>
    <!-- end thanks -->
</div>
</body>
</html>
