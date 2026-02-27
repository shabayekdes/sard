<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Http\Controllers\LandingPage\CustomPageController;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PayPalPaymentController;
use App\Http\Controllers\StripePaymentController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Features\UserImpersonation;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomainOrSubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    require __DIR__ . '/auth.php';


    // Public route: token logs in the user without email/password. Do not add 'auth' middleware.
    Route::get('/impersonate/{token}', function (string $token) {
        return UserImpersonation::makeResponse($token);
    })->name('tenant.impersonate');

    Route::middleware(['auth', 'verified'])->group(function () {
        // Payment routes - accessible without plan check
        Route::post('payments/stripe', [Controllers\StripePaymentController::class, 'processPayment'])->name('stripe.payment');
        Route::post('payments/paypal', [Controllers\PayPalPaymentController::class, 'processPayment'])->name('paypal.payment');
        Route::post('payments/bank-transfer', [Controllers\BankPaymentController::class, 'processPayment'])->name('bank-transfer.payment');
        Route::post('payments/paystack', [Controllers\PaystackPaymentController::class, 'processPayment'])->name('paystack.payment');
        Route::post('payments/flutterwave', [Controllers\FlutterwavePaymentController::class, 'processPayment'])->name('flutterwave.payment');
        Route::post('payments/paytabs', [Controllers\PayTabsPaymentController::class, 'processPayment'])->name('paytabs.payment');
        Route::post('payments/skrill', [Controllers\SkrillPaymentController::class, 'processPayment'])->name('skrill.payment');
        Route::post('payments/coingate', [Controllers\CoinGatePaymentController::class, 'processPayment'])->name('coingate.payment');
        Route::post('payments/payfast', [Controllers\PayfastPaymentController::class, 'processPayment'])->name('payfast.payment');
        Route::post('payments/mollie', [Controllers\MolliePaymentController::class, 'processPayment'])->name('mollie.payment');
        Route::post('payments/toyyibpay', [Controllers\ToyyibPayPaymentController::class, 'processPayment'])->name('toyyibpay.payment');
        Route::post('payments/iyzipay', [Controllers\IyzipayPaymentController::class, 'processPayment'])->name('iyzipay.payment');
        Route::post('payments/benefit', [Controllers\BenefitPaymentController::class, 'processPayment'])->name('benefit.payment');
        Route::post('payments/ozow', [Controllers\OzowPaymentController::class, 'processPayment'])->name('ozow.payment');
        Route::post('payments/easebuzz', [Controllers\EasebuzzPaymentController::class, 'processPayment'])->name('easebuzz.payment');
        Route::post('payments/khalti', [Controllers\KhaltiPaymentController::class, 'processPayment'])->name('khalti.payment');
        Route::post('payments/authorizenet', [Controllers\AuthorizeNetPaymentController::class, 'processPayment'])->name('authorizenet.payment');
        Route::post('payments/fedapay', [Controllers\FedaPayPaymentController::class, 'processPayment'])->name('fedapay.payment');
        Route::post('payments/payhere', [Controllers\PayHerePaymentController::class, 'processPayment'])->name('payhere.payment');
        Route::post('payments/cinetpay', [Controllers\CinetPayPaymentController::class, 'processPayment'])->name('cinetpay.payment');
        Route::post('payments/paiement', [Controllers\PaiementPaymentController::class, 'processPayment'])->name('paiement.payment');

        Route::post('payments/yookassa', [Controllers\YooKassaPaymentController::class, 'processPayment'])->name('yookassa.payment');
        Route::post('payments/aamarpay', [Controllers\AamarpayPaymentController::class, 'processPayment'])->name('aamarpay.payment');
        Route::post('payments/midtrans', [Controllers\MidtransPaymentController::class, 'processPayment'])->name('midtrans.payment');
        Route::post('payments/paymentwall', [Controllers\PaymentWallPaymentController::class, 'processPayment'])->name('paymentwall.payment');
        Route::post('payments/sspay', [Controllers\SSPayPaymentController::class, 'processPayment'])->name('sspay.payment');

        // Payment gateway specific routes
        Route::post('razorpay/create-order', [Controllers\RazorpayController::class, 'createOrder'])->name('razorpay.create-order');
        Route::post('razorpay/verify-payment', [Controllers\RazorpayController::class, 'verifyPayment'])->name('razorpay.verify-payment');
        Route::post('cashfree/create-session', [Controllers\CashfreeController::class, 'createPaymentSession'])->name('cashfree.create-session');
        Route::post('cashfree/verify-payment', [Controllers\CashfreeController::class, 'verifyPayment'])->name('cashfree.verify-payment');

        // Other payment creation routes
        Route::post('tap/create-payment', [Controllers\TapPaymentController::class, 'createPayment'])->name('tap.create-payment');
        Route::post('xendit/create-payment', [Controllers\XenditPaymentController::class, 'createPayment'])->name('xendit.create-payment');
        Route::post('payments/paytr/create-token', [Controllers\PayTRPaymentController::class, 'createPaymentToken'])->name('paytr.create-token');
        Route::post('iyzipay/create-form', [Controllers\IyzipayPaymentController::class, 'createPaymentForm'])->name('iyzipay.create-form');
        Route::post('benefit/create-session', [Controllers\BenefitPaymentController::class, 'createPaymentSession'])->name('benefit.create-session');
        Route::post('ozow/create-payment', [Controllers\OzowPaymentController::class, 'createPayment'])->name('ozow.create-payment');
        Route::post('easebuzz/create-payment', [Controllers\EasebuzzPaymentController::class, 'createPayment'])->name('easebuzz.create-payment');
        Route::post('khalti/create-payment', [Controllers\KhaltiPaymentController::class, 'createPayment'])->name('khalti.create-payment');
        Route::post('authorizenet/create-form', [Controllers\AuthorizeNetPaymentController::class, 'createPaymentForm'])->name('authorizenet.create-form');
        Route::post('fedapay/create-payment', [Controllers\FedaPayPaymentController::class, 'createPayment'])->name('fedapay.create-payment');
        Route::post('payhere/create-payment', [Controllers\PayHerePaymentController::class, 'createPayment'])->name('payhere.create-payment');
        Route::post('cinetpay/create-payment', [Controllers\CinetPayPaymentController::class, 'createPayment'])->name('cinetpay.create-payment');

        Route::post('yookassa/create-payment', [Controllers\YooKassaPaymentController::class, 'createPayment'])->name('yookassa.create-payment');

        Route::post('midtrans/create-payment', [Controllers\MidtransPaymentController::class, 'createPayment'])->name('midtrans.create-payment');
        Route::post('paymentwall/create-payment', [Controllers\PaymentWallPaymentController::class, 'createPayment'])->name('paymentwall.create-payment');
        Route::post('sspay/create-payment', [Controllers\SSPayPaymentController::class, 'createPayment'])->name('sspay.create-payment');

        // Payment success/callback routes
        Route::post('payments/skrill/callback', [Controllers\SkrillPaymentController::class, 'callback'])->name('skrill.callback');
        Route::get('payments/paytr/success', [Controllers\PayTRPaymentController::class, 'success'])->name('paytr.success');
        Route::get('payments/paytr/failure', [Controllers\PayTRPaymentController::class, 'failure'])->name('paytr.failure');
        Route::get('payments/mollie/success', [Controllers\MolliePaymentController::class, 'success'])->name('mollie.success');
        Route::post('payments/mollie/callback', [Controllers\MolliePaymentController::class, 'callback'])->name('mollie.callback');
        Route::match(['GET', 'POST'], 'payments/toyyibpay/success', [Controllers\ToyyibPayPaymentController::class, 'success'])->name('toyyibpay.success');
        Route::post('payments/toyyibpay/callback', [Controllers\ToyyibPayPaymentController::class, 'callback'])->name('toyyibpay.callback');
        Route::post('payments/iyzipay/callback', [Controllers\IyzipayPaymentController::class, 'callback'])->name('iyzipay.callback');
        Route::match(['GET', 'POST'], 'payments/iyzipay/success', [Controllers\IyzipayPaymentController::class, 'success'])->name('iyzipay.success');

        Route::get('payments/ozow/success', [Controllers\OzowPaymentController::class, 'success'])->name('ozow.success');
        Route::post('payments/ozow/callback', [Controllers\OzowPaymentController::class, 'callback'])->name('ozow.callback');
        Route::get('payments/payhere/success', [Controllers\PayHerePaymentController::class, 'success'])->name('payhere.success');
        Route::post('payments/payhere/callback', [Controllers\PayHerePaymentController::class, 'callback'])->name('payhere.callback');
        Route::get('payments/cinetpay/success', [Controllers\CinetPayPaymentController::class, 'success'])->name('cinetpay.success');
        Route::post('payments/cinetpay/callback', [Controllers\CinetPayPaymentController::class, 'callback'])->name('cinetpay.callback');
        Route::post('paiement/create-payment', [Controllers\PaiementPaymentController::class, 'createPayment'])->name('paiement.create-payment');
        Route::get('payments/paiement/success', [Controllers\PaiementPaymentController::class, 'success'])
            ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
            ->name('paiement.success');
        Route::post('payments/paiement/callback', [Controllers\PaiementPaymentController::class, 'callback'])
            ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
            ->name('paiement.callback');
        Route::post('payments/midtrans/callback', [Controllers\MidtransPaymentController::class, 'callback'])->name('midtrans.callback');
        Route::post('paymentwall/process', [Controllers\PaymentWallPaymentController::class, 'processPayment'])->name('paymentwall.process');
        Route::get('payments/sspay/success', [Controllers\SSPayPaymentController::class, 'success'])->name('sspay.success');
        Route::post('payments/sspay/callback', [Controllers\SSPayPaymentController::class, 'callback'])->name('sspay.callback');
        Route::post('authorizenet/test-connection', [Controllers\AuthorizeNetPaymentController::class, 'testConnection'])->name('authorizenet.test-connection');

        // All other routes require plan access check
        Route::middleware('plan.access')->group(function () {
            Route::get('dashboard', [Controllers\DashboardController::class, 'index'])->name('dashboard');
            Route::get('dashboard/redirect', [Controllers\DashboardController::class, 'redirectToFirstAvailablePage'])->name('dashboard.redirect');

            // Analytics routes
            Route::get('dashboard/analytics', [Controllers\DashboardAnalyticsController::class, 'index'])->name('dashboard.analytics.index');


            // Users routes with granular permissions
            Route::middleware('permission:manage-users')->group(function () {
                Route::get('users', [Controllers\UserController::class, 'index'])->middleware('permission:manage-users')->name('users.index');
                Route::get('users/create', [Controllers\UserController::class, 'create'])->middleware('permission:create-users')->name('users.create');
                Route::post('users', [Controllers\UserController::class, 'store'])->middleware('permission:create-users')->name('users.store');
                Route::get('users/{user}', [Controllers\UserController::class, 'show'])->middleware('permission:view-users')->name('users.show');
                Route::get('users/{user}/edit', [Controllers\UserController::class, 'edit'])->middleware('permission:edit-users')->name('users.edit');
                Route::put('users/{user}', [Controllers\UserController::class, 'update'])->middleware('permission:edit-users')->name('users.update');
                Route::patch('users/{user}', [Controllers\UserController::class, 'update'])->middleware('permission:edit-users');
                Route::delete('users/{user}', [Controllers\UserController::class, 'destroy'])->middleware('permission:delete-users')->name('users.destroy');
                Route::get('user-logs', [Controllers\UserController::class, 'loginhistory'])->name('user-logs.index');

                // Additional user routes
                Route::put('users/{user}/reset-password', [Controllers\UserController::class, 'resetPassword'])->middleware('permission:reset-password-users')->name('users.reset-password');
                Route::put('users/{user}/toggle-status', [Controllers\UserController::class, 'toggleStatus'])->middleware('permission:toggle-status-users')->name('users.toggle-status');
            });


            // Permissions routes with granular permissions
            Route::middleware('permission:manage-permissions')->group(function () {
                Route::get('permissions', [Controllers\PermissionController::class, 'index'])->middleware('permission:manage-permissions')->name('permissions.index');
                Route::get('permissions/create', [Controllers\PermissionController::class, 'create'])->middleware('permission:create-permissions')->name('permissions.create');
                Route::post('permissions', [Controllers\PermissionController::class, 'store'])->middleware('permission:create-permissions')->name('permissions.store');
                Route::get('permissions/{permission}', [Controllers\PermissionController::class, 'show'])->middleware('permission:view-permissions')->name('permissions.show');
                Route::get('permissions/{permission}/edit', [Controllers\PermissionController::class, 'edit'])->middleware('permission:edit-permissions')->name('permissions.edit');
                Route::put('permissions/{permission}', [Controllers\PermissionController::class, 'update'])->middleware('permission:edit-permissions')->name('permissions.update');
                Route::patch('permissions/{permission}', [Controllers\PermissionController::class, 'update'])->middleware('permission:edit-permissions');
                Route::delete('permissions/{permission}', [Controllers\PermissionController::class, 'destroy'])->middleware('permission:delete-permissions')->name('permissions.destroy');
            });

            // Roles routes with granular permissions
            Route::middleware('permission:manage-roles')->group(function () {
                Route::get('roles', [Controllers\RoleController::class, 'index'])->middleware('permission:manage-roles')->name('roles.index');
                Route::get('roles/create', [Controllers\RoleController::class, 'create'])->middleware('permission:create-roles')->name('roles.create');
                Route::post('roles', [Controllers\RoleController::class, 'store'])->middleware('permission:create-roles')->name('roles.store');
                Route::get('roles/{role}', [Controllers\RoleController::class, 'show'])->middleware('permission:view-roles')->name('roles.show');
                Route::get('roles/{role}/edit', [Controllers\RoleController::class, 'edit'])->middleware('permission:edit-roles')->name('roles.edit');
                Route::put('roles/{role}', [Controllers\RoleController::class, 'update'])->middleware('permission:edit-roles')->name('roles.update');
                Route::patch('roles/{role}', [Controllers\RoleController::class, 'update'])->middleware('permission:edit-roles');
                Route::delete('roles/{role}', [Controllers\RoleController::class, 'destroy'])->middleware('permission:delete-roles')->name('roles.destroy');
            });
            
            // Quick action form data
            Route::get('quick-actions/case-data', [Controllers\QuickActionController::class, 'caseFormData'])->name('quick-actions.case-data');
            Route::get('quick-actions/client-data', [Controllers\QuickActionController::class, 'clientFormData'])->name('quick-actions.client-data');
            Route::get('quick-actions/task-data', [Controllers\QuickActionController::class, 'taskFormData'])->name('quick-actions.task-data');
            Route::get('quick-actions/hearing-data', [Controllers\QuickActionController::class, 'hearingFormData'])->name('quick-actions.hearing-data');

            // Setup-prefixed routes (Master Data module index pages at /setup/{module})
            Route::prefix('setup')
                ->middleware('permission:view-setup')
                ->name('setup.')
                ->group(function () {
                    Route::get('/', fn() => Inertia::render('setup/index'))->name('index');

                    Route::middleware('permission:manage-client-types')->group(function () {
                        Route::get('client-types', [Controllers\ClientTypeController::class, 'index'])->name('client-types.index');
                        Route::post('client-types', [Controllers\ClientTypeController::class, 'store'])->middleware('permission:create-client-types')->name('client-types.store');
                        Route::put('client-types/{clientType}', [Controllers\ClientTypeController::class, 'update'])->middleware('permission:edit-client-types')->name('client-types.update');
                        Route::delete('client-types/{clientType}', [Controllers\ClientTypeController::class, 'destroy'])->middleware('permission:delete-client-types')->name('client-types.destroy');
                        Route::put('client-types/{clientType}/toggle-status', [Controllers\ClientTypeController::class, 'toggleStatus'])->middleware('permission:edit-client-types')->name('client-types.toggle-status');
                    });
                    Route::middleware('permission:manage-case-categories')->group(function () {
                        Route::get('case-categories', [Controllers\CaseCategoryController::class, 'index'])->name('case-categories.index');
                        Route::post('case-categories', [Controllers\CaseCategoryController::class, 'store'])->middleware('permission:create-case-categories')->name('case-categories.store');
                        Route::put('case-categories/{caseCategory}', [Controllers\CaseCategoryController::class, 'update'])->middleware('permission:edit-case-categories')->name('case-categories.update');
                        Route::delete('case-categories/{caseCategory}', [Controllers\CaseCategoryController::class, 'destroy'])->middleware('permission:delete-case-categories')->name('case-categories.destroy');
                        Route::put('case-categories/{caseCategory}/toggle-status', [Controllers\CaseCategoryController::class, 'toggleStatus'])->middleware('permission:edit-case-categories')->name('case-categories.toggle-status');
                        Route::get('case-categories/{categoryId}/subcategories', [Controllers\CaseCategoryController::class, 'getSubcategories'])->name('case-categories.subcategories');
                        Route::get('case-categories/{subcategoryId}/case-types', [Controllers\CaseCategoryController::class, 'getCaseTypes'])->name('case-categories.case-types');
                    });

                    // Case Types routes
                    Route::middleware('permission:manage-case-types')->group(function () {
                        Route::get('case-types', [Controllers\CaseTypeController::class, 'index'])->name('case-types.index');
                        Route::post('case-types', [Controllers\CaseTypeController::class, 'store'])->middleware('permission:create-case-types')->name('case-types.store');
                        Route::put('case-types/{caseType}', [Controllers\CaseTypeController::class, 'update'])->middleware('permission:edit-case-types')->name('case-types.update');
                        Route::delete('case-types/{caseType}', [Controllers\CaseTypeController::class, 'destroy'])->middleware('permission:delete-case-types')->name('case-types.destroy');
                        Route::put('case-types/{caseType}/toggle-status', [Controllers\CaseTypeController::class, 'toggleStatus'])->middleware('permission:edit-case-types')->name('case-types.toggle-status');
                    });

                    // Hearing Type Management routes
                    Route::middleware('permission:manage-hearing-types')->group(function () {
                        Route::get('hearing-types', [Controllers\HearingTypeController::class, 'index'])->name('hearing-types.index');
                        Route::get('hearing-types/{hearingType}', [Controllers\HearingTypeController::class, 'show'])->middleware('permission:view-hearing-types')->name('hearing-types.show');
                        Route::post('hearing-types', [Controllers\HearingTypeController::class, 'store'])->middleware('permission:create-hearing-types')->name('hearing-types.store');
                        Route::put('hearing-types/{hearingType}', [Controllers\HearingTypeController::class, 'update'])->middleware('permission:edit-hearing-types')->name('hearing-types.update');
                        Route::delete('hearing-types/{hearingType}', [Controllers\HearingTypeController::class, 'destroy'])->middleware('permission:delete-hearing-types')->name('hearing-types.destroy');
                        Route::put('hearing-types/{hearingType}/toggle-status', [Controllers\HearingTypeController::class, 'toggleStatus'])->middleware('permission:edit-hearing-types')->name('hearing-types.toggle-status');
                    });

                    // Event Type routes
                    Route::middleware('permission:manage-event-types')->group(function () {
                        Route::get('event-types', [Controllers\EventTypeController::class, 'index'])->name('event-types.index');
                        Route::post('event-types', [Controllers\EventTypeController::class, 'store'])->middleware('permission:create-event-types')->name('event-types.store');
                        Route::put('event-types/{eventType}', [Controllers\EventTypeController::class, 'update'])->middleware('permission:edit-event-types')->name('event-types.update');
                        Route::delete('event-types/{eventType}', [Controllers\EventTypeController::class, 'destroy'])->middleware('permission:delete-event-types')->name('event-types.destroy');
                        Route::put('event-types/{eventType}/toggle-status', [Controllers\EventTypeController::class, 'toggleStatus'])->middleware('permission:edit-event-types')->name('event-types.toggle-status');
                    });
                    // Case Statuses routes
                    Route::middleware('permission:manage-case-statuses')->group(function () {
                        Route::get('case-statuses', [Controllers\CaseStatusController::class, 'index'])->name('case-statuses.index');
                        Route::post('case-statuses', [Controllers\CaseStatusController::class, 'store'])->middleware('permission:create-case-statuses')->name('case-statuses.store');
                        Route::put('case-statuses/{caseStatus}', [Controllers\CaseStatusController::class, 'update'])->middleware('permission:edit-case-statuses')->name('case-statuses.update');
                        Route::delete('case-statuses/{caseStatus}', [Controllers\CaseStatusController::class, 'destroy'])->middleware('permission:delete-case-statuses')->name('case-statuses.destroy');
                        Route::put('case-statuses/{caseStatus}/toggle-status', [Controllers\CaseStatusController::class, 'toggleStatus'])->middleware('permission:edit-case-statuses')->name('case-statuses.toggle-status');
                    });
                    // Court Type routes
                    Route::middleware('permission:manage-court-types')->group(function () {
                        Route::get('court-types', [Controllers\CourtTypeController::class, 'index'])->name('court-types.index');
                        Route::post('court-types', [Controllers\CourtTypeController::class, 'store'])->middleware('permission:create-court-types')->name('court-types.store');
                        Route::put('court-types/{courtType}', [Controllers\CourtTypeController::class, 'update'])->middleware('permission:edit-court-types')->name('court-types.update');
                        Route::delete('court-types/{courtType}', [Controllers\CourtTypeController::class, 'destroy'])->middleware('permission:delete-court-types')->name('court-types.destroy');
                        Route::put('court-types/{courtType}/toggle-status', [Controllers\CourtTypeController::class, 'toggleStatus'])->middleware('permission:edit-court-types')->name('court-types.toggle-status');
                    });
                    // Circle Type routes
                    Route::middleware('permission:manage-circle-types')->group(function () {
                        Route::get('circle-types', [Controllers\CircleTypeController::class, 'index'])->name('circle-types.index');
                        Route::post('circle-types', [Controllers\CircleTypeController::class, 'store'])->middleware('permission:create-circle-types')->name('circle-types.store');
                        Route::put('circle-types/{circleType}', [Controllers\CircleTypeController::class, 'update'])->middleware('permission:edit-circle-types')->name('circle-types.update');
                        Route::delete('circle-types/{circleType}', [Controllers\CircleTypeController::class, 'destroy'])->middleware('permission:delete-circle-types')->name('circle-types.destroy');
                        Route::put('circle-types/{circleType}/toggle-status', [Controllers\CircleTypeController::class, 'toggleStatus'])->middleware('permission:edit-circle-types')->name('circle-types.toggle-status');
                    });
                    // Document Category routes
                    Route::middleware('permission:manage-document-categories')->group(function () {
                        Route::get('document-categories', [Controllers\DocumentCategoryController::class, 'index'])->name('document-categories.index');
                        Route::post('document-categories', [Controllers\DocumentCategoryController::class, 'store'])->middleware('permission:create-document-categories')->name('document-categories.store');
                        Route::put('document-categories/{category}', [Controllers\DocumentCategoryController::class, 'update'])->middleware('permission:edit-document-categories')->name('document-categories.update');
                        Route::delete('document-categories/{category}', [Controllers\DocumentCategoryController::class, 'destroy'])->middleware('permission:delete-document-categories')->name('document-categories.destroy');
                        Route::put('document-categories/{category}/toggle-status', [Controllers\DocumentCategoryController::class, 'toggleStatus'])->middleware('permission:edit-document-categories')->name('document-categories.toggle-status');
                    });

                    // Document Type routes
                    Route::middleware('permission:manage-document-types')->group(function () {
                        Route::get('document-types', [Controllers\DocumentTypeController::class, 'index'])->name('document-types.index');
                        Route::post('document-types', [Controllers\DocumentTypeController::class, 'store'])->middleware('permission:create-document-types')->name('document-types.store');
                        Route::put('document-types/{documentType}', [Controllers\DocumentTypeController::class, 'update'])->middleware('permission:edit-document-types')->name('document-types.update');
                        Route::delete('document-types/{documentType}', [Controllers\DocumentTypeController::class, 'destroy'])->middleware('permission:delete-document-types')->name('document-types.destroy');
                        Route::put('document-types/{documentType}/toggle-status', [Controllers\DocumentTypeController::class, 'toggleStatus'])->middleware('permission:edit-document-types')->name('document-types.toggle-status');
                    });

                    // Research Type routes
                    Route::middleware('permission:manage-research-types')->group(function () {
                        Route::get('research-types', [Controllers\ResearchTypeController::class, 'index'])->name('research-types.index');
                        Route::post('research-types', [Controllers\ResearchTypeController::class, 'store'])->middleware('permission:create-research-types')->name('research-types.store');
                        Route::put('research-types/{researchType}', [Controllers\ResearchTypeController::class, 'update'])->middleware('permission:edit-research-types')->name('research-types.update');
                        Route::delete('research-types/{researchType}', [Controllers\ResearchTypeController::class, 'destroy'])->middleware('permission:delete-research-types')->name('research-types.destroy');
                        Route::put('research-types/{researchType}/toggle-status', [Controllers\ResearchTypeController::class, 'toggleStatus'])->middleware('permission:edit-research-types')->name('research-types.toggle-status');
                    });

                    // Research Source routes
                    Route::middleware('permission:manage-research-sources')->group(function () {
                        Route::get('research-sources', [Controllers\ResearchSourceController::class, 'index'])->name('research-sources.index');
                        Route::post('research-sources', [Controllers\ResearchSourceController::class, 'store'])->middleware('permission:create-research-sources')->name('research-sources.store');
                        Route::put('research-sources/{source}', [Controllers\ResearchSourceController::class, 'update'])->middleware('permission:edit-research-sources')->name('research-sources.update');
                        Route::delete('research-sources/{source}', [Controllers\ResearchSourceController::class, 'destroy'])->middleware('permission:delete-research-sources')->name('research-sources.destroy');
                        Route::put('research-sources/{source}/toggle-status', [Controllers\ResearchSourceController::class, 'toggleStatus'])->middleware('permission:edit-research-sources')->name('research-sources.toggle-status');
                    });

                    // Task Type routes
                    Route::middleware('permission:manage-task-types')->group(function () {
                        Route::get('task-types', [Controllers\TaskTypeController::class, 'index'])->name('task-types.index');
                        Route::post('task-types', [Controllers\TaskTypeController::class, 'store'])->middleware('permission:create-task-types')->name('task-types.store');
                        Route::put('task-types/{taskType}', [Controllers\TaskTypeController::class, 'update'])->middleware('permission:edit-task-types')->name('task-types.update');
                        Route::delete('task-types/{taskType}', [Controllers\TaskTypeController::class, 'destroy'])->middleware('permission:delete-task-types')->name('task-types.destroy');
                        Route::put('task-types/{taskType}/toggle-status', [Controllers\TaskTypeController::class, 'toggleStatus'])->middleware('permission:toggle-status-task-types')->name('task-types.toggle-status');
                    });

                    // Task Status routes
                    Route::middleware('permission:manage-task-statuses')->group(function () {
                        Route::get('task-statuses', [Controllers\TaskStatusController::class, 'index'])->name('task-statuses.index');
                        Route::post('task-statuses', [Controllers\TaskStatusController::class, 'store'])->middleware('permission:create-task-statuses')->name('task-statuses.store');
                        Route::put('task-statuses/{taskStatus}', [Controllers\TaskStatusController::class, 'update'])->middleware('permission:edit-task-statuses')->name('task-statuses.update');
                        Route::delete('task-statuses/{taskStatus}', [Controllers\TaskStatusController::class, 'destroy'])->middleware('permission:delete-task-statuses')->name('task-statuses.destroy');
                        Route::put('task-statuses/{taskStatus}/toggle-status', [Controllers\TaskStatusController::class, 'toggleStatus'])->middleware('permission:toggle-status-task-statuses')->name('task-statuses.toggle-status');
                    });

                    // Expense Category routes
                    Route::middleware('permission:manage-expense-categories')->group(function () {
                        Route::get('expense-categories', [Controllers\ExpenseCategoryController::class, 'index'])->name('expense-categories.index');
                        Route::post('expense-categories', [Controllers\ExpenseCategoryController::class, 'store'])->middleware('permission:create-expense-categories')->name('expense-categories.store');
                        Route::put('expense-categories/{expenseCategory}', [Controllers\ExpenseCategoryController::class, 'update'])->middleware('permission:edit-expense-categories')->name('expense-categories.update');
                        Route::delete('expense-categories/{expenseCategory}', [Controllers\ExpenseCategoryController::class, 'destroy'])->middleware('permission:delete-expense-categories')->name('expense-categories.destroy');
                        Route::put('expense-categories/{expenseCategory}/toggle-status', [Controllers\ExpenseCategoryController::class, 'toggleStatus'])->middleware('permission:toggle-status-expense-categories')->name('expense-categories.toggle-status');
                    });
                });

            // Document API routes
            Route::get('api/documents/{document}/download', [Controllers\DocumentController::class, 'apiDownload'])->middleware('permission:download-documents')->name('api.documents.download');

            // Client routes
            Route::middleware('permission:manage-clients')->group(function () {
                Route::get('clients', [Controllers\ClientController::class, 'index'])->name('clients.index');
                Route::get('clients/create', [Controllers\ClientController::class, 'create'])->middleware('permission:create-clients')->name('clients.create');
                Route::get('clients/{client}/edit', [Controllers\ClientController::class, 'edit'])->middleware('permission:edit-clients')->name('clients.edit');
                Route::get('clients/{client}', [Controllers\ClientController::class, 'show'])->middleware('permission:view-clients')->name('clients.show');
                Route::post('clients', [Controllers\ClientController::class, 'store'])->middleware('permission:create-clients')->name('clients.store');
                Route::put('clients/{client}', [Controllers\ClientController::class, 'update'])->middleware('permission:edit-clients')->name('clients.update');
                Route::delete('clients/{client}', [Controllers\ClientController::class, 'destroy'])->middleware('permission:delete-clients')->name('clients.destroy');
                Route::put('clients/{client}/toggle-status', [Controllers\ClientController::class, 'toggleStatus'])->middleware('permission:edit-clients')->name('clients.toggle-status');
                Route::put('clients/{client}/reset-password', [Controllers\ClientController::class, 'resetPassword'])->middleware('permission:edit-clients')->name('clients.reset-password');
            });

            // Client Document routes
            Route::middleware('permission:manage-client-documents')->group(function () {
                Route::get('client/documents', [Controllers\ClientDocumentController::class, 'index'])->name('clients.documents.index');
                Route::post('client/documents', [Controllers\ClientDocumentController::class, 'store'])->middleware('permission:create-client-documents')->name('clients.documents.store');
                Route::put('client/documents/{document}', [Controllers\ClientDocumentController::class, 'update'])->middleware('permission:edit-client-documents')->name('clients.documents.update');
                Route::delete('client/documents/{document}', [Controllers\ClientDocumentController::class, 'destroy'])->middleware('permission:delete-client-documents')->name('clients.documents.destroy');
                Route::get('client/documents/{document}/download', [Controllers\ClientDocumentController::class, 'download'])->middleware('permission:download-client-documents')->name('clients.documents.download');
            });

            // Client Billing Info routes
            Route::middleware('permission:manage-client-billing')->group(function () {
                Route::get('client/billing', [Controllers\ClientBillingInfoController::class, 'index'])->name('clients.billing.index');
                Route::post('client/billing', [Controllers\ClientBillingInfoController::class, 'store'])->middleware('permission:create-client-billing')->name('clients.billing.store');
                Route::put('client/billing/{billing}', [Controllers\ClientBillingInfoController::class, 'update'])->middleware('permission:edit-client-billing')->name('clients.billing.update');
                Route::delete('client/billing/{billing}', [Controllers\ClientBillingInfoController::class, 'destroy'])->middleware('permission:delete-client-billing')->name('clients.billing.destroy');
            });

            // Company Profile routes
            Route::middleware('permission:manage-company-profiles')->group(function () {
                Route::get('advocate/company-profiles', [Controllers\CompanyProfileController::class, 'index'])->name('advocate.company-profiles.index');
                Route::post('advocate/company-profiles', [Controllers\CompanyProfileController::class, 'store'])->middleware('permission:create-company-profiles')->name('advocate.company-profiles.store');
                Route::put('advocate/company-profiles/{profile}', [Controllers\CompanyProfileController::class, 'update'])->middleware('permission:edit-company-profiles')->name('advocate.company-profiles.update');
                Route::delete('advocate/company-profiles/{profile}', [Controllers\CompanyProfileController::class, 'destroy'])->middleware('permission:delete-company-profiles')->name('advocate.company-profiles.destroy');
            });

            // Practice Area routes
            Route::middleware('permission:manage-practice-areas')->group(function () {
                Route::get('advocate/practice-areas', [Controllers\PracticeAreaController::class, 'index'])->name('advocate.practice-areas.index');
                Route::post('advocate/practice-areas', [Controllers\PracticeAreaController::class, 'store'])->middleware('permission:create-practice-areas')->name('advocate.practice-areas.store');
                Route::put('advocate/practice-areas/{area}', [Controllers\PracticeAreaController::class, 'update'])->middleware('permission:edit-practice-areas')->name('advocate.practice-areas.update');
                Route::delete('advocate/practice-areas/{area}', [Controllers\PracticeAreaController::class, 'destroy'])->middleware('permission:delete-practice-areas')->name('advocate.practice-areas.destroy');
                Route::put('advocate/practice-areas/{area}/toggle-status', [Controllers\PracticeAreaController::class, 'toggleStatus'])->middleware('permission:edit-practice-areas')->name('advocate.practice-areas.toggle-status');
            });

            // Company Setting routes
            Route::middleware('permission:manage-company-settings')->group(function () {
                Route::get('advocate/company-settings', [Controllers\CompanySettingController::class, 'index'])->name('advocate.company-settings.index');
                Route::put('advocate/company-settings/{setting}', [Controllers\CompanySettingController::class, 'update'])->middleware('permission:edit-company-settings')->name('advocate.company-settings.update');
            });

            // Case Document routes
            Route::middleware('permission:manage-case-documents')->group(function () {
                Route::get('advocate/case-documents', [Controllers\CaseDocumentController::class, 'index'])->name('advocate.case-documents.index');
                Route::post('advocate/case-documents', [Controllers\CaseDocumentController::class, 'store'])->middleware('permission:create-case-documents')->name('advocate.case-documents.store');
                Route::put('advocate/case-documents/{document}', [Controllers\CaseDocumentController::class, 'update'])->middleware('permission:edit-case-documents')->name('advocate.case-documents.update');
                Route::delete('advocate/case-documents/{document}', [Controllers\CaseDocumentController::class, 'destroy'])->middleware('permission:delete-case-documents')->name('advocate.case-documents.destroy');
                Route::get('advocate/case-documents/{document}/download', [Controllers\CaseDocumentController::class, 'download'])->middleware('permission:download-case-documents')->name('advocate.case-documents.download');
            });

            // Case Note routes
            Route::middleware('permission:manage-case-notes')->group(function () {
                Route::get('advocate/case-notes', [Controllers\CaseNoteController::class, 'index'])->name('advocate.case-notes.index');
                Route::post('advocate/case-notes', [Controllers\CaseNoteController::class, 'store'])->middleware('permission:create-case-notes')->name('advocate.case-notes.store');
                Route::put('advocate/case-notes/{note}', [Controllers\CaseNoteController::class, 'update'])->middleware('permission:edit-case-notes')->name('advocate.case-notes.update');
                Route::delete('advocate/case-notes/{note}', [Controllers\CaseNoteController::class, 'destroy'])->middleware('permission:delete-case-notes')->name('advocate.case-notes.destroy');
            });

            // Document routes
            Route::middleware('permission:manage-documents')->group(function () {
                Route::get('document-management/documents', [Controllers\DocumentController::class, 'index'])->name('document-management.documents.index');
                Route::get('document-management/documents/{document}', [Controllers\DocumentController::class, 'show'])->middleware('permission:view-documents')->name('document-management.documents.show');
                Route::post('document-management/documents', [Controllers\DocumentController::class, 'store'])->middleware('permission:create-documents')->name('document-management.documents.store');
                Route::put('document-management/documents/{document}', [Controllers\DocumentController::class, 'update'])->middleware('permission:edit-documents')->name('document-management.documents.update');
                Route::delete('document-management/documents/{document}', [Controllers\DocumentController::class, 'destroy'])->middleware('permission:delete-documents')->name('document-management.documents.destroy');
                Route::get('document-management/documents/{document}/download', [Controllers\DocumentController::class, 'download'])->middleware('permission:download-documents')->name('document-management.documents.download');
            });

            // Document Version routes
            Route::middleware('permission:manage-document-versions')->group(function () {
                Route::get('document-management/versions', [Controllers\DocumentVersionController::class, 'index'])->name('document-management.versions.index');
                Route::post('document-management/versions', [Controllers\DocumentVersionController::class, 'store'])->middleware('permission:create-document-versions')->name('document-management.versions.store');
                Route::delete('document-management/versions/{version}', [Controllers\DocumentVersionController::class, 'destroy'])->middleware('permission:delete-document-versions')->name('document-management.versions.destroy');
                Route::get('document-management/versions/{version}/download', [Controllers\DocumentVersionController::class, 'download'])->middleware('permission:download-document-versions')->name('document-management.versions.download');
                Route::put('document-management/versions/{version}/restore', [Controllers\DocumentVersionController::class, 'restore'])->middleware('permission:restore-document-versions')->name('document-management.versions.restore');
            });

            // Document Comment routes
            Route::middleware('permission:manage-document-comments')->group(function () {
                Route::get('document-management/comments', [Controllers\DocumentCommentController::class, 'index'])->name('document-management.comments.index');
                Route::post('document-management/comments', [Controllers\DocumentCommentController::class, 'store'])->middleware('permission:create-document-comments')->name('document-management.comments.store');
                Route::put('document-management/comments/{comment}', [Controllers\DocumentCommentController::class, 'update'])->middleware('permission:edit-document-comments')->name('document-management.comments.update');
                Route::delete('document-management/comments/{comment}', [Controllers\DocumentCommentController::class, 'destroy'])->middleware('permission:delete-document-comments')->name('document-management.comments.destroy');
                Route::put('document-management/comments/{comment}/toggle-resolve', [Controllers\DocumentCommentController::class, 'toggleResolve'])->middleware('permission:resolve-document-comments')->name('document-management.comments.toggle-resolve');
            });

            // Document Permission routes
            Route::middleware('permission:manage-document-permissions')->group(function () {
                Route::get('document-management/permissions', [Controllers\DocumentPermissionController::class, 'index'])->name('document-management.permissions.index');
                Route::post('document-management/permissions', [Controllers\DocumentPermissionController::class, 'store'])->middleware('permission:create-document-permissions')->name('document-management.permissions.store');
                Route::put('document-management/permissions/{permission}', [Controllers\DocumentPermissionController::class, 'update'])->middleware('permission:edit-document-permissions')->name('document-management.permissions.update');
                Route::delete('document-management/permissions/{permission}', [Controllers\DocumentPermissionController::class, 'destroy'])->middleware('permission:delete-document-permissions')->name('document-management.permissions.destroy');
            });

            // Research Project routes
            Route::middleware('permission:manage-research-projects')->group(function () {
                Route::get('legal-research/projects', [Controllers\ResearchProjectController::class, 'index'])->name('legal-research.projects.index');
                Route::get('legal-research/projects/{project}', [Controllers\ResearchProjectController::class, 'show'])->middleware('permission:view-research-projects')->name('legal-research.projects.show');
                Route::post('legal-research/projects', [Controllers\ResearchProjectController::class, 'store'])->middleware('permission:create-research-projects')->name('legal-research.projects.store');
                Route::put('legal-research/projects/{project}', [Controllers\ResearchProjectController::class, 'update'])->middleware('permission:edit-research-projects')->name('legal-research.projects.update');
                Route::delete('legal-research/projects/{project}', [Controllers\ResearchProjectController::class, 'destroy'])->middleware('permission:delete-research-projects')->name('legal-research.projects.destroy');
                Route::put('legal-research/projects/{project}/toggle-status', [Controllers\ResearchProjectController::class, 'toggleStatus'])->middleware('permission:edit-research-projects')->name('legal-research.projects.toggle-status');
            });

            // Research Category routes
            Route::middleware('permission:manage-research-categories')->group(function () {
                Route::get('legal-research/categories', [Controllers\ResearchCategoryController::class, 'index'])->name('legal-research.categories.index');
                Route::post('legal-research/categories', [Controllers\ResearchCategoryController::class, 'store'])->middleware('permission:create-research-categories')->name('legal-research.categories.store');
                Route::put('legal-research/categories/{category}', [Controllers\ResearchCategoryController::class, 'update'])->middleware('permission:edit-research-categories')->name('legal-research.categories.update');
                Route::delete('legal-research/categories/{category}', [Controllers\ResearchCategoryController::class, 'destroy'])->middleware('permission:delete-research-categories')->name('legal-research.categories.destroy');
                Route::put('legal-research/categories/{category}/toggle-status', [Controllers\ResearchCategoryController::class, 'toggleStatus'])->middleware('permission:edit-research-categories')->name('legal-research.categories.toggle-status');
            });

            // Knowledge Article routes
            Route::middleware('permission:manage-knowledge-articles')->group(function () {
                Route::get('legal-research/knowledge', [Controllers\KnowledgeArticleController::class, 'index'])->name('legal-research.knowledge.index');
                Route::post('legal-research/knowledge', [Controllers\KnowledgeArticleController::class, 'store'])->middleware('permission:create-knowledge-articles')->name('legal-research.knowledge.store');
                Route::put('legal-research/knowledge/{article}', [Controllers\KnowledgeArticleController::class, 'update'])->middleware('permission:edit-knowledge-articles')->name('legal-research.knowledge.update');
                Route::delete('legal-research/knowledge/{article}', [Controllers\KnowledgeArticleController::class, 'destroy'])->middleware('permission:delete-knowledge-articles')->name('legal-research.knowledge.destroy');
                Route::put('legal-research/knowledge/{article}/publish', [Controllers\KnowledgeArticleController::class, 'publish'])->middleware('permission:publish-knowledge-articles')->name('legal-research.knowledge.publish');
            });

            // Legal Precedent routes
            Route::middleware('permission:manage-legal-precedents')->group(function () {
                Route::get('legal-research/precedents', [Controllers\LegalPrecedentController::class, 'index'])->name('legal-research.precedents.index');
                Route::post('legal-research/precedents', [Controllers\LegalPrecedentController::class, 'store'])->middleware('permission:create-legal-precedents')->name('legal-research.precedents.store');
                Route::put('legal-research/precedents/{precedent}', [Controllers\LegalPrecedentController::class, 'update'])->middleware('permission:edit-legal-precedents')->name('legal-research.precedents.update');
                Route::delete('legal-research/precedents/{precedent}', [Controllers\LegalPrecedentController::class, 'destroy'])->middleware('permission:delete-legal-precedents')->name('legal-research.precedents.destroy');
                Route::put('legal-research/precedents/{precedent}/toggle-status', [Controllers\LegalPrecedentController::class, 'toggleStatus'])->middleware('permission:edit-legal-precedents')->name('legal-research.precedents.toggle-status');
            });

            // Research Note routes
            Route::middleware('permission:manage-research-notes')->group(function () {
                Route::get('legal-research/notes', [Controllers\ResearchNoteController::class, 'index'])->name('legal-research.notes.index');
                Route::post('legal-research/notes', [Controllers\ResearchNoteController::class, 'store'])->middleware('permission:create-research-notes')->name('legal-research.notes.store');
                Route::put('legal-research/notes/{note}', [Controllers\ResearchNoteController::class, 'update'])->middleware('permission:edit-research-notes')->name('legal-research.notes.update');
                Route::delete('legal-research/notes/{note}', [Controllers\ResearchNoteController::class, 'destroy'])->middleware('permission:delete-research-notes')->name('legal-research.notes.destroy');
            });

            // Research Citation routes
            Route::middleware('permission:manage-research-citations')->group(function () {
                Route::get('legal-research/citations', [Controllers\ResearchCitationController::class, 'index'])->name('legal-research.citations.index');
                Route::post('legal-research/citations', [Controllers\ResearchCitationController::class, 'store'])->middleware('permission:create-research-citations')->name('legal-research.citations.store');
                Route::put('legal-research/citations/{citation}', [Controllers\ResearchCitationController::class, 'update'])->middleware('permission:edit-research-citations')->name('legal-research.citations.update');
                Route::delete('legal-research/citations/{citation}', [Controllers\ResearchCitationController::class, 'destroy'])->middleware('permission:delete-research-citations')->name('legal-research.citations.destroy');
            });

            // Hearing routes
            Route::middleware('permission:manage-hearings')->group(function () {
                Route::get('hearings', [Controllers\HearingController::class, 'index'])->name('hearings.index');
                Route::post('hearings', [Controllers\HearingController::class, 'store'])->middleware('permission:create-hearings')->name('hearings.store');
                Route::put('hearings/{hearing}', [Controllers\HearingController::class, 'update'])->middleware('permission:edit-hearings')->name('hearings.update');
                Route::delete('hearings/{hearing}', [Controllers\HearingController::class, 'destroy'])->middleware('permission:delete-hearings')->name('hearings.destroy');
            });

            // Calendar route
            Route::get('calendar', [Controllers\CalendarController::class, 'index'])->name('calendar.index');

            // Google Calendar API routes
            Route::get('api/google-calendar/events', [Controllers\GoogleCalendarController::class, 'getEvents'])->name('google-calendar.events');
            Route::post('api/google-calendar/sync', [Controllers\GoogleCalendarController::class, 'syncEvents'])->name('google-calendar.sync');
            Route::get('google-calendar/auth', [Controllers\GoogleCalendarController::class, 'authorizeGoogleCalendar'])->name('google-calendar.auth');
            Route::get('google-calendar/callback', [Controllers\GoogleCalendarController::class, 'callback'])->name('google-calendar.callback');

            // Court Management routes
            Route::middleware('permission:manage-courts')->group(function () {
                Route::get('courts', [Controllers\CourtController::class, 'index'])->name('courts.index');
                Route::get('courts/{court}', [Controllers\CourtController::class, 'show'])->middleware('permission:view-courts')->name('courts.show');
                Route::post('courts', [Controllers\CourtController::class, 'store'])->middleware('permission:create-courts')->name('courts.store');
                Route::put('courts/{court}', [Controllers\CourtController::class, 'update'])->middleware('permission:edit-courts')->name('courts.update');
                Route::delete('courts/{court}', [Controllers\CourtController::class, 'destroy'])->middleware('permission:delete-courts')->name('courts.destroy');
                Route::put('courts/{court}/toggle-status', [Controllers\CourtController::class, 'toggleStatus'])->middleware('permission:edit-courts')->name('courts.toggle-status');
            });

            // Company Settings in Settings page routes
            Route::middleware('permission:manage-company-settings')->group(function () {
                Route::post('settings/company', [Controllers\Settings\SettingsController::class, 'storeCompanySetting'])->name('settings.company.store');
                Route::put('settings/company/{id}', [Controllers\Settings\SettingsController::class, 'updateCompanySetting'])->name('settings.company.update');
                Route::delete('settings/company/{id}', [Controllers\Settings\SettingsController::class, 'destroyCompanySetting'])->name('settings.company.destroy');
            });

            // Case Management routes
            Route::middleware('permission:manage-cases')->group(function () {
                Route::get('cases', [Controllers\CaseController::class, 'index'])->name('cases.index');
                Route::get('cases/create', [Controllers\CaseController::class, 'create'])->middleware('permission:create-cases')->name('cases.create');
                Route::get('cases/{case}/edit', [Controllers\CaseController::class, 'edit'])->middleware('permission:edit-cases')->name('cases.edit');
                Route::get('cases/{case}', [Controllers\CaseController::class, 'show'])->middleware('permission:view-cases')->name('cases.show');
                Route::post('cases', [Controllers\CaseController::class, 'store'])->middleware('permission:create-cases')->name('cases.store');
                Route::put('cases/{case}', [Controllers\CaseController::class, 'update'])->middleware('permission:edit-cases')->name('cases.update');
                Route::delete('cases/{case}', [Controllers\CaseController::class, 'destroy'])->middleware('permission:delete-cases')->name('cases.destroy');
                Route::put('cases/{case}/toggle-status', [Controllers\CaseController::class, 'toggleStatus'])->middleware('permission:edit-cases')->name('cases.toggle-status');
            });

            // Case Timelines routes
            Route::middleware('permission:manage-case-timelines')->group(function () {
                Route::get('cases/case-timelines', [Controllers\CaseTimelineController::class, 'index'])->name('cases.case-timelines.index');
                Route::post('cases/case-timelines', [Controllers\CaseTimelineController::class, 'store'])->middleware('permission:create-case-timelines')->name('cases.case-timelines.store');
                Route::put('cases/case-timelines/{timeline}', [Controllers\CaseTimelineController::class, 'update'])->middleware('permission:edit-case-timelines')->name('cases.case-timelines.update');
                Route::delete('cases/case-timelines/{timeline}', [Controllers\CaseTimelineController::class, 'destroy'])->middleware('permission:delete-case-timelines')->name('cases.case-timelines.destroy');
                Route::put('cases/case-timelines/{timeline}/toggle-status', [Controllers\CaseTimelineController::class, 'toggleStatus'])->middleware('permission:edit-case-timelines')->name('cases.case-timelines.toggle-status');
            });

            // Case Team Members routes
            Route::middleware('permission:manage-case-team-members')->group(function () {
                Route::get('cases/case-team-members', [Controllers\CaseTeamMemberController::class, 'index'])->name('cases.case-team-members.index');
                Route::post('cases/case-team-members', [Controllers\CaseTeamMemberController::class, 'store'])->middleware('permission:create-case-team-members')->name('cases.case-team-members.store');
                Route::put('cases/case-team-members/{teamMember}', [Controllers\CaseTeamMemberController::class, 'update'])->middleware('permission:edit-case-team-members')->name('cases.case-team-members.update');
                Route::delete('cases/case-team-members/{teamMember}', [Controllers\CaseTeamMemberController::class, 'destroy'])->middleware('permission:delete-case-team-members')->name('cases.case-team-members.destroy');
                Route::put('cases/case-team-members/{teamMember}/toggle-status', [Controllers\CaseTeamMemberController::class, 'toggleStatus'])->middleware('permission:edit-case-team-members')->name('cases.case-team-members.toggle-status');
            });

            // ChatGPT routes
            Route::post('api/chatgpt/generate', [Controllers\ChatGptController::class, 'generate'])->name('chatgpt.generate');

            // Compliance Requirements routes
            Route::middleware('permission:manage-compliance-requirements')->group(function () {
                Route::get('compliance/requirements', [Controllers\ComplianceRequirementController::class, 'index'])->name('compliance.requirements.index');
                Route::post('compliance/requirements', [Controllers\ComplianceRequirementController::class, 'store'])->middleware('permission:create-compliance-requirements')->name('compliance.requirements.store');
                Route::put('compliance/requirements/{requirement}', [Controllers\ComplianceRequirementController::class, 'update'])->middleware('permission:edit-compliance-requirements')->name('compliance.requirements.update');
                Route::delete('compliance/requirements/{requirement}', [Controllers\ComplianceRequirementController::class, 'destroy'])->middleware('permission:delete-compliance-requirements')->name('compliance.requirements.destroy');
                Route::put('compliance/requirements/{requirement}/toggle-status', [Controllers\ComplianceRequirementController::class, 'toggleStatus'])->middleware('permission:toggle-status-compliance-requirements')->name('compliance.requirements.toggle-status');
            });

            // Compliance Categories routes
            Route::middleware('permission:manage-compliance-categories')->group(function () {
                Route::get('compliance/categories', [Controllers\ComplianceCategoryController::class, 'index'])->name('compliance.categories.index');
                Route::post('compliance/categories', [Controllers\ComplianceCategoryController::class, 'store'])->middleware('permission:create-compliance-categories')->name('compliance.categories.store');
                Route::put('compliance/categories/{category}', [Controllers\ComplianceCategoryController::class, 'update'])->middleware('permission:edit-compliance-categories')->name('compliance.categories.update');
                Route::delete('compliance/categories/{category}', [Controllers\ComplianceCategoryController::class, 'destroy'])->middleware('permission:delete-compliance-categories')->name('compliance.categories.destroy');
                Route::put('compliance/categories/{category}/toggle-status', [Controllers\ComplianceCategoryController::class, 'toggleStatus'])->middleware('permission:toggle-status-compliance-categories')->name('compliance.categories.toggle-status');
            });

            // Compliance Frequencies routes
            Route::middleware('permission:manage-compliance-frequencies')->group(function () {
                Route::get('compliance/frequencies', [Controllers\ComplianceFrequencyController::class, 'index'])->name('compliance.frequencies.index');
                Route::post('compliance/frequencies', [Controllers\ComplianceFrequencyController::class, 'store'])->middleware('permission:create-compliance-frequencies')->name('compliance.frequencies.store');
                Route::put('compliance/frequencies/{frequency}', [Controllers\ComplianceFrequencyController::class, 'update'])->middleware('permission:edit-compliance-frequencies')->name('compliance.frequencies.update');
                Route::delete('compliance/frequencies/{frequency}', [Controllers\ComplianceFrequencyController::class, 'destroy'])->middleware('permission:delete-compliance-frequencies')->name('compliance.frequencies.destroy');
                Route::put('compliance/frequencies/{frequency}/toggle-status', [Controllers\ComplianceFrequencyController::class, 'toggleStatus'])->middleware('permission:toggle-status-compliance-frequencies')->name('compliance.frequencies.toggle-status');
            });

            // Professional Licenses routes
            Route::middleware('permission:manage-professional-licenses')->group(function () {
                Route::get('compliance/professional-licenses', [Controllers\ProfessionalLicenseController::class, 'index'])->name('compliance.professional-licenses.index');
                Route::post('compliance/professional-licenses', [Controllers\ProfessionalLicenseController::class, 'store'])->middleware('permission:create-professional-licenses')->name('compliance.professional-licenses.store');
                Route::put('compliance/professional-licenses/{license}', [Controllers\ProfessionalLicenseController::class, 'update'])->middleware('permission:edit-professional-licenses')->name('compliance.professional-licenses.update');
                Route::delete('compliance/professional-licenses/{license}', [Controllers\ProfessionalLicenseController::class, 'destroy'])->middleware('permission:delete-professional-licenses')->name('compliance.professional-licenses.destroy');
                Route::put('compliance/professional-licenses/{license}/toggle-status', [Controllers\ProfessionalLicenseController::class, 'toggleStatus'])->middleware('permission:toggle-status-professional-licenses')->name('compliance.professional-licenses.toggle-status');
            });

            // Regulatory Bodies routes
            Route::middleware('permission:manage-regulatory-bodies')->group(function () {
                Route::get('compliance/regulatory-bodies', [Controllers\RegulatoryBodyController::class, 'index'])->name('compliance.regulatory-bodies.index');
                Route::post('compliance/regulatory-bodies', [Controllers\RegulatoryBodyController::class, 'store'])->middleware('permission:create-regulatory-bodies')->name('compliance.regulatory-bodies.store');
                Route::put('compliance/regulatory-bodies/{body}', [Controllers\RegulatoryBodyController::class, 'update'])->middleware('permission:edit-regulatory-bodies')->name('compliance.regulatory-bodies.update');
                Route::delete('compliance/regulatory-bodies/{body}', [Controllers\RegulatoryBodyController::class, 'destroy'])->middleware('permission:delete-regulatory-bodies')->name('compliance.regulatory-bodies.destroy');
                Route::put('compliance/regulatory-bodies/{body}/toggle-status', [Controllers\RegulatoryBodyController::class, 'toggleStatus'])->middleware('permission:toggle-status-regulatory-bodies')->name('compliance.regulatory-bodies.toggle-status');
            });

            // CLE Tracking routes
            Route::middleware('permission:manage-cle-tracking')->group(function () {
                Route::get('compliance/cle-tracking', [Controllers\CleTrackingController::class, 'index'])->name('compliance.cle-tracking.index');
                Route::post('compliance/cle-tracking', [Controllers\CleTrackingController::class, 'store'])->middleware('permission:create-cle-tracking')->name('compliance.cle-tracking.store');
                Route::put('compliance/cle-tracking/{cleTracking}', [Controllers\CleTrackingController::class, 'update'])->middleware('permission:edit-cle-tracking')->name('compliance.cle-tracking.update');
                Route::delete('compliance/cle-tracking/{cleTracking}', [Controllers\CleTrackingController::class, 'destroy'])->middleware('permission:delete-cle-tracking')->name('compliance.cle-tracking.destroy');
                Route::get('compliance/cle-tracking/{cleTracking}/download', [Controllers\CleTrackingController::class, 'download'])->middleware('permission:download-cle-tracking')->name('compliance.cle-tracking.download');
            });

            // Compliance Policies routes
            Route::middleware('permission:manage-compliance-policies')->group(function () {
                Route::get('compliance/policies', [Controllers\CompliancePolicyController::class, 'index'])->name('compliance.policies.index');
                Route::get('compliance/policies/create', [Controllers\CompliancePolicyController::class, 'create'])->middleware('permission:create-compliance-policies')->name('compliance.policies.create');
                Route::post('compliance/policies', [Controllers\CompliancePolicyController::class, 'store'])->middleware('permission:create-compliance-policies')->name('compliance.policies.store');
                Route::get('compliance/policies/{policy}', [Controllers\CompliancePolicyController::class, 'show'])->middleware('permission:view-compliance-policies')->name('compliance.policies.show');
                Route::get('compliance/policies/{policy}/edit', [Controllers\CompliancePolicyController::class, 'edit'])->middleware('permission:edit-compliance-policies')->name('compliance.policies.edit');
                Route::put('compliance/policies/{policy}', [Controllers\CompliancePolicyController::class, 'update'])->middleware('permission:edit-compliance-policies')->name('compliance.policies.update');
                Route::delete('compliance/policies/{policy}', [Controllers\CompliancePolicyController::class, 'destroy'])->middleware('permission:delete-compliance-policies')->name('compliance.policies.destroy');
                Route::put('compliance/policies/{policy}/toggle-status', [Controllers\CompliancePolicyController::class, 'toggleStatus'])->middleware('permission:toggle-status-compliance-policies')->name('compliance.policies.toggle-status');
            });

            // Risk Categories routes
            Route::middleware('permission:manage-risk-categories')->group(function () {
                Route::get('compliance/risk-categories', [Controllers\RiskCategoryController::class, 'index'])->name('compliance.risk-categories.index');
                Route::post('compliance/risk-categories', [Controllers\RiskCategoryController::class, 'store'])->middleware('permission:create-risk-categories')->name('compliance.risk-categories.store');
                Route::put('compliance/risk-categories/{category}', [Controllers\RiskCategoryController::class, 'update'])->middleware('permission:edit-risk-categories')->name('compliance.risk-categories.update');
                Route::delete('compliance/risk-categories/{category}', [Controllers\RiskCategoryController::class, 'destroy'])->middleware('permission:delete-risk-categories')->name('compliance.risk-categories.destroy');
                Route::put('compliance/risk-categories/{category}/toggle-status', [Controllers\RiskCategoryController::class, 'toggleStatus'])->middleware('permission:toggle-status-risk-categories')->name('compliance.risk-categories.toggle-status');
            });

            // Risk Assessments routes
            Route::middleware('permission:manage-risk-assessments')->group(function () {
                Route::get('compliance/risk-assessments', [Controllers\RiskAssessmentController::class, 'index'])->name('compliance.risk-assessments.index');
                Route::post('compliance/risk-assessments', [Controllers\RiskAssessmentController::class, 'store'])->middleware('permission:create-risk-assessments')->name('compliance.risk-assessments.store');
                Route::put('compliance/risk-assessments/{riskAssessment}', [Controllers\RiskAssessmentController::class, 'update'])->middleware('permission:edit-risk-assessments')->name('compliance.risk-assessments.update');
                Route::delete('compliance/risk-assessments/{riskAssessment}', [Controllers\RiskAssessmentController::class, 'destroy'])->middleware('permission:delete-risk-assessments')->name('compliance.risk-assessments.destroy');
            });

            // Audit Types routes
            Route::middleware('permission:manage-audit-types')->group(function () {
                Route::get('compliance/audit-types', [Controllers\AuditTypeController::class, 'index'])->name('compliance.audit-types.index');
                Route::post('compliance/audit-types', [Controllers\AuditTypeController::class, 'store'])->middleware('permission:create-audit-types')->name('compliance.audit-types.store');
                Route::put('compliance/audit-types/{auditType}', [Controllers\AuditTypeController::class, 'update'])->middleware('permission:edit-audit-types')->name('compliance.audit-types.update');
                Route::delete('compliance/audit-types/{auditType}', [Controllers\AuditTypeController::class, 'destroy'])->middleware('permission:delete-audit-types')->name('compliance.audit-types.destroy');
                Route::put('compliance/audit-types/{auditType}/toggle-status', [Controllers\AuditTypeController::class, 'toggleStatus'])->middleware('permission:toggle-status-audit-types')->name('compliance.audit-types.toggle-status');
            });

            // Compliance Audits routes
            Route::middleware('permission:manage-compliance-audits')->group(function () {
                Route::get('compliance/audits', [Controllers\ComplianceAuditController::class, 'index'])->name('compliance.audits.index');
                Route::post('compliance/audits', [Controllers\ComplianceAuditController::class, 'store'])->middleware('permission:create-compliance-audits')->name('compliance.audits.store');
                Route::put('compliance/audits/{audit}', [Controllers\ComplianceAuditController::class, 'update'])->middleware('permission:edit-compliance-audits')->name('compliance.audits.update');
                Route::delete('compliance/audits/{audit}', [Controllers\ComplianceAuditController::class, 'destroy'])->middleware('permission:delete-compliance-audits')->name('compliance.audits.destroy');
            });

            // Time Entry routes
            Route::middleware('permission:manage-time-entries')->group(function () {
                Route::get('billing/time-entries', [Controllers\TimeEntryController::class, 'index'])->name('billing.time-entries.index');
                Route::post('billing/time-entries', [Controllers\TimeEntryController::class, 'store'])->middleware('permission:create-time-entries')->name('billing.time-entries.store');
                Route::put('billing/time-entries/{timeEntry}', [Controllers\TimeEntryController::class, 'update'])->middleware('permission:edit-time-entries')->name('billing.time-entries.update');
                Route::delete('billing/time-entries/{timeEntry}', [Controllers\TimeEntryController::class, 'destroy'])->middleware('permission:delete-time-entries')->name('billing.time-entries.destroy');
                Route::put('billing/time-entries/{timeEntry}/approve', [Controllers\TimeEntryController::class, 'approve'])->middleware('permission:approve-time-entries')->name('billing.time-entries.approve');
                Route::post('billing/time-entries/start-timer', [Controllers\TimeEntryController::class, 'startTimer'])->middleware('permission:start-timer')->name('billing.time-entries.start-timer');
                Route::put('billing/time-entries/{timeEntry}/stop-timer', [Controllers\TimeEntryController::class, 'stopTimer'])->middleware('permission:stop-timer')->name('billing.time-entries.stop-timer');
            });

            // Billing Rate routes
            Route::middleware('permission:manage-billing-rates')->group(function () {
                Route::get('billing/billing-rates', [Controllers\BillingRateController::class, 'index'])->name('billing.billing-rates.index');
                Route::post('billing/billing-rates', [Controllers\BillingRateController::class, 'store'])->middleware('permission:create-billing-rates')->name('billing.billing-rates.store');
                Route::put('billing/billing-rates/{billingRate}', [Controllers\BillingRateController::class, 'update'])->middleware('permission:edit-billing-rates')->name('billing.billing-rates.update');
                Route::delete('billing/billing-rates/{billingRate}', [Controllers\BillingRateController::class, 'destroy'])->middleware('permission:delete-billing-rates')->name('billing.billing-rates.destroy');
                Route::put('billing/billing-rates/{billingRate}/toggle-status', [Controllers\BillingRateController::class, 'toggleStatus'])->middleware('permission:toggle-status-billing-rates')->name('billing.billing-rates.toggle-status');
            });

            // Fee Type routes
            Route::middleware('permission:manage-fee-types')->group(function () {
                Route::get('billing/fee-types', [Controllers\FeeTypeController::class, 'index'])->name('billing.fee-types.index');
                Route::post('billing/fee-types', [Controllers\FeeTypeController::class, 'store'])->middleware('permission:create-fee-types')->name('billing.fee-types.store');
                Route::put('billing/fee-types/{feeType}', [Controllers\FeeTypeController::class, 'update'])->middleware('permission:edit-fee-types')->name('billing.fee-types.update');
                Route::delete('billing/fee-types/{feeType}', [Controllers\FeeTypeController::class, 'destroy'])->middleware('permission:delete-fee-types')->name('billing.fee-types.destroy');
                Route::put('billing/fee-types/{feeType}/toggle-status', [Controllers\FeeTypeController::class, 'toggleStatus'])->middleware('permission:toggle-status-fee-types')->name('billing.fee-types.toggle-status');
            });

            // Fee Structure routes
            Route::middleware('permission:manage-fee-structures')->group(function () {
                Route::get('billing/fee-structures', [Controllers\FeeStructureController::class, 'index'])->name('billing.fee-structures.index');
                Route::post('billing/fee-structures', [Controllers\FeeStructureController::class, 'store'])->middleware('permission:create-fee-structures')->name('billing.fee-structures.store');
                Route::put('billing/fee-structures/{feeStructure}', [Controllers\FeeStructureController::class, 'update'])->middleware('permission:edit-fee-structures')->name('billing.fee-structures.update');
                Route::delete('billing/fee-structures/{feeStructure}', [Controllers\FeeStructureController::class, 'destroy'])->middleware('permission:delete-fee-structures')->name('billing.fee-structures.destroy');
                Route::put('billing/fee-structures/{feeStructure}/toggle-status', [Controllers\FeeStructureController::class, 'toggleStatus'])->middleware('permission:toggle-status-fee-structures')->name('billing.fee-structures.toggle-status');
            });

            // Expense routes
            Route::middleware('permission:manage-expenses')->group(function () {
                Route::get('billing/expenses', [Controllers\ExpenseController::class, 'index'])->name('billing.expenses.index');
                Route::post('billing/expenses', [Controllers\ExpenseController::class, 'store'])->middleware('permission:create-expenses')->name('billing.expenses.store');
                Route::put('billing/expenses/{expense}', [Controllers\ExpenseController::class, 'update'])->middleware('permission:edit-expenses')->name('billing.expenses.update');
                Route::delete('billing/expenses/{expense}', [Controllers\ExpenseController::class, 'destroy'])->middleware('permission:delete-expenses')->name('billing.expenses.destroy');
                Route::put('billing/expenses/{expense}/approve', [Controllers\ExpenseController::class, 'approve'])->middleware('permission:approve-expenses')->name('billing.expenses.approve');
            });

            // Invoice routes
            Route::middleware('permission:manage-invoices')->group(function () {
                Route::get('billing/invoices', [Controllers\InvoiceController::class, 'index'])->name('billing.invoices.index');
                Route::get('billing/invoices/create', [Controllers\InvoiceController::class, 'create'])->middleware('permission:create-invoices')->name('billing.invoices.create');
                Route::get('billing/invoices/{invoice}', [Controllers\InvoiceController::class, 'show'])->middleware('permission:view-invoices')->name('billing.invoices.show');
                Route::get('billing/invoices/{invoice}/edit', [Controllers\InvoiceController::class, 'edit'])->middleware('permission:edit-invoices')->name('billing.invoices.edit');
                Route::get('billing/invoices/{invoice}/generate', [Controllers\InvoiceController::class, 'generate'])->middleware('permission:view-invoices')->name('billing.invoices.generate');
                Route::get('invoices/{invoice}/pdf', [Controllers\InvoicePdfController::class, 'show'])->middleware('permission:view-invoices')->name('invoices.pdf');
                Route::post('billing/invoices', [Controllers\InvoiceController::class, 'store'])->middleware('permission:create-invoices')->name('billing.invoices.store');
                Route::put('billing/invoices/{invoice}', [Controllers\InvoiceController::class, 'update'])->middleware('permission:edit-invoices')->name('billing.invoices.update');
                Route::delete('billing/invoices/{invoice}', [Controllers\InvoiceController::class, 'destroy'])->middleware('permission:delete-invoices')->name('billing.invoices.destroy');
                Route::put('billing/invoices/{invoice}/send', [Controllers\InvoiceController::class, 'send'])->middleware('permission:send-invoices')->name('billing.invoices.send');
                Route::post('billing/invoices/generate-from-time-and-expenses', [Controllers\InvoiceController::class, 'generateFromTimeAndExpenses'])->middleware('permission:create-invoices')->name('billing.invoices.generate-from-time-and-expenses');
            });

            // Payment routes
            Route::middleware('permission:manage-payments')->group(function () {
                Route::get('billing/payments', [Controllers\PaymentController::class, 'index'])->name('billing.payments.index');
                Route::post('billing/payments', [Controllers\PaymentController::class, 'store'])->middleware('permission:create-payments')->name('billing.payments.store');
                Route::put('billing/payments/{payment}', [Controllers\PaymentController::class, 'update'])->middleware('permission:edit-payments')->name('billing.payments.update');
                Route::delete('billing/payments/{payment}', [Controllers\PaymentController::class, 'destroy'])->middleware('permission:delete-payments')->name('billing.payments.destroy');
                Route::post('billing/payments/{payment}/approve', [Controllers\PaymentController::class, 'approve'])->name('billing.payments.approve');
                Route::post('billing/payments/{payment}/reject', [Controllers\PaymentController::class, 'reject'])->name('billing.payments.reject');
            });

            // Task Management routes
            Route::middleware('permission:manage-tasks')->group(function () {
                Route::get('tasks', [Controllers\TaskController::class, 'index'])->name('tasks.index');
                Route::get('tasks/{task}', [Controllers\TaskController::class, 'show'])->middleware('permission:view-tasks')->name('tasks.show');
                Route::post('tasks', [Controllers\TaskController::class, 'store'])->middleware('permission:create-tasks')->name('tasks.store');
                Route::put('tasks/{task}', [Controllers\TaskController::class, 'update'])->middleware('permission:edit-tasks')->name('tasks.update');
                Route::delete('tasks/{task}', [Controllers\TaskController::class, 'destroy'])->middleware('permission:delete-tasks')->name('tasks.destroy');
                Route::put('tasks/{task}/toggle-status', [Controllers\TaskController::class, 'toggleStatus'])->middleware('permission:toggle-status-tasks')->name('tasks.toggle-status');
                Route::get('api/tasks/case-users/{case}', [Controllers\TaskController::class, 'getCaseUsers'])->name('api.tasks.case-users');
                Route::get('api/clients/{client}/cases', [Controllers\InvoiceController::class, 'getClientCases'])->name('api.clients.cases');
                Route::get('api/cases/{case}/time-entries', [Controllers\InvoiceController::class, 'getCaseTimeEntries'])->name('api.cases.time-entries');
                Route::get('api/clients/{client}/time-entries', [Controllers\InvoiceController::class, 'getClientTimeEntries'])->name('api.clients.time-entries');
            });

            // Workflow routes
            Route::middleware('permission:manage-workflows')->group(function () {
                Route::get('task/workflows', [Controllers\WorkflowController::class, 'index'])->name('tasks.workflows.index');
                Route::post('task/workflows', [Controllers\WorkflowController::class, 'store'])->middleware('permission:create-workflows')->name('tasks.workflows.store');
                Route::put('task/workflows/{workflow}', [Controllers\WorkflowController::class, 'update'])->middleware('permission:edit-workflows')->name('tasks.workflows.update');
                Route::delete('task/workflows/{workflow}', [Controllers\WorkflowController::class, 'destroy'])->middleware('permission:delete-workflows')->name('tasks.workflows.destroy');
                Route::put('task/workflows/{workflow}/toggle-status', [Controllers\WorkflowController::class, 'toggleStatus'])->middleware('permission:toggle-status-workflows')->name('tasks.workflows.toggle-status');
            });

            // Task Comment routes
            Route::middleware('permission:manage-task-comments')->group(function () {
                Route::get('task/task-comments', [Controllers\TaskCommentController::class, 'index'])->name('tasks.task-comments.index');
                Route::post('task/task-comments', [Controllers\TaskCommentController::class, 'store'])->middleware('permission:create-task-comments')->name('tasks.task-comments.store');
                Route::put('task/task-comments/{taskComment}', [Controllers\TaskCommentController::class, 'update'])->middleware('permission:edit-task-comments')->name('tasks.task-comments.update');
                Route::delete('task/task-comments/{taskComment}', [Controllers\TaskCommentController::class, 'destroy'])->middleware('permission:delete-task-comments')->name('tasks.task-comments.destroy');
            });

            // Communication & Collaboration Routes
            Route::prefix('communication')->name('communication.')->group(function () {
                // Messages
                Route::middleware('permission:manage-messages')->group(function () {
                    Route::get('/messages', [Controllers\MessageController::class, 'index'])->name('messages.index');
                    Route::get('/messages/{conversation}', [Controllers\MessageController::class, 'show'])->middleware('permission:view-messages')->name('messages.show');
                    Route::post('/messages', [Controllers\MessageController::class, 'store'])->middleware('permission:send-messages')->name('messages.store');
                    Route::delete('/messages/{conversation}', [Controllers\MessageController::class, 'destroy'])->middleware('permission:delete-messages')->name('messages.destroy');

                    // API endpoints for polling
                    Route::get('/api/unread-count', [Controllers\MessageController::class, 'getUnreadCount'])->name('messages.unread-count');
                    Route::get('/api/recent-messages', [Controllers\MessageController::class, 'getRecentMessages'])->name('messages.recent');
                    Route::get('/messages/user/{user}', [Controllers\MessageController::class, 'getUserDetails'])->name('messages.getUserDetails');
                });
            });

            // Language management
            Route::get('manage-language/{lang?}', [Controllers\LanguageController::class, 'managePage'])->middleware('permission:manage-language')->name('manage-language');
            Route::get('language/load', [Controllers\LanguageController::class, 'load'])->name('language.load');
            Route::match(['POST', 'PATCH'], 'language/save', [Controllers\LanguageController::class, 'save'])->middleware('permission:edit-language')->name('language.save');
            Route::post('language/create', [Controllers\LanguageController::class, 'createLanguage'])->middleware('permission:manage-language')->name('language.create');
            Route::delete('languages/{languageCode}', [Controllers\LanguageController::class, 'deleteLanguage'])->middleware('permission:manage-language')->name('languages.delete');
            Route::patch('languages/{languageCode}/toggle', [Controllers\LanguageController::class, 'toggleLanguageStatus'])->middleware('permission:manage-language')->name('languages.toggle');

            // Landing Page content management (Super Admin only)
            Route::middleware('App\Http\Middleware\SuperAdminMiddleware')->group(function () {
                Route::get('landing-page/settings', [Controllers\LandingPageController::class, 'settings'])->name('landing-page.settings');
                Route::post('landing-page/settings', [Controllers\LandingPageController::class, 'updateSettings'])->name('landing-page.settings.update');

                Route::resource('landing-page/custom-pages', CustomPageController::class)->names([
                    'index' => 'landing-page.custom-pages.index',
                    'store' => 'landing-page.custom-pages.store',
                    'update' => 'landing-page.custom-pages.update',
                    'destroy' => 'landing-page.custom-pages.destroy',
                ]);
            });
            // Impersonation routes
            Route::middleware('App\Http\Middleware\SuperAdminMiddleware')->group(function () {
                Route::get('impersonate/{userId}', [Controllers\ImpersonateController::class, 'start'])->name('impersonate.start');
            });

            Route::post('impersonate/leave', [Controllers\ImpersonateController::class, 'leave'])->name('impersonate.leave');
        }); // End plan.access middleware group
    });
    Route::get('/', function () {
        $settings = Settings::all();

        dd($settings);
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });

});
