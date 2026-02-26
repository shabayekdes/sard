<?php

use App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware([
    'universal',
])->group(function () {

    require __DIR__ . '/settings.php';

    Route::middleware(['auth', 'verified'])
        ->group(function () {

            Route::get('dashboard', [Controllers\DashboardController::class, 'index'])->name('dashboard');
            Route::get('dashboard/redirect', [Controllers\DashboardController::class, 'redirectToFirstAvailablePage'])->name('dashboard.redirect');

            Route::get('media-library', function () {
                $storageSettings = \App\Services\StorageConfigService::getStorageConfig();

                return Inertia::render('media-library', [
                    'storageSettings' => $storageSettings,
                ]);
            })->name('media-library');
            // Media Library API routes
            Route::get('api/media', [Controllers\MediaController::class, 'index'])->middleware('permission:manage-media')->name('api.media.index');
            Route::post('api/media/batch', [Controllers\MediaController::class, 'batchStore'])->middleware('permission:create-media')->name('api.media.batch');
            Route::get('api/media/{id}/download', [Controllers\MediaController::class, 'download'])->middleware('permission:download-media')->name('api.media.download');
            Route::delete('api/media/{id}', [Controllers\MediaController::class, 'destroy'])->middleware('permission:delete-media')->name('api.media.destroy');

            Route::get('/translations/{locale}', [Controllers\TranslationController::class, 'getTranslations'])->name('translations');
            Route::get('/refresh-language/{locale}', [Controllers\TranslationController::class, 'refreshLanguage'])->name('refresh-language');
            Route::get('/initial-locale', [Controllers\TranslationController::class, 'getInitialLocale'])->name('initial-locale');
            Route::post('/refresh-all-languages', [Controllers\TranslationController::class, 'refreshAllLanguages'])->name('refresh-all-languages');

            // Plans routes - accessible without plan check
            Route::get('plans', [Controllers\PlanController::class, 'index'])->name('plans.index');
            Route::post('plans/request', [Controllers\PlanController::class, 'requestPlan'])->name('plans.request');
            Route::post('plans/trial', [Controllers\PlanController::class, 'startTrial'])->name('plans.trial');
            Route::post('plans/subscribe', [Controllers\PlanController::class, 'subscribe'])->name('plans.subscribe');
            Route::post('plans/coupons/validate', [Controllers\CouponController::class, 'validate'])->name('coupons.validate');

        });
});