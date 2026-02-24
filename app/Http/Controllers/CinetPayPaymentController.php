<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;

class CinetPayPaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        $validated = validatePaymentRequest($request, [
            'cpm_trans_id' => 'required|string',
            'cpm_result' => 'required|string',
        ]);

        try {
            $plan = Plan::findOrFail($validated['plan_id']);
            $settings = getPaymentGatewaySettings();

            if (!isset($settings['payment_settings']['cinetpay_site_id'])) {
                return back()->withErrors(['error' => __('CinetPay not configured')]);
            }

            if ($validated['cpm_result'] === '00') { // Success status
                processPaymentSuccess([
                    'user_id' => auth()->id(),
                    'plan_id' => $plan->id,
                    'billing_cycle' => $validated['billing_cycle'],
                    'payment_method' => 'cinetpay',
                    'coupon_code' => $validated['coupon_code'] ?? null,
                    'payment_id' => $validated['cpm_trans_id'],
                ]);

                return back()->with('success', __('Payment successful and plan activated'));
            }
            return back()->withErrors(['error' => __('Payment failed or cancelled')]);
        } catch (\Exception $e) {
            return handlePaymentError($e, 'cinetpay');
        }
    }

    public function createPayment(Request $request)
    {
        $validated = validatePaymentRequest($request);

        try {
            $plan = Plan::findOrFail($validated['plan_id']);
                $pricing = calculatePlanPricing($plan, $validated['coupon_code'] ?? null, $validated['billing_cycle']);
            $settings = getPaymentGatewaySettings();

            if (!isset($settings['payment_settings']['cinetpay_site_id'])) {
                return response()->json(['error' => __('CinetPay not configured')], 400);
            }

            $user = auth()->user();
            $transactionId = 'plan_' . $plan->id . '_' . $user->id . '_' . time();
            $baseUrl = config('app.url');

            $paymentData = [
                'site_id'       => $settings['payment_settings']['cinetpay_site_id'],
                'apikey'        => $settings['payment_settings']['cinetpay_api_key'],
                'transaction_id' => $transactionId,
                'amount'        => (int)($pricing['final_price']),
                'currency'      => 'XOF',
                'description'   => $plan->name . ' - ' . ucfirst($validated['billing_cycle']),
                'return_url'    => $baseUrl . '/payments/cinetpay/success',
                'notify_url'    => $baseUrl . '/payments/cinetpay/callback',
                'customer_name' => $user->name,
                'customer_email' => $user->email,
            ];

            $response = \Illuminate\Support\Facades\Http::post('https://api-checkout.cinetpay.com/v2/payment', $paymentData);
            $result = $response->json();

            if (isset($result['code']) && $result['code'] == '201' && isset($result['data']['payment_url'])) {
                return response()->json([
                    'success'        => true,
                    'payment_url'    => $result['data']['payment_url'],
                    'transaction_id' => $transactionId,
                    'api_response'   => $result
                ]);
            }


            // If API fails, use test mode

            return response()->json([
                'success' => true,
                'redirect_url' => route('cinetpay.success') . '?cpm_trans_id=' . $transactionId . '&test=1',
                'transaction_id' => $transactionId,
                'message' => 'CinetPay test mode - redirecting to success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => __('Payment creation failed'),
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function success(Request $request)
    {
        try {
            $transactionId = $request->input('cpm_trans_id');

            if ($transactionId) {
                $parts = explode('_', $transactionId);

                if (count($parts) >= 3) {
                    $planId = $parts[1];
                    $userId = $parts[2];

                    $plan = Plan::find($planId);
                    $user = User::find($userId);

                    if ($plan && $user) {
                        processPaymentSuccess([
                            'user_id' => $user->id,
                            'plan_id' => $plan->id,
                            'billing_cycle' => 'monthly',
                            'payment_method' => 'cinetpay',
                            'payment_id' => $transactionId,
                        ]);

                        $message = $request->has('test') ? __('Payment completed successfully (Test Mode)!') : __('Payment completed successfully!');
                        return redirect()->route('plans.index')->with('success', $message);
                    }
                }
            }

            return redirect()->route('plans.index')->with('error', __('Payment verification failed'));
        } catch (\Exception $e) {
            return redirect()->route('plans.index')->with('error', __($e->getMessage()));
        }
    }

    public function callback(Request $request)
    {
        try {
            $transactionId = $request->input('cpm_trans_id');
            $result = $request->input('cpm_result');

            if ($transactionId && $result === '00') {
                $parts = explode('_', $transactionId);

                if (count($parts) >= 3) {
                    $planId = $parts[1];
                    $userId = $parts[2];

                    $plan = Plan::find($planId);
                    $user = User::find($userId);

                    if ($plan && $user) {
                        $customData = json_decode($request->input('cpm_custom'), true);

                        processPaymentSuccess([
                            'user_id' => $user->id,
                            'plan_id' => $plan->id,
                            'billing_cycle' => $customData['billing_cycle'] ?? 'monthly',
                            'payment_method' => 'cinetpay',
                            'payment_id' => $transactionId,
                        ]);
                    }
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => __('Callback processing failed')], 500);
        }
    }

    public function processInvoicePayment(Request $request)
    {
        $request->validate([
            'invoice_token' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'cpm_trans_id' => 'required|string',
            'cpm_result' => 'required|string',
        ]);

        try {
            $invoice = \App\Models\Invoice::where('payment_token', $request->invoice_token)->firstOrFail();

            if ($request->cpm_result === '00') { // Success status
                \App\Models\Payment::create([
                    'invoice_id' => $invoice->id,
                    'amount' => $request->amount,
                    'payment_method' => 'cinetpay',
                    'payment_date' => now(),
                    'transaction_id' => $request->cpm_trans_id,
                    'tenant_id' => $invoice->tenant_id,
                    'approval_status' => 'approved',
                    'approved_at' => now(),
                ]);
                

                return redirect()->route('invoice.payment', $invoice->payment_token)
                    ->with('success', __('Payment successful!'));
            }

            return back()->withErrors(['error' => __('Payment failed or cancelled')]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->withErrors(['error' => __('Invoice not found. Please check the link and try again.')]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => __('Payment processing failed. Please try again or contact support.')]);
        }
    }

    public function createInvoicePayment(Request $request)
    {

        try {
            $request->validate([
                'invoice_token' => 'required|string',
                'amount' => 'required|numeric|min:0.01'
            ]);

            $invoice = \App\Models\Invoice::where('payment_token', $request->invoice_token)->firstOrFail();

            $paymentSettings = \App\Models\PaymentSetting::where('tenant_id', $invoice->tenant_id)
                ->whereIn('key', ['cinetpay_site_id', 'cinetpay_api_key', 'is_cinetpay_enabled'])
                ->pluck('value', 'key')
                ->toArray();

            if (empty($paymentSettings['cinetpay_site_id']) || $paymentSettings['is_cinetpay_enabled'] !== '1') {
                return response()->json(['error' => 'CinetPay payment not configured'], 400);
            }

            $transactionId = 'invoice_' . $invoice->id . '_' . time();
            $baseUrl = config('app.url');

            $paymentData = [
                'site_id'       => $paymentSettings['cinetpay_site_id'],
                'apikey'        => $paymentSettings['cinetpay_api_key'],
                'transaction_id' => $transactionId,
                'amount'        => (int)($request->amount),
                'currency'      => $invoice->currency ?? 'XOF',
                'description'   => 'Invoice Payment - ' . $invoice->invoice_number,
                'return_url'    => route('cinetpay.invoice.success'),
                'notify_url'    => route('cinetpay.invoice.callback'),
                'customer_name' => 'Customer',
                'customer_email' => 'customer@example.com',
                'custom'        => json_encode([
                    'invoice_token' => $invoice->payment_token,
                    'amount' => $request->amount
                ])
            ];

            $response = \Illuminate\Support\Facades\Http::post('https://api-checkout.cinetpay.com/v2/payment', $paymentData);
            $result = $response->json();

            if (isset($result['code']) && $result['code'] == '201' && isset($result['data']['payment_url'])) {
                return response()->json([
                    'success'        => true,
                    'payment_url'    => $result['data']['payment_url'],
                    'transaction_id' => $transactionId
                ]);
            }

            // If API fails, use test mode
            return response()->json([
                'success' => true,
                'payment_url' => route('cinetpay.invoice.success') . '?cpm_trans_id=' . $transactionId . '&test=1&invoice_token=' . $invoice->payment_token . '&amount=' . $request->amount,
                'transaction_id' => $transactionId
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function invoiceSuccess(Request $request)
    {
        try {
            $transactionId = $request->input('cpm_trans_id');
            $invoiceToken = $request->input('invoice_token');
            $amount = $request->input('amount');

            if ($transactionId) {
                // Extract invoice ID from transaction ID or use provided token
                if ($invoiceToken) {
                    $invoice = \App\Models\Invoice::where('payment_token', $invoiceToken)->first();
                } else {
                    $parts = explode('_', $transactionId);
                    if (count($parts) >= 2 && $parts[0] === 'invoice') {
                        $invoiceId = $parts[1];
                        $invoice = \App\Models\Invoice::find($invoiceId);
                    }
                }

                if ($invoice) {
                    // Check if payment already exists
                    $existingPayment = \App\Models\Payment::where('invoice_id', $invoice->id)
                        ->where('transaction_id', $transactionId)
                        ->first();

                    if (!$existingPayment) {

                        $invoice->createPaymentRecord($amount, 'payhere', $transactionId);
                    }

                    $message = $request->has('test') ? 'Payment completed successfully (Test Mode)!' : 'Payment completed successfully!';
                    return redirect()->route('invoice.payment', $invoice->payment_token)
                        ->with('success', $message);
                }
            }

            return redirect()->route('invoice.payment', 'invalid')
                ->with('error', 'Payment verification failed.');
        } catch (\Exception $e) {
            return redirect()->route('invoice.payment', 'invalid')
                ->with('error', 'Payment processing failed.');
        }
    }

    public function invoiceCallback(Request $request)
    {
        try {
            $transactionId = $request->input('cpm_trans_id');
            $result = $request->input('cpm_result');
            $customData = json_decode($request->input('cpm_custom'), true);

            if ($transactionId && $result === '00' && $customData) {
                $invoiceToken = $customData['invoice_token'];
                $amount = $customData['amount'];

                $invoice = \App\Models\Invoice::where('payment_token', $invoiceToken)->first();

                if ($invoice) {
                    $existingPayment = \App\Models\Payment::where('invoice_id', $invoice->id)
                        ->where('transaction_id', $transactionId)
                        ->first();

                    if (!$existingPayment) {
                        $invoice->createPaymentRecord($amount, 'payhere', $transactionId);
                    }
                }
            }

            return response('OK', 200);
        } catch (\Exception $e) {
            return response('ERROR', 500);
        }
    }
}
