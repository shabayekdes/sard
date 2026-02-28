<?php

namespace App\Http\Controllers;

use App\Facades\Settings;
use App\Models\Client;
use App\Models\ClientBillingInfo;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ClientBillingInfoController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientBillingInfo::withPermissionCheck()
            ->with(['client', 'creator']);

        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('billing_contact_name', 'like', '%' . $request->search . '%')
                    ->orWhere('billing_contact_email', 'like', '%' . $request->search . '%')
                    ->orWhereHas('client', function ($clientQuery) use ($request) {
                        $clientQuery->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        // Handle client filter
        if ($request->has('client_id') && !empty($request->client_id) && $request->client_id !== 'all') {
            $query->where('client_id', $request->client_id);
        }

        // Handle payment terms filter
        if ($request->has('payment_terms') && !empty($request->payment_terms) && $request->payment_terms !== 'all') {
            $query->where('payment_terms', $request->payment_terms);
        }

        // Handle status filter
        if ($request->has('status') && !empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Handle sorting
        if ($request->has('sort_field') && !empty($request->sort_field)) {
            $query->orderBy($request->sort_field, $request->sort_direction ?? 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $billingInfo = $query->paginate($request->per_page ?? 10);

        // Get clients for filter dropdown
        $clients = Client::where('tenant_id', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        // Get currencies for form dropdown
        $currencies = Currency::where('status', true)
            ->orderByRaw("JSON_EXTRACT(name, '$.ar') ASC")
            ->get(['id', 'name', 'code', 'symbol']);

        return Inertia::render('clients/billing/index', [
            'billingInfo' => $billingInfo,
            'clients' => $clients,
            'currencies' => $currencies,
            'filters' => $request->all(['search', 'client_id', 'payment_terms', 'status', 'sort_field', 'sort_direction', 'per_page']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id|unique:client_billing_infos,client_id',
            'billing_address' => 'nullable|string',
            'billing_contact_name' => 'nullable|string|max:255',
            'billing_contact_email' => 'nullable|email|max:255',
            'billing_contact_phone' => 'nullable|string|max:20',
            'payment_terms' => 'required|in:net_15,net_30,net_45,net_60,due_on_receipt,custom',
            'custom_payment_terms' => 'nullable|string|max:255',
            'currency' => [
                'nullable',
                Rule::exists('currencies', 'code')
                    ->where('status', true),
            ],
            'billing_notes' => 'nullable|string',
            'status' => 'nullable|in:active,suspended,closed',
        ]);

        $validated['tenant_id'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';
        $defaultCurrency = Settings::string('DEFAULT_CURRENCY');
        $validated['currency'] = $validated['currency'] ?? $defaultCurrency ?? 'USD';

        // Check if client belongs to the current user's company
        $client = Client::where('id', $validated['client_id'])
            ->where('tenant_id', createdBy())
            ->first();

        if (!$client) {
            return redirect()->back()->with('error', __('Invalid client selected.'));
        }

        ClientBillingInfo::create($validated);

        return redirect()->back()->with('success', __(':model created successfully.', ['model' => __('Billing Information')]));
    }

    public function update(Request $request, $billingId)
    {
        $billing = ClientBillingInfo::whereHas('client', function ($q) {
                $q->where('tenant_id', createdBy());
            })
            ->where('id', $billingId)
            ->first();

        if ($billing) {
            try {
                $validated = $request->validate([
                    'client_id' => 'required|exists:clients,id|unique:client_billing_infos,client_id,' . $billingId,
                    'billing_address' => 'nullable|string',
                    'billing_contact_name' => 'nullable|string|max:255',
                    'billing_contact_email' => 'nullable|email|max:255',
                    'billing_contact_phone' => 'nullable|string|max:20',
                    'payment_terms' => 'required|in:net_15,net_30,net_45,net_60,due_on_receipt,custom',
                    'custom_payment_terms' => 'nullable|string|max:255',
                    'currency' => [
                        'nullable',
                        Rule::exists('currencies', 'code')
                            ->where('status', true),
                    ],
                    'billing_notes' => 'nullable|string',
                    'status' => 'nullable|in:active,suspended,closed',
                ]);

                // Check if client belongs to the current user's company
                $client = Client::where('id', $validated['client_id'])
                    ->where('tenant_id', createdBy())
                    ->first();

                if (!$client) {
                    return redirect()->back()->with('error', __('Invalid client selected.'));
                }

                $billing->update($validated);

                return redirect()->back()->with('success', __(':model updated successfully', ['model' => __('Billing Information')]));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage() ?: __('Failed to update :model', ['model' => __('Billing Information')]));
            }
        } else {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Billing Information')]));
        }
    }

    public function destroy($billingId)
    {
        $billing = ClientBillingInfo::whereHas('client', function ($q) {
                $q->where('tenant_id', createdBy());
            })
            ->where('id', $billingId)
            ->first();

        if ($billing) {
            try {
                $billing->delete();
                return redirect()->back()->with('success', __(':model deleted successfully', ['model' => __('Billing Information')]));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage() ?: __('Failed to delete :model', ['model' => __('Billing Information')]));
            }
        } else {
            return redirect()->back()->with('error', __(':model not found.', ['model' => __('Billing Information')]));
        }
    }
}