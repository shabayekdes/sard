<?php

namespace App\Http\Controllers;

use App\Models\CaseModel;
use App\Models\CaseStatus;
use App\Models\CaseCategory;
use App\Models\CaseType;
use App\Models\Client;
use App\Models\ClientType;
use App\Models\Country;
use App\Models\Court;
use App\Models\HearingType;
use App\Models\Judge;
use App\Models\Setting;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Http\Request;

class QuickActionController extends Controller
{
    public function caseFormData(Request $request)
    {
        $caseTypes = CaseType::where('created_by', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        $caseCategories = CaseCategory::where('created_by', createdBy())
            ->where('status', 'active')
            ->whereNull('parent_id')
            ->get(['id', 'name'])
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'name_translations' => $category->getTranslations('name'),
                ];
            });

        $caseStatuses = CaseStatus::where('created_by', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        $clients = Client::where('created_by', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        $courts = Court::where('created_by', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        $locale = app()->getLocale();
        $countries = Country::where('is_active', true)
            ->orderByRaw("JSON_EXTRACT(nationality_name, '$.{$locale}')")
            ->orderByRaw("JSON_EXTRACT(nationality_name, '$.en')")
            ->get(['id', 'name', 'nationality_name'])
            ->map(function ($country) {
                $nationalityLabel = $country->nationality_name;
                $countryName = $country->name;
                $label = !empty($nationalityLabel) ? $nationalityLabel : $countryName;

                return [
                    'value' => $country->id,
                    'label' => $label,
                ];
            })
            ->filter(function ($country) {
                return !empty($country['label']);
            })
            ->values();

        $googleCalendarEnabled = Setting::where('user_id', createdBy())
            ->where('key', 'googleCalendarEnabled')
            ->value('value') == '1';

        $currentUser = auth()->user();

        return response()->json([
            'caseTypes' => $caseTypes,
            'caseCategories' => $caseCategories,
            'caseStatuses' => $caseStatuses,
            'clients' => $clients,
            'courts' => $courts,
            'countries' => $countries,
            'googleCalendarEnabled' => $googleCalendarEnabled,
            'currentUser' => $currentUser ? [
                'id' => $currentUser->id,
                'name' => $currentUser->name,
            ] : null,
        ]);
    }

    public function clientFormData(Request $request)
    {
        $clientTypes = ClientType::where('created_by', createdBy())
            ->where('status', 'active')
            ->get()
            ->map(function ($type) {
                /** @var \App\Models\ClientType $type */
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'name_translations' => $type->getTranslations('name'),
                ];
            });

        $countryModels = Country::where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'name', 'nationality_name', 'country_code']);

        $defaultCountryCode = getSetting('defaultCountry', '');
        $defaultCountryId = $defaultCountryCode
            ? $countryModels->firstWhere('country_code', $defaultCountryCode)?->id
            : null;

        $countries = $countryModels->map(function ($country) {
            return [
                'value' => $country->id,
                'label' => $country->nationality_name,
                'code' => $country->country_code,
            ];
        });

        $phoneCountries = Country::where('is_active', true)
            ->whereNotNull('country_code')
            ->get(['id', 'name', 'country_code'])
            ->map(function ($country) {
                return [
                    'value' => $country->id,
                    'label' => $country->name,
                    'code' => $country->country_code,
                ];
            })
            ->values();

        return response()->json([
            'clientTypes' => $clientTypes,
            'countries' => $countries,
            'defaultTaxRate' => getSetting('defaultTaxRate', '0'),
            'defaultCountryId' => $defaultCountryId,
            'defaultCountry' => $defaultCountryCode,
            'phoneCountries' => $phoneCountries,
        ]);
    }

    public function taskFormData(Request $request)
    {
        $taskTypes = TaskType::where('created_by', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        $taskStatuses = TaskStatus::where('created_by', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        $users = User::where('created_by', createdBy())
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'client');
            })
            ->orWhere('id', createdBy())
            ->get(['id', 'name']);

        $cases = CaseModel::where('created_by', createdBy())
            ->get(['id', 'case_id', 'title']);

        $googleCalendarEnabled = Setting::where('user_id', createdBy())
            ->where('key', 'googleCalendarEnabled')
            ->value('value') == '1';

        return response()->json([
            'taskTypes' => $taskTypes,
            'taskStatuses' => $taskStatuses,
            'users' => $users,
            'cases' => $cases,
            'googleCalendarEnabled' => $googleCalendarEnabled,
        ]);
    }

    public function hearingFormData(Request $request)
    {
        $cases = CaseModel::withPermissionCheck()
            ->get(['id', 'case_id', 'title', 'file_number']);

        $courts = Court::withPermissionCheck()
            ->with(['courtType', 'circleType'])
            ->where('status', 'active')
            ->get(['id', 'name', 'court_type_id', 'circle_type_id']);

        $judges = Judge::withPermissionCheck()
            ->where('status', 'active')
            ->get(['id', 'name']);

        $hearingTypes = HearingType::withPermissionCheck()
            ->where('status', 'active')
            ->get(['id', 'name']);

        $googleCalendarEnabled = Setting::where('user_id', createdBy())
            ->where('key', 'googleCalendarEnabled')
            ->value('value') == '1';

        return response()->json([
            'cases' => $cases,
            'courts' => $courts,
            'judges' => $judges,
            'hearingTypes' => $hearingTypes,
            'googleCalendarEnabled' => $googleCalendarEnabled,
        ]);
    }
}
