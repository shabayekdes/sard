<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Simplified Tax Invoice - فاتورة ضريبية مبسطة</title>
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
                                    <span>Vehicle Information</span>
                                    <span class="text-right">بيانات المركبة</span>
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>Name</td>
                            <td class="break-word px-2">
                                <div class="vehicle-name flex align-items-center justify-content-center gap-1">
                                    <div class="vehicle-logo"></div>
                                    {{ '-' }}
                                </div>
                            </td>
                            <td class="text-right">الأسم</td>
                        </tr>
                        <tr>
                            <td>Plate No.</td>
                            <td class="text-center break-word px-2">
                                {{ '-' }}
                            </td>
                            <td class="text-right">رقم اللوحة</td>
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

        <!-- products table -->
        <div class="products-table table-container">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>
                            <div>الإجمالي</div>
                            <div>Total</div>
                        </th>
                        <th>
                            <div>الضمان</div>
                            <div>Warranty</div>
                        </th>
                        <th>
                            <div>الضرائب</div>
                            <div>Tax</div>
                        </th>
                        <th>
                            <div>الخصم ٪</div>
                            <div>Discount%</div>
                        </th>
                        <th>
                            <div>خصومات</div>
                            <div>Discount</div>
                        </th>
                        <th>
                            <div>السعر</div>
                            <div>Price</div>
                        </th>
                        <th>
                            <div>الكمية</div>
                            <div>qty</div>
                        </th>
                        <th>
                            <div>النوع</div>
                            <div>Type</div>
                        </th>
                        <th>
                            <div>SKU</div>
                        </th>
                        <th>
                            <div>البيانات</div>
                            <div>Description</div>
                        </th>
                        <th>
                            <div>الرقم</div>
                            <div>NO</div>
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($items as $index => $item)
                        <tr>
                            <td><span class="sar-symbol">{{ $format($item['line_total']) }}</span></td>
                            <td>-</td>
                            <td><span class="sar-symbol">{{ $format($item['vat_amount']) }}</span></td>
                            <td><span class="sar-symbol">0</span></td>
                            <td><span class="sar-symbol">0</span></td>
                            <td><span class="sar-symbol">{{ $format($item['unit_price']) }}</span></td>
                            <td>{{ $format($item['quantity']) }}</td>
                            <td>-</td>
                            <td>-</td>
                            <td>{{ $item['description'] }}</td>
                            <td>{{ $index + 1 }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- end products table -->

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
