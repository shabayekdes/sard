<?php

namespace App\Http\Controllers;

use App\Models\CaseModel;
use App\Models\CaseStatus;
use App\Models\CaseType;
use App\Models\Client;
use App\Models\ClientType;
use App\Models\Country;
use App\Models\Court;
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

        $caseStatuses = CaseStatus::where('created_by', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        $clients = Client::where('created_by', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        $courts = Court::where('created_by', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        return response()->json([
            'caseTypes' => $caseTypes,
            'caseStatuses' => $caseStatuses,
            'clients' => $clients,
            'courts' => $courts,
        ]);
    }

    public function clientFormData(Request $request)
    {
        $clientTypes = ClientType::where('created_by', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        $countries = Country::where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'name']);

        return response()->json([
            'clientTypes' => $clientTypes,
            'countries' => $countries,
            'defaultTaxRate' => getSetting('defaultTaxRate', '0'),
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
}
