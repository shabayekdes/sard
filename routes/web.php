<?php

use App\Http\Controllers\AamarpayPaymentController;
use App\Http\Controllers\AuthorizeNetPaymentController;
use App\Http\Controllers\BankPaymentController;
use App\Http\Controllers\BenefitPaymentController;
use App\Http\Controllers\CashfreeController;
use App\Http\Controllers\CinetPayPaymentController;
use App\Http\Controllers\CoinGatePaymentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\EasebuzzPaymentController;
use App\Http\Controllers\FedaPayPaymentController;
use App\Http\Controllers\FlutterwavePaymentController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\IyzipayPaymentController;
use App\Http\Controllers\KhaltiPaymentController;
use App\Http\Controllers\LandingPage\CustomPageController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\MidtransPaymentController;
use App\Http\Controllers\MolliePaymentController;
use App\Http\Controllers\OzowPaymentController;
use App\Http\Controllers\PaiementPaymentController;
use App\Http\Controllers\PayfastPaymentController;
use App\Http\Controllers\PayHerePaymentController;
use App\Http\Controllers\PaymentWallPaymentController;
use App\Http\Controllers\PayPalPaymentController;
use App\Http\Controllers\PaystackPaymentController;
use App\Http\Controllers\PayTabsPaymentController;
use App\Http\Controllers\PayTRPaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PlanOrderController;
use App\Http\Controllers\PlanRequestController;
use App\Http\Controllers\QuickActionController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SkrillPaymentController;
use App\Http\Controllers\SSPayPaymentController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\TapPaymentController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\ToyyibPayPaymentController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\XenditPaymentController;
use App\Http\Controllers\YooKassaPaymentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__ . '/tenant.php';
require __DIR__ . '/universal.php';
require __DIR__ . '/central.php';
require __DIR__ . '/auth.php';


