<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoicePdfService;
use Illuminate\Http\Request;

class InvoicePdfController extends Controller
{
    public function show(Request $request, Invoice $invoice, InvoicePdfService $pdfService)
    {
        $validated = $request->validate([
            'type' => 'required|in:tax,simplified',
            'disposition' => 'nullable|in:inline,attachment',
        ]);

        $invoice = Invoice::withPermissionCheck()
            ->with(['client.billingInfo', 'payments', 'currency', 'creator', 'case'])
            ->findOrFail($invoice->id);

        $type = $validated['type'];
        $disposition = $validated['disposition'] ?? 'attachment';

        return $pdfService->makeResponse($invoice, $type, $disposition);
    }
}
