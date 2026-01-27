<?php

namespace App\Services;

use App\Models\CompanyProfile;
use App\Models\Invoice;
use Illuminate\Support\Carbon;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use Spatie\LaravelPdf\Facades\Pdf;

class InvoicePdfService
{
    public function makeResponse(Invoice $invoice, string $type, string $disposition)
    {
        $view = $type === 'simplified'
            ? 'invoices.pdf.simplified'
            : 'invoices.pdf.tax';

        $data = $this->buildViewData($invoice, $type);

        $filename = sprintf('%s-%s.pdf', $type, $invoice->invoice_number);

        $pdf = Pdf::view($view, $data)
            ->format('a4')
            ->name($filename);

        return $disposition === 'inline'
            ? $pdf->inline($filename)
            : $pdf->download($filename);
    }

    private function buildViewData(Invoice $invoice, string $type): array
    {
        $companyProfile = CompanyProfile::withPermissionCheck()
            ->where('created_by', $invoice->created_by)
            ->first();

        $client = $invoice->client;
        $billingInfo = $client?->billingInfo;

        $defaultVatRate = (float) config('invoice_pdf.default_vat_rate', 15);
        $currencyCode = $invoice->currency?->code ?? config('invoice_pdf.currency_code', 'SAR');

        $items = collect($invoice->line_items ?? [])->map(function (array $item) use ($defaultVatRate) {
            $quantity = (float) ($item['quantity'] ?? 1);
            $unitPrice = (float) ($item['rate'] ?? 0);
            $taxableAmount = (float) ($item['amount'] ?? ($quantity * $unitPrice));
            $vatRate = isset($item['vat_rate']) ? (float) $item['vat_rate'] : $defaultVatRate;
            $vatAmount = isset($item['vat_amount'])
                ? (float) $item['vat_amount']
                : round($taxableAmount * $vatRate / 100, 2);

            return [
                'description' => $item['description'] ?? '',
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'type' => $item['type'] ?? 'manual',
                'taxable_amount' => $taxableAmount,
                'vat_rate' => $vatRate,
                'vat_amount' => $vatAmount,
                'line_total' => $taxableAmount + $vatAmount,
            ];
        });

        $subtotal = $invoice->subtotal !== null ? (float) $invoice->subtotal : $items->sum('taxable_amount');
        $vatTotal = $invoice->tax_amount !== null ? (float) $invoice->tax_amount : $items->sum('vat_amount');
        $grandTotal = $invoice->total_amount !== null ? (float) $invoice->total_amount : ($subtotal + $vatTotal);

        $issuedAt = $this->resolveIssuedAt($invoice);

        $sellerVatNumber = config('invoice_pdf.seller_vat_number')
            ?: ($companyProfile?->registration_number ?? '');

        $logoPath = getSetting('logoDark', null, $invoice->created_by)
            ?: getSetting('logoLight', null, $invoice->created_by);
        $logoUrl = $this->buildLogoDataUri($logoPath);

        $qrCode = $this->buildQrCodeDataUri(
            $companyProfile?->name ?? ($invoice->creator?->name ?? ''),
            $sellerVatNumber,
            $issuedAt->toIso8601String(),
            $grandTotal,
            $vatTotal
        );

        return [
            'type' => $type,
            'invoice' => $invoice,
            'currency_code' => $currencyCode,
            'issued_at' => $issuedAt,
            'branding' => [
                'logo_url' => $logoUrl,
            ],
            'seller' => [
                'name' => $companyProfile?->name ?? ($invoice->creator?->name ?? ''),
                'address' => $companyProfile?->address ?? '',
                'vat_number' => $sellerVatNumber,
                'registration_number' => $companyProfile?->registration_number ?? '',
                'phone' => $companyProfile?->phone ?? '',
            ],
            'customer' => [
                'name' => $client?->name ?? '',
                'address' => $billingInfo?->billing_address ?? $client?->address ?? '',
                'vat_number' => $client?->vat_number ?? $client?->tax_id ?? '',
                'phone' => $client?->phone ?? '',
            ],
            'items' => $items,
            'totals' => [
                'subtotal' => $subtotal,
                'vat_total' => $vatTotal,
                'grand_total' => $grandTotal,
            ],
            'qr_code' => $qrCode,
        ];
    }

    private function resolveIssuedAt(Invoice $invoice): Carbon
    {
        $invoiceDate = $invoice->invoice_date
            ? Carbon::parse($invoice->invoice_date->format('Y-m-d'))
            : null;

        $timeSource = $invoice->created_at ?? now();

        return $invoiceDate
            ? $invoiceDate->setTimeFrom($timeSource)
            : Carbon::parse($timeSource);
    }

    private function buildQrCodeDataUri(
        string $sellerName,
        string $vatNumber,
        string $timestamp,
        float $invoiceTotal,
        float $vatTotal
    ): string
    {
        $scale = max(1, (int) round((int) config('invoice_pdf.qr.size', 140) / 25));

        return GenerateQrCode::fromArray([
            new Seller($sellerName),
            new TaxNumber($vatNumber),
            new InvoiceDate($timestamp),
            new InvoiceTotalAmount(number_format($invoiceTotal, 2, '.', '')),
            new InvoiceTaxAmount(number_format($vatTotal, 2, '.', '')),
        ])->render([
            'scale' => $scale,
            'quietzoneSize' => (int) config('invoice_pdf.qr.margin', 1),
        ]);
    }

    private function buildLogoDataUri(?string $logoPath): ?string
    {
        if (!$logoPath || str_starts_with($logoPath, 'http://') || str_starts_with($logoPath, 'https://')) {
            return null;
        }

        $relativePath = ltrim($logoPath, '/');
        $candidates = [
            public_path($relativePath),
            public_path('storage/' . ltrim($relativePath, 'storage/')),
        ];

        foreach ($candidates as $path) {
            if (is_file($path)) {
                $mime = mime_content_type($path) ?: 'image/png';
                $data = base64_encode(file_get_contents($path));
                return "data:{$mime};base64,{$data}";
            }
        }

        return null;
    }
}
