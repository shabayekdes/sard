<?php

use App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {

        require __DIR__ . '/auth.php';

        // Route::get('/', function () {
        //     return 'This is the central domain';
        // });


        Route::middleware([
            'auth',
            'verified',
        ])
            ->group(function () {
                Route::get('dashboard', [Controllers\DashboardController::class, 'index'])->name('dashboard');

                // Companies routes
                Route::middleware('permission:manage-companies')->group(function () {
                    Route::get('companies', [Controllers\CompanyController::class, 'index'])->middleware('permission:manage-companies')->name('companies.index');
                    Route::post('companies', [Controllers\CompanyController::class, 'store'])->middleware('permission:create-companies')->name('companies.store');
                    Route::put('companies/{company}', [Controllers\CompanyController::class, 'update'])->middleware('permission:edit-companies')->name('companies.update');
                    Route::delete('companies/{company}', [Controllers\CompanyController::class, 'destroy'])->middleware('permission:delete-companies')->name('companies.destroy');
                    Route::put('companies/{company}/reset-password', [Controllers\CompanyController::class, 'resetPassword'])->middleware('permission:reset-password-companies')->name('companies.reset-password');
                    Route::put('companies/{company}/toggle-status', [Controllers\CompanyController::class, 'toggleStatus'])->middleware('permission:toggle-status-companies')->name('companies.toggle-status');
                    Route::get('companies/{company}/plans', [Controllers\CompanyController::class, 'getPlans'])->middleware('permission:manage-plans-companies')->name('companies.plans');
                    Route::get('companies/{company}/impersonate', [Controllers\CompanyController::class, 'impersonate'])->middleware('permission:manage-companies')->name('companies.impersonate');
                    Route::put('companies/{company}/upgrade-plan', [Controllers\CompanyController::class, 'upgradePlan'])->middleware('permission:upgrade-plan-companies')->name('companies.upgrade-plan');
                });

                // Plans management routes (admin only)
                Route::middleware('permission:manage-plans')->group(function () {
                    Route::get('plans/create', [Controllers\PlanController::class, 'create'])->middleware('permission:create-plans')->name('plans.create');
                    Route::post('plans', [Controllers\PlanController::class, 'store'])->middleware('permission:create-plans')->name('plans.store');
                    Route::get('plans/{plan}/edit', [Controllers\PlanController::class, 'edit'])->middleware('permission:edit-plans')->name('plans.edit');
                    Route::put('plans/{plan}', [Controllers\PlanController::class, 'update'])->middleware('permission:edit-plans')->name('plans.update');
                    Route::delete('plans/{plan}', [Controllers\PlanController::class, 'destroy'])->middleware('permission:delete-plans')->name('plans.destroy');
                    Route::post('plans/{plan}/toggle-status', [Controllers\PlanController::class, 'toggleStatus'])->name('plans.toggle-status');
                });

                // Plan Orders routes
                Route::middleware('permission:manage-plan-orders')->group(function () {
                    Route::get('plan-orders', [Controllers\PlanOrderController::class, 'index'])->middleware('permission:manage-plan-orders')->name('plan-orders.index');
                    Route::post('plan-orders/{planOrder}/approve', [Controllers\PlanOrderController::class, 'approve'])->middleware('permission:approve-plan-orders')->name('plan-orders.approve');
                    Route::post('plan-orders/{planOrder}/reject', [Controllers\PlanOrderController::class, 'reject'])->middleware('permission:reject-plan-orders')->name('plan-orders.reject');
                });

                // Plan Requests routes (placeholder)
                Route::get('plan-requests', function () {
                    return Inertia::render('plans/plan-request');
                })->name('plan-requests.index');

                // Plan request cancel route (accessible to all authenticated users)
                Route::post('plan-requests/{planRequest}/cancel', [Controllers\PlanRequestController::class, 'cancel'])->name('plan-requests.cancel');

                // Coupons routes
                Route::middleware('permission:manage-coupons')->group(function () {
                    Route::get('coupons', [Controllers\CouponController::class, 'index'])->middleware('permission:manage-coupons')->name('coupons.index');
                    Route::get('coupons/{coupon}', [Controllers\CouponController::class, 'show'])->middleware('permission:view-coupons')->name('coupons.show');
                    Route::post('coupons', [Controllers\CouponController::class, 'store'])->middleware('permission:create-coupons')->name('coupons.store');
                    Route::put('coupons/{coupon}', [Controllers\CouponController::class, 'update'])->middleware('permission:edit-coupons')->name('coupons.update');
                    Route::put('coupons/{coupon}/toggle-status', [Controllers\CouponController::class, 'toggleStatus'])->middleware('permission:toggle-status-coupons')->name('coupons.toggle-status');
                    Route::delete('coupons/{coupon}', [Controllers\CouponController::class, 'destroy'])->middleware('permission:delete-coupons')->name('coupons.destroy');
                });

                // Contact Us routes
                Route::middleware('permission:manage-contact-us')->group(function () {
                    Route::get('contact-us', [Controllers\ContactUsController::class, 'index'])->name('contact-us.index');
                    Route::delete('contact-us/{contact}', [Controllers\ContactUsController::class, 'destroy'])->name('contact-us.destroy');
                });

                // Newsletter routes
                Route::middleware('permission:manage-contact-us')->group(function () {
                    Route::get('newsletter', [Controllers\NewsletterController::class, 'index'])->name('newsletter.index');
                    Route::post('newsletter/send', [Controllers\NewsletterController::class, 'send'])->name('newsletter.send');
                    Route::delete('newsletter/{subscription}', [Controllers\NewsletterController::class, 'destroy'])->name('newsletter.destroy');
                });

                // Currencies routes
                Route::middleware('permission:manage-currencies')->group(function () {
                    Route::get('currencies', [Controllers\CurrencyController::class, 'index'])->name('currencies.index');
                    Route::post('currencies', [Controllers\CurrencyController::class, 'store'])->middleware('permission:create-currencies')->name('currencies.store');
                    Route::put('currencies/{currency}', [Controllers\CurrencyController::class, 'update'])->middleware('permission:edit-currencies')->name('currencies.update');
                    Route::delete('currencies/{currency}', [Controllers\CurrencyController::class, 'destroy'])->middleware('permission:delete-currencies')->name('currencies.destroy');
                    Route::get('api/currencies', [Controllers\CurrencyController::class, 'getAllCurrencies'])->name('api.currencies');
                });

                // Tax Rates routes
                Route::middleware('permission:manage-tax-rates')->group(function () {
                    Route::get('tax-rates', [Controllers\TaxRateController::class, 'index'])->name('tax-rates.index');
                    Route::post('tax-rates', [Controllers\TaxRateController::class, 'store'])->middleware('permission:create-tax-rates')->name('tax-rates.store');
                    Route::put('tax-rates/{taxRate}', [Controllers\TaxRateController::class, 'update'])->middleware('permission:edit-tax-rates')->name('tax-rates.update');
                    Route::delete('tax-rates/{taxRate}', [Controllers\TaxRateController::class, 'destroy'])->middleware('permission:delete-tax-rates')->name('tax-rates.destroy');
                });

                // Countries routes
                Route::middleware('permission:manage-countries')->group(function () {
                    Route::get('countries', [Controllers\CountryController::class, 'index'])->middleware('permission:manage-countries')->name('countries.index');
                    Route::post('countries', [Controllers\CountryController::class, 'store'])->middleware('permission:create-countries')->name('countries.store');
                    Route::put('countries/{country}', [Controllers\CountryController::class, 'update'])->middleware('permission:edit-countries')->name('countries.update');
                    Route::delete('countries/{country}', [Controllers\CountryController::class, 'destroy'])->middleware('permission:delete-countries')->name('countries.destroy');
                });

                // Referral routes
                Route::middleware('permission:manage-referral')->group(function () {
                    Route::get('referral', [Controllers\ReferralController::class, 'index'])->middleware('permission:manage-referral')->name('referral.index');
                    Route::get('referral/referred-users', [Controllers\ReferralController::class, 'getReferredUsers'])->middleware('permission:manage-users-referral')->name('referral.referred-users');
                    Route::post('referral/settings', [Controllers\ReferralController::class, 'updateSettings'])->middleware('permission:manage-setting-referral')->name('referral.settings.update');
                    Route::post('referral/payout-request', [Controllers\ReferralController::class, 'createPayoutRequest'])->middleware('permission:manage-payout-referral')->name('referral.payout-request.create');
                    Route::post('referral/payout-request/{payoutRequest}/approve', [Controllers\ReferralController::class, 'approvePayoutRequest'])->middleware('permission:approve-payout-referral')->name('referral.payout-request.approve');
                    Route::post('referral/payout-request/{payoutRequest}/reject', [Controllers\ReferralController::class, 'rejectPayoutRequest'])->middleware('permission:reject-payout-referral')->name('referral.payout-request.reject');
                });
            });
    });
}