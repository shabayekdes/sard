<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoicePdfService;
use Illuminate\Http\Request;

class InvoicePdfController extends Controller
{
    public function show(Request $request, Invoice $invoice, InvoicePdfService $pdfService)
    {
        $this->authorize('view', $invoice);

        $validated = $request->validate([
            'type' => 'required|in:tax,simplified',
            'disposition' => 'nullable|in:inline,attachment',
        ]);

        $invoice->load(['client.billingInfo', 'payments', 'currency', 'creator', 'case']);

        $type = $validated['type'];
        $disposition = $validated['disposition'] ?? 'attachment';

        return $pdfService->makeResponse($invoice, $type, $disposition);
    }
}
