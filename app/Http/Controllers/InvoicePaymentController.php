<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoicePaymentController extends Controller
{
    public function show($token, Request $request)
    {
        // Handle Iyzipay POST callback first
        if ($request->isMethod('post') && $request->has('token')) {
            return $this->handleIyzipayCallback($token, $request);
        }

        return $this->showPaymentPage($token, $request);
    }

    private function handleIyzipayCallback($token, Request $request)
    {
        try {
            $iyzipayToken = $request->input('token');
            $invoice = Invoice::where('payment_token', $token)->firstOrFail();

            $paymentSettings = PaymentSetting::where('user_id', $invoice->created_by)
                ->whereIn('key', ['iyzipay_public_key', 'iyzipay_secret_key', 'iyzipay_mode'])
                ->pluck('value', 'key')
                ->toArray();

            $options = new \Iyzipay\Options();
            $options->setApiKey($paymentSettings['iyzipay_public_key']);
            $options->setSecretKey($paymentSettings['iyzipay_secret_key']);
            $options->setBaseUrl($paymentSettings['iyzipay_mode'] === 'live' ? 'https://api.iyzipay.com' : 'https://sandbox-api.iyzipay.com');

            $retrieveRequest = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
            $retrieveRequest->setToken($iyzipayToken);
            $paymentResult = \Iyzipay\Model\CheckoutForm::retrieve($retrieveRequest, $options);

            if ($paymentResult && $paymentResult->getPaymentStatus() === 'SUCCESS') {
                $invoice->createPaymentRecord($paymentResult->getPaidPrice(), 'iyzipay', $paymentResult->getPaymentId());
                return redirect()->route('invoice.payment', $token)->with('success', 'Payment completed successfully!');
            }

            return redirect()->route('invoice.payment', $token)->with('error', 'Payment verification failed');
        } catch (\Exception $e) {
            return redirect()->route('invoice.payment', $token)->with('error', 'Payment processing failed');
        }
    }

    private function showPaymentPage($token, Request $request)
    {
        $invoice = Invoice::where('payment_token', $token)
            ->with(['client', 'case', 'creator'])
            ->firstOrFail();

        // Get company information
        $company = \App\Models\User::where('id', $invoice->created_by)
            ->where('type', 'company')
            ->select('id', 'name')
            ->first();

        $companyProfile = \App\Models\CompanyProfile::where('created_by', $invoice->created_by)
            ->first();
            
        // Get favicon and app name from settings table
        $settings = \App\Models\Setting::where('user_id', $invoice->created_by)
            ->whereIn('key', ['favicon', 'app_name'])
            ->pluck('value', 'key')
            ->toArray();
            
        $favicon = $settings['favicon'] ?? null;
        $appName = $settings['app_name'] ?? 'Advocate Saas';

        $brandSettings = \App\Models\Setting::where('user_id', $invoice->created_by)
            ->whereIn('key', ['logoLight', 'logoDark'])
            ->pluck('value', 'key')
            ->toArray();
        $companyLogo = $brandSettings['logoDark'] ?? null;

        // Handle success/error parameters from Iyzipay callback
        if ($request->has('success')) {
            session()->flash('success', 'Payment completed successfully!');
        } elseif ($request->has('error')) {
            session()->flash('error', 'Payment processing failed');
        }

        // Generic payment callback handler removed - all payments use their own controllers

        // Always show payment page, even if paid (to show updated status)

        $enabledGateways = $this->getEnabledPaymentGateways($invoice->created_by);

        // Load client billing info and currencies (no permission check for public payment page)
        $clientBillingInfo = \App\Models\ClientBillingInfo::select('client_id', 'currency')
            ->get()
            ->keyBy('client_id');

        // TODO: Not used currently, remove if not needed
        // $currencies = \App\Models\Currency::where('created_by', $invoice->created_by)
        //     ->where('status', true)
        //     ->select('id', 'name', 'code', 'symbol')
        //     ->get();

        // Get PayPal settings for frontend
        $paypalSettings = getPaymentMethodConfig('paypal', $invoice->created_by);

        // Get payment gateway settings for frontend
        $paymentSettings = PaymentSetting::where('user_id', $invoice->created_by)
            ->pluck('value', 'key')
            ->toArray();

        // Get company currency setting
        $companyCurrency = \App\Models\Setting::where('user_id', $invoice->created_by)
            ->where('key', 'currency')
            ->value('value');
            
        // Get currency details if company currency is set
        $defaultCurrency = null;
        if ($companyCurrency) {
            $defaultCurrency = \App\Models\Currency::where(function ($query) use ($companyCurrency) {
                $query->where('code', $companyCurrency)
                    ->orWhere('id', $companyCurrency);
            })
                ->select('id', 'name', 'code', 'symbol')
                ->first();
        }

        return Inertia::render('invoice/payment', [
            'invoice' => $invoice,
            'enabledGateways' => $enabledGateways,
            'remainingAmount' => $invoice->remaining_amount,
            'clientBillingInfo' => $clientBillingInfo,
            // 'currencies' => $currencies,
            // 'defaultCurrency' => $defaultCurrency,
            'paypalClientId' => $paypalSettings['client_id'] ?? null,
            'flutterwavePublicKey' => $paymentSettings['flutterwave_public_key'] ?? null,
            'tapPublicKey' => $paymentSettings['tap_secret_key'] ?? null,
            'paystackPublicKey' => $paymentSettings['paystack_public_key'] ?? null,
            'company' => $company,
            'companyProfile' => $companyProfile,
            'companyLogo' => $companyLogo,
            'favicon' => $favicon,
            'appName' => $appName
        ]);
    }

    public function processPayment(Request $request, $token)
    {
        $invoice = Invoice::where('payment_token', $token)->firstOrFail();

        $maxAmount = $invoice->remaining_amount ?: $invoice->total_amount;
        
        $request->validate([
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:0.01|max:' . $maxAmount
        ]);
        
        // Ensure amount doesn't exceed remaining balance
        if ($request->amount > $maxAmount) {
            return back()->withErrors(['amount' => 'Payment amount cannot exceed remaining balance of ' . $maxAmount]);
        }

        // Add invoice context to request
        $request->merge([
            'invoice_id' => $invoice->id,
            'invoice_token' => $token,
            'type' => 'invoice'
        ]);

        $paymentMethod = $request->payment_method;

        // Call specific invoice payment methods
        $controller = config('payment_methods.' . $paymentMethod . '.controller', '');


        if (!isset($controller)) {
            return back()->withErrors(['error' => 'Payment method not supported']);
        }

        try {
            $controller = app($controller);
            return $controller->processInvoicePayment($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->withErrors(['error' => __('Invoice not found. Please check the link and try again.')]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => __('Payment processing failed. Please try again or contact support.')]);
        }
    }


    public function success($token)
    {
        $invoice = Invoice::where('payment_token', $token)
            ->with(['client', 'payments'])
            ->firstOrFail();

        return Inertia::render('invoice/payment-success', [
            'invoice' => $invoice,
            'message' => session('success')
        ]);
    }

    private function getEnabledPaymentGateways($invoiceCreatorId = null)
    {
        if (!$invoiceCreatorId) {
            return [];
        }

        // Get company-specific payment settings only
        $settings = PaymentSetting::where('user_id', $invoiceCreatorId)->pluck('value', 'key')->toArray();

        $gateways = [];
        $paymentGateways = config('payment_methods');

        foreach ($paymentGateways as $key => $config) {
            $isEnabled = $settings[$key . '_enabled'] ?? false;
            if ($isEnabled) {
                $gateways[] = [
                    'id' => $key,
                    'name' => $config['name'][app()->getLocale()],
                    'icon' => $config['icon']
                ];
            }
        }

        return $gateways;
    }
}

