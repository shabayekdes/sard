<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CurrencySettingController extends Controller
{
    /**
     * Update the currency settings.
     */
    public function update(Request $request)
    {
        try {
            $defaultCurrencyRule = Rule::exists('currencies', 'code')
                ->where('status', true);

            $validated = $request->validate([
                'decimalFormat' => 'required|string|in:0,1,2,3,4',
                'defaultCurrency' => ['required', 'string', $defaultCurrencyRule],
                'decimalSeparator' => ['required', 'string', Rule::in(['.', ','])],
                'thousandsSeparator' => 'required|string',
                'floatNumber' => 'required|boolean',
                'currencySymbolSpace' => 'required|boolean',
                'currencySymbolPosition' => 'required|string|in:before,after',
            ]);

            // Update settings using helper function
            foreach ($validated as $key => $value) {
                updateSetting($key, is_bool($value) ? ($value ? '1' : '0') : $value);
            }

            // Clear settings cache so next request gets fresh currency data
            \Cache::forget('settings_' . (function_exists('createdBy') ? createdBy() : 'global'));

            return redirect()->back()->with('success', __('Currency settings updated successfully.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to update currency settings: :error', ['error' => $e->getMessage()]));
        }
    }
}
