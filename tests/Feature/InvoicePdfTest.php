<?php

use App\Models\Client;
use App\Models\CompanyProfile;
use App\Models\Invoice;
use App\Models\User;
use Spatie\LaravelPdf\Facades\Pdf;

function createInvoiceFor(User $user): Invoice
{
    $client = Client::create([
        'name' => 'Test Client',
        'created_by' => $user->id,
    ]);

    CompanyProfile::create([
        'name' => 'Test Seller',
        'registration_number' => 'VAT-123456789',
        'address' => 'Riyadh, KSA',
        'created_by' => $user->id,
    ]);

    $invoice = Invoice::create([
        'created_by' => $user->id,
        'client_id' => $client->id,
        'subtotal' => 100,
        'tax_amount' => 15,
        'total_amount' => 115,
        'status' => 'draft',
        'invoice_date' => now()->toDateString(),
        'due_date' => now()->addDays(7)->toDateString(),
    ]);

    $invoice->lineItems()->create([
        'type' => 'manual',
        'description' => 'Consulting',
        'quantity' => 1,
        'rate' => 100,
        'amount' => 100,
        'sort_order' => 0,
        'vat_rate' => 15,
        'vat_amount' => 15,
    ]);

    return $invoice;
}

test('tax invoice pdf uses tax template and attachment disposition', function () {
    Pdf::fake();

    $user = User::factory()->create(['type' => 'superadmin']);
    $invoice = createInvoiceFor($user);

    $this->actingAs($user)
        ->get("/invoices/{$invoice->id}/pdf?type=tax")
        ->assertOk();

    Pdf::assertRespondedWithPdf(function ($pdf) {
        return $pdf->viewName === 'invoices.pdf.tax' && $pdf->isDownload();
    });
});

test('simplified invoice pdf uses simplified template and inline disposition', function () {
    Pdf::fake();

    $user = User::factory()->create(['type' => 'superadmin']);
    $invoice = createInvoiceFor($user);

    $this->actingAs($user)
        ->get("/invoices/{$invoice->id}/pdf?type=simplified&disposition=inline")
        ->assertOk();

    Pdf::assertRespondedWithPdf(function ($pdf) {
        return $pdf->viewName === 'invoices.pdf.simplified' && $pdf->isInline();
    });
});

test('invoice pdf requires permission', function () {
    $user = User::factory()->create(['type' => 'company']);
    $invoice = createInvoiceFor($user);

    $this->actingAs($user)
        ->get("/invoices/{$invoice->id}/pdf?type=tax")
        ->assertRedirect(route('dashboard.redirect'));
});