// AamarPay invoice success route - must be outside CSRF protection
Route::match(['GET', 'POST'], 'aamarpay/invoice/success', [AamarpayPaymentController::class, 'invoiceSuccess'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->name('aamarpay.invoice.success');

Route::get('/', [LandingPageController::class, 'show'])->name('home');
Route::get('/directory', [DirectoryController::class, 'index'])->name('directory.index');

// Payment gateway invoice success routes - completely bypass CSRF
Route::match(['GET', 'POST'], 'iyzipay/invoice/success/{token}', [IyzipayPaymentController::class, 'invoiceSuccess'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('iyzipay.invoice.success');
Route::match(['GET', 'POST'], 'iyzipay/success', [IyzipayPaymentController::class, 'success'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('iyzipay.success');
Route::match(['GET', 'POST'], 'iyzipay/invoice/callback/{token}', [IyzipayPaymentController::class, 'invoiceCallback'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('iyzipay.invoice.callback');
Route::post('iyzipay/invoice/notify/{token}', [IyzipayPaymentController::class, 'invoiceNotify'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('iyzipay.invoice.notify');

Route::match(['GET', 'POST'], 'khalti/invoice/success', [KhaltiPaymentController::class, 'invoiceSuccess'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('khalti.invoice.success');
Route::match(['GET', 'POST'], 'cashfree/invoice/success', [CashfreeController::class, 'invoiceSuccess'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('cashfree.invoice.success');
Route::match(['GET', 'POST'], 'sspay/invoice/success', [SSPayPaymentController::class, 'invoiceSuccess'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('sspay.invoice.success');
Route::match(['GET', 'POST'], 'skrill/invoice/success', [SkrillPaymentController::class, 'invoiceSuccess'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('skrill.invoice.success');

// Payment gateway invoice routes (public - no CSRF)
Route::post('mollie/create-invoice-payment', [MolliePaymentController::class, 'createInvoicePayment'])
    ->name('mollie.create-invoice-payment');
Route::post('tap/create-invoice-payment', [TapPaymentController::class, 'createInvoicePayment'])
    ->name('tap.create-invoice-payment');
Route::post('payhere/create-invoice-payment', [PayHerePaymentController::class, 'createInvoicePayment'])
    ->name('payhere.create-invoice-payment');
Route::post('cinetpay/create-invoice-payment', [CinetPayPaymentController::class, 'createInvoicePayment'])
    ->name('cinetpay.create-invoice-payment');
Route::post('fedapay/create-invoice-payment', [FedaPayPaymentController::class, 'createInvoicePayment'])
    ->name('fedapay.create-invoice-payment');
Route::post('paytabs/create-invoice-payment', [PayTabsPaymentController::class, 'createInvoicePayment'])
    ->name('paytabs.create-invoice-payment');
Route::post('khalti/create-invoice-payment', [KhaltiPaymentController::class, 'createInvoicePayment'])
    ->name('khalti.create-invoice-payment');

Route::post('cashfree/create-invoice-payment', [CashfreeController::class, 'createInvoicePayment'])
    ->name('cashfree.create-invoice-payment');
Route::post('aamarpay/create-invoice-payment', [AamarpayPaymentController::class, 'createInvoicePayment'])
    ->name('aamarpay.create-invoice-payment');

Route::post('toyyibpay/create-invoice-payment', [ToyyibPayPaymentController::class, 'createInvoicePayment'])
    ->name('toyyibpay.create-invoice-payment');
Route::post('skrill/create-invoice-payment', [SkrillPaymentController::class, 'processInvoicePayment'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('skrill.create-invoice-payment');
Route::post('skrill/process-invoice-payment', [SkrillPaymentController::class, 'processInvoicePayment'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('skrill.process-invoice-payment');
Route::post('authorizenet/create-invoice-payment', [AuthorizeNetPaymentController::class, 'createInvoicePayment'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('authorizenet.create-invoice-payment');
Route::post('authorizenet/process-invoice-payment', [AuthorizeNetPaymentController::class, 'processInvoicePayment'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('authorizenet.process-invoice-payment');
Route::post('ozow/create-invoice-payment', [OzowPaymentController::class, 'createInvoicePayment'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('ozow.create-invoice-payment');
Route::post('paiement/create-invoice-payment', [PaiementPaymentController::class, 'createInvoicePayment'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('paiement.create-invoice-payment');
Route::post('paymentwall/create-invoice-payment', [PaymentWallPaymentController::class, 'createInvoicePayment'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('paymentwall.create-invoice-payment');
// Invoice Payment Routes (Public - No Auth Required)
Route::match(['GET', 'POST'], 'invoice/pay/{token}', [\App\Http\Controllers\InvoicePaymentController::class, 'show'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('invoice.payment');
Route::post('invoice/pay/{token}/process', [\App\Http\Controllers\InvoicePaymentController::class, 'processPayment'])->name('invoice.payment.process');
Route::get('invoice/pay/{token}/success', [\App\Http\Controllers\InvoicePaymentController::class, 'success'])->name('invoice.payment.success');

// Public form submission routes

// Cashfree webhook (public route)
Route::post('cashfree/webhook', [CashfreeController::class, 'webhook'])->name('cashfree.webhook');
Route::get('cashfree/success', [CashfreeController::class, 'success'])->name('cashfree.success');
Route::post('cashfree/callback', [CashfreeController::class, 'success'])->name('cashfree.callback');

// Benefit webhook (public route)
Route::post('benefit/webhook', [BenefitPaymentController::class, 'webhook'])->name('benefit.webhook');
Route::get('payments/benefit/success', [BenefitPaymentController::class, 'success'])->name('benefit.success');
Route::post('payments/benefit/callback', [BenefitPaymentController::class, 'callback'])->name('benefit.callback');

// FedaPay callback (public route)
Route::match(['GET', 'POST'], 'payments/fedapay/callback', [FedaPayPaymentController::class, 'callback'])->name('fedapay.callback');

// YooKassa success/callback (public routes)
Route::get('payments/yookassa/success', [YooKassaPaymentController::class, 'success'])->name('yookassa.success');
Route::post('payments/yookassa/callback', [YooKassaPaymentController::class, 'callback'])->name('yookassa.callback');

// PayTR callback (public route)
Route::post('payments/paytr/callback', [PayTRPaymentController::class, 'callback'])->name('paytr.callback');

// PayTabs callback (public route)
Route::match(['GET', 'POST'], 'payments/paytabs/callback', [PayTabsPaymentController::class, 'callback'])->name('paytabs.callback');
Route::get('payments/paytabs/success', [PayTabsPaymentController::class, 'success'])->name('paytabs.success');

// Tap payment routes (public routes)
Route::get('payments/tap/success', [TapPaymentController::class, 'success'])->name('tap.success');
Route::post('payments/tap/callback', [TapPaymentController::class, 'callback'])->name('tap.callback');

// Aamarpay payment routes (public routes)

// PaymentWall callback (public route)
Route::match(['GET', 'POST'], 'payments/paymentwall/callback', [PaymentWallPaymentController::class, 'callback'])->name('paymentwall.callback');
Route::get('payments/paymentwall/success', [PaymentWallPaymentController::class, 'success'])->name('paymentwall.success');

// PayFast payment routes (public routes)
Route::get('payments/payfast/success', [PayfastPaymentController::class, 'success'])->name('payfast.success');
Route::post('payments/payfast/callback', [PayfastPaymentController::class, 'callback'])->name('payfast.callback');

// CoinGate callback (public route)
Route::match(['GET', 'POST'], 'payments/coingate/callback', [CoinGatePaymentController::class, 'callback'])->name('coingate.callback');

// Skrill callback (public route)
Route::post('payments/skrill/callback', [SkrillPaymentController::class, 'callback'])->name('skrill.callback');
Route::get('payments/skrill/success', [SkrillPaymentController::class, 'success'])->name('skrill.success');
Route::post('skrill/invoice/callback', [SkrillPaymentController::class, 'invoiceCallback'])->name('skrill.invoice.callback');

// Xendit payment routes (public routes)
Route::get('payments/xendit/success', [XenditPaymentController::class, 'success'])->name('xendit.success');
Route::post('payments/xendit/callback', [XenditPaymentController::class, 'callback'])->name('xendit.callback');

// PWA Manifest routes removed

Route::get('/landing-page', [LandingPageController::class, 'settings'])->name('landing-page');
Route::post('/landing-page/contact', [LandingPageController::class, 'submitContact'])->name('landing-page.contact');
Route::post('/landing-page/subscribe', [LandingPageController::class, 'subscribe'])->name('landing-page.subscribe');
Route::post('newsletter/subscribe', [\App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('newsletter/unsubscribe/{email}', [\App\Http\Controllers\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Cookie consent routes
Route::post('/cookie-consent/store', [\App\Http\Controllers\CookieConsentController::class, 'store'])->name('cookie.consent.store');
Route::get('/cookie-consent/download', [\App\Http\Controllers\CookieConsentController::class, 'download'])->name('cookie.consent.download');

// SSPay payment routes (public routes)
Route::get('payments/sspay/success', [SSPayPaymentController::class, 'success'])->name('sspay.success');
Route::post('payments/sspay/callback', [SSPayPaymentController::class, 'callback'])->name('sspay.callback');
Route::get('payments/sspay/invoice-success', [SSPayPaymentController::class, 'invoiceSuccess'])->name('sspay.invoice.success');
Route::post('payments/sspay/invoice-callback', [SSPayPaymentController::class, 'invoiceCallback'])->name('sspay.invoice.callback');
Route::get('/page/{slug}', [CustomPageController::class, 'show'])->name('custom-page.show');

// Email Templates routes (no middleware for testing)
Route::get('email-templates', [\App\Http\Controllers\EmailTemplateController::class, 'index'])->name('email-templates.index');
Route::get('email-templates/{emailTemplate}', [\App\Http\Controllers\EmailTemplateController::class, 'show'])->name('email-templates.show');
Route::put('email-templates/{emailTemplate}/settings', [\App\Http\Controllers\EmailTemplateController::class, 'updateSettings'])->name('email-templates.update-settings');
Route::put('email-templates/{emailTemplate}/content', [\App\Http\Controllers\EmailTemplateController::class, 'updateContent'])->name('email-templates.update-content');

// Notification Templates routes (no middleware for testing)
Route::get('notification-templates', [\App\Http\Controllers\NotificationTemplateController::class, 'index'])->name('notification-templates.index');
Route::get('notification-templates/{notificationTemplate}', [\App\Http\Controllers\NotificationTemplateController::class, 'show'])->name('notification-templates.show');
Route::put('notification-templates/{notificationTemplate}/settings', [\App\Http\Controllers\NotificationTemplateController::class, 'updateSettings'])->name('notification-templates.update-settings');
Route::put('notification-templates/{notificationTemplate}/content', [\App\Http\Controllers\NotificationTemplateController::class, 'updateContent'])->name('notification-templates.update-content');

// Test routes
Route::get('tap-test-get', function () {
    return response()->json(['success' => true, 'message' => 'GET Working']);
});

Route::post('tap-test-post', function () {
    return response()->json(['success' => true, 'message' => 'POST Working']);
});

// Invoice payment routes (public - no auth required)
Route::get('tap/create-invoice-payment/{token}/{amount}', [TapPaymentController::class, 'createInvoicePayment'])->name('tap.create-invoice-payment-get');

// Test route
Route::post('test-mollie', function () {
    return response()->json(['success' => true, 'message' => 'Test route working']);
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->group(function () {

        Route::post('aamarpay/create-payment', [AamarpayPaymentController::class, 'createPayment'])->name('aamarpay.create-payment');
        Route::match(['GET', 'POST'], 'payments/aamarpay/success', [AamarpayPaymentController::class, 'success'])->name('aamarpay.success');
        Route::post('payments/aamarpay/callback', [AamarpayPaymentController::class, 'callback'])->name('aamarpay.callback');
        Route::post('easebuzz/create-invoice-payment', [EasebuzzPaymentController::class, 'createInvoicePayment'])->name('easebuzz.create-invoice-payment');
        Route::post('sspay/create-invoice-payment', [SSPayPaymentController::class, 'createInvoicePayment'])->name('sspay.create-invoice-payment');
        Route::post('yookassa/create-invoice-payment', [YooKassaPaymentController::class, 'createInvoicePayment'])->name('yookassa.create-invoice-payment');
        Route::post('midtrans/create-invoice-payment', [MidtransPaymentController::class, 'createInvoicePayment'])->name('midtrans.create-invoice-payment');
        Route::post('cashfree/create-invoice-payment', [CashfreeController::class, 'createInvoicePayment'])->name('cashfree.create-invoice-payment');
        Route::post('cashfree/verify-invoice-payment', [CashfreeController::class, 'verifyInvoicePayment'])->name('cashfree.verify-invoice-payment');
        Route::post('razorpay/create-invoice-order', [RazorpayController::class, 'createInvoiceOrder'])->name('razorpay.create-invoice-order');
        Route::post('razorpay/verify-invoice-payment', [RazorpayController::class, 'verifyInvoicePayment'])->name('razorpay.verify-invoice-payment');
        Route::post('razorpay/create-invoice-order', [RazorpayController::class, 'createInvoiceOrder'])->name('razorpay.create-invoice-order');
        Route::post('razorpay/verify-invoice-payment', [RazorpayController::class, 'verifyInvoicePayment'])->name('razorpay.verify-invoice-payment');

        Route::match(['GET', 'POST'], 'paymentwall/invoice/success', [PaymentWallPaymentController::class, 'invoiceSuccess'])->name('paymentwall.invoice.success');
        Route::post('payfast/create-invoice-payment', [PayfastPaymentController::class, 'createInvoicePayment'])->name('payfast.create-invoice-payment');
        Route::post('paytr/create-invoice-payment', [PayTRPaymentController::class, 'createInvoicePayment'])->name('paytr.create-invoice-payment');
        Route::post('iyzipay/create-invoice-payment', [IyzipayPaymentController::class, 'createInvoicePayment'])->name('iyzipay.create-invoice-payment');
        Route::match(['GET', 'POST'], 'iyzipay/invoice/callback', [IyzipayPaymentController::class, 'invoiceCallback'])->name('iyzipay.invoice.callback');

        Route::post('benefit/create-invoice-session', [BenefitPaymentController::class, 'createInvoiceSession'])->name('benefit.create-invoice-session');

        Route::match(['GET', 'POST'], 'authorizenet/invoice/success', [AuthorizeNetPaymentController::class, 'invoiceSuccess'])->name('authorizenet.invoice.success');
        Route::post('ozow/process-invoice-payment', [OzowPaymentController::class, 'processInvoicePayment'])->name('ozow.process-invoice-payment');
    });

// Skrill test page route (outside CSRF group)
Route::get('skrill-test', function (\Illuminate\Http\Request $request) {
    return view('skrill-test', [
        'amount' => $request->amount,
        'orderId' => $request->order_id,
        'invoiceToken' => $request->invoice_token,
    ]);
})->name('skrill.test.page');

// Cashfree test page route (outside CSRF group)
Route::get('cashfree-test', function (\Illuminate\Http\Request $request) {
    $successUrl = route('cashfree.invoice.success') . '?order_id=' . $request->order_id . '&invoice_token=' . $request->invoice_token . '&test=1';

    return view('cashfree-test', [
        'amount' => $request->amount,
        'order_id' => $request->order_id,
        'invoice_token' => $request->invoice_token,
        'success_url' => $successUrl,
    ]);
})->name('cashfree.test.page');

Route::match(['GET', 'POST'], 'payments/easebuzz/success', [EasebuzzPaymentController::class, 'success'])->name('easebuzz.success');
Route::post('payments/easebuzz/callback', [EasebuzzPaymentController::class, 'callback'])->name('easebuzz.callback');
Route::match(['GET', 'POST'], 'easebuzz/invoice/success', [EasebuzzPaymentController::class, 'invoiceSuccess'])->name('easebuzz.invoice.success');

// Payment gateway invoice success/callback routes (public - no auth required)
Route::match(['GET', 'POST'], 'mollie/invoice/success/{token}', [MolliePaymentController::class, 'invoiceSuccess'])->name('mollie.invoice.success');
Route::post('mollie/invoice/callback', [MolliePaymentController::class, 'invoiceCallback'])->name('mollie.invoice.callback');
Route::match(['GET', 'POST'], 'tap/invoice/success/{token}', [TapPaymentController::class, 'invoiceSuccess'])->name('tap.invoice.success');
Route::post('tap/invoice/callback', [TapPaymentController::class, 'invoiceCallback'])->name('tap.invoice.callback');
Route::match(['GET', 'POST'], 'easebuzz/invoice/success', [EasebuzzPaymentController::class, 'invoiceSuccess'])->name('easebuzz.invoice.success');
Route::match(['GET', 'POST'], 'payhere/invoice/success/{token}', [PayHerePaymentController::class, 'invoiceSuccess'])->name('payhere.invoice.success');
Route::post('payhere/invoice/notify', [PayHerePaymentController::class, 'invoiceNotify'])->name('payhere.invoice.notify');

Route::post('payhere/invoice/callback', [PayHerePaymentController::class, 'invoiceCallback'])->name('payhere.invoice.callback');
Route::match(['GET', 'POST'], 'cinetpay/invoice/success', [CinetPayPaymentController::class, 'invoiceSuccess'])->name('cinetpay.invoice.success');
Route::post('cinetpay/invoice/callback', [CinetPayPaymentController::class, 'invoiceCallback'])->name('cinetpay.invoice.callback');
Route::match(['GET', 'POST'], 'fedapay/invoice/callback', [FedaPayPaymentController::class, 'invoiceCallback'])->name('fedapay.invoice.callback');
Route::match(['GET', 'POST'], 'paytabs/invoice/success', [PayTabsPaymentController::class, 'invoiceSuccess'])->name('paytabs.invoice.success');
Route::post('paytabs/invoice/callback', [PayTabsPaymentController::class, 'invoiceCallback'])->name('paytabs.invoice.callback');
Route::match(['GET', 'POST'], 'khalti/invoice/success', [KhaltiPaymentController::class, 'invoiceSuccess'])->name('khalti.invoice.success');
Route::match(['GET', 'POST'], 'paiement/invoice/success', [PaiementPaymentController::class, 'invoiceSuccess'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('paiement.invoice.success');
Route::post('paiement/invoice/callback', [PaiementPaymentController::class, 'invoiceCallback'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('paiement.invoice.callback');
Route::match(['GET', 'POST'], 'cashfree/invoice/success', [CashfreeController::class, 'invoiceSuccess'])->name('cashfree.invoice.success');
Route::get('cashfree/invoice/success/{token}', [CashfreeController::class, 'invoiceSuccess'])->name('cashfree.invoice.success.token');
Route::post('cashfree/invoice/callback', [CashfreeController::class, 'invoiceCallback'])->name('cashfree.invoice.callback');
Route::match(['GET', 'POST'], 'sspay/invoice/success', [SSPayPaymentController::class, 'invoiceSuccess'])->name('sspay.invoice.success');
Route::post('sspay/invoice/callback', [SSPayPaymentController::class, 'invoiceCallback'])->name('sspay.invoice.callback');
Route::match(['GET', 'POST'], 'skrill/invoice/success', [SkrillPaymentController::class, 'invoiceSuccess'])->name('skrill.invoice.success');
Route::post('skrill/invoice/callback', [SkrillPaymentController::class, 'invoiceCallback'])->name('skrill.invoice.callback');
Route::match(['GET', 'POST'], 'yookassa/invoice/success', [YooKassaPaymentController::class, 'invoiceSuccess'])->name('yookassa.invoice.success');
Route::post('yookassa/invoice/callback', [YooKassaPaymentController::class, 'invoiceCallback'])->name('yookassa.invoice.callback');
Route::match(['GET', 'POST'], 'midtrans/invoice/success', [MidtransPaymentController::class, 'invoiceSuccess'])->name('midtrans.invoice.success');
Route::post('midtrans/invoice/callback', [MidtransPaymentController::class, 'invoiceCallback'])->name('midtrans.invoice.callback');
Route::post('aamarpay/invoice/callback', [AamarpayPaymentController::class, 'invoiceCallback'])->name('aamarpay.invoice.callback');
// Move Aamarpay invoice success to CSRF-exempt group

Route::post('paymentwall/invoice/callback', [PaymentWallPaymentController::class, 'invoiceCallback'])->name('paymentwall.invoice.callback');
Route::post('paymentwall/process-invoice', [PaymentWallPaymentController::class, 'processInvoicePayment'])->name('paymentwall.process.invoice');
Route::match(['GET', 'POST'], 'payfast/invoice/success', [PayfastPaymentController::class, 'invoiceSuccess'])->name('payfast.invoice.success');
Route::post('payfast/invoice/callback', [PayfastPaymentController::class, 'invoiceCallback'])->name('payfast.invoice.callback');
Route::match(['GET', 'POST'], 'xendit/invoice/success/{token}', [XenditPaymentController::class, 'invoiceSuccess'])->name('xendit.invoice.success');
Route::match(['GET', 'POST'], 'paytr/invoice/success', [PayTRPaymentController::class, 'invoiceSuccess'])->name('paytr.invoice.success');
Route::match(['GET', 'POST'], 'toyyibpay/invoice/success', [ToyyibPayPaymentController::class, 'invoiceSuccess'])->name('toyyibpay.invoice.success');
Route::post('toyyibpay/invoice/callback', [ToyyibPayPaymentController::class, 'invoiceCallback'])->name('toyyibpay.invoice.callback');
Route::match(['GET', 'POST'], 'benefit/invoice/success', [BenefitPaymentController::class, 'invoiceSuccess'])->name('benefit.invoice.success');
Route::post('benefit/invoice/callback', [BenefitPaymentController::class, 'invoiceCallback'])->name('benefit.invoice.callback');
Route::match(['GET', 'POST'], 'ozow/invoice/success', [OzowPaymentController::class, 'invoiceSuccess'])->name('ozow.invoice.success');
Route::post('ozow/invoice/callback', [OzowPaymentController::class, 'invoiceCallback'])->name('ozow.invoice.callback');
