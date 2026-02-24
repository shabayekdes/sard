<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Models\Setting;
use App\Models\PlanOrder;
use App\Models\PaymentSetting;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class BankPaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        $validated = validatePaymentRequest($request, [
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            $plan = Plan::findOrFail($validated['plan_id']);
            
            createPlanOrder([
                'user_id' => auth()->id(),
                'plan_id' => $plan->id,
                'billing_cycle' => $validated['billing_cycle'],
                'payment_method' => 'bank_transfer',
                'coupon_code' => $validated['coupon_code'] ?? null,
                'payment_id' => 'BANK_' . strtoupper(uniqid()),
                'status' => 'pending',
            ]);

            return back()->with('success', __('Payment request submitted. Your plan will be activated after payment verification.'));

        } catch (\Exception $e) {
            return handlePaymentError($e, 'bank_transfer');
        }
    }
    
    public function processInvoicePayment(Request $request)
    {
        try {
            $request->validate([
                'invoice_token' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif,doc,docx|max:10240',
            ]);
            
            $invoice = Invoice::where('payment_token', $request->invoice_token)->firstOrFail();
            
            $attachmentPaths = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = 'storage/' . $file->store('payment-attachments/' . $invoice->id, 'public');
                $attachmentPaths = [$path];
            }
            
            Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $request->amount,
                'payment_method' => 'bank_transfer',
                'payment_date' => now(),
                'tenant_id' => $invoice->tenant_id,
                'approval_status' => 'pending',
                'attachment' => $attachmentPaths,
            ]);
            
            return redirect()->route('invoice.payment', $invoice->payment_token)
                ->with('success', __('Payment request submitted. Invoice will be marked as paid after payment verification.'));
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->withErrors(['error' => __('Invoice not found. Please check the link and try again.')]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => __('Payment request failed. Please try again or contact support.')]);
        }
    }
}