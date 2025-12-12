<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Models\Setting;
use App\Models\User;

class TranslationController extends BaseController
{
    public function getTranslations($locale)
    {
      

        $path = resource_path("lang/{$locale}.json");

        if (!File::exists($path)) {
            $path = resource_path("lang/en.json");
            $locale = 'en';
        }

        // Always determine direction based on locale
        $layoutDirection = in_array($locale, ['ar', 'he']) ? 'right' : 'left';

        // Always store language preference in cookie for persistence
        Cookie::queue('app_language', $locale, 60 * 24 * 30); // 30 days
        Cookie::queue('app_direction', $layoutDirection, 60 * 24 * 30);

        // Demo mode handling
        if (config('app.is_demo') !== true) {
            if (auth()->check()) {
                // Update authenticated user's language and direction settings
                auth()->user()->update(['lang' => $locale]);

                Setting::updateOrCreate(
                    [
                        'key' => 'layoutDirection',
                        'user_id' => auth()->id()
                    ],
                    [
                        'value' => $layoutDirection
                    ]
                );
            } else {
                // For unauthenticated users on auth pages, use superadmin's language
                $superAdmin = User::where('type', 'superadmin')->first();
                if ($superAdmin && request()->is('login', 'register', 'password/*', 'email/*')) {
                    $locale = $superAdmin->lang ?? 'en';

                    // Check if superadmin's language is enabled
                    $languagesFile = resource_path('lang/language.json');
                    if (File::exists($languagesFile)) {
                        $languages = json_decode(File::get($languagesFile), true);
                        $requestedLang = collect($languages)->firstWhere('code', $locale);

                        // // If language is disabled, fallback to English
                        // if ($requestedLang && isset($requestedLang['enabled']) && $requestedLang['enabled'] === false) {
                        //     $locale = 'en';
                        // }
                    }

                    $path = resource_path("lang/{$locale}.json");

                    if (!File::exists($path)) {
                        $path = resource_path("lang/en.json");
                        $locale = 'en';
                    }

                    // Re-determine direction based on superadmin's locale
                    $layoutDirection = in_array($locale, ['ar', 'he']) ? 'right' : 'left';
                }
            }
        }

        $translations = json_decode(File::get($path), true);

        // Add layout direction to the response
        $response = [
            'translations' => $translations,
            'layoutDirection' => $layoutDirection,
            'locale' => $locale
        ];

        return response()->json($response);
    }

    // Add a method to get the initial locale
    public function getInitialLocale()
    {
        $locale = 'en'; // Default fallback

        // First check cookie for all users for consistency
        $cookieLang = Cookie::get('app_language');
        if ($cookieLang) {
            $locale = $cookieLang;
        } else if (auth()->check()) {
            // For authenticated users, get from user preferences
            $locale = auth()->user()->lang ?? 'en';
        } else if (request()->is('login', 'register', 'password/*', 'email/*')) {
            // For auth pages, get from superadmin
            $superAdmin = User::where('type', 'superadmin')->first();
            $locale = $superAdmin->lang ?? 'en';
        }

        // Check if the determined language is enabled
        $languagesFile = resource_path('lang/language.json');
        if (File::exists($languagesFile)) {
            $languages = json_decode(File::get($languagesFile), true);
            $requestedLang = collect($languages)->firstWhere('code', $locale);

            // If language is disabled, fallback to English
            // if ($requestedLang && isset($requestedLang['enabled']) && $requestedLang['enabled'] === false) {
            //     $locale = 'en';
            // }
        }

        return $locale;
    }

    public function refreshAllLanguages()
    {
        try {
            // Clear all language-related cookies and sessions
            Cookie::queue(Cookie::forget('app_language'));
            Cookie::queue(Cookie::forget('app_direction'));
            Session::forget('locale');

            return response()->json(['success' => true, 'message' => 'Language settings refreshed']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to refresh language settings'], 500);
        }
    }
}
