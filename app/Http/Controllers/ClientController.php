<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientType;
use App\Models\CaseType;
use App\Models\CaseStatus;
use App\Models\Court;
use App\Models\ClientDocument;
use App\Models\DocumentType;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Client::class, 'client');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Client::withPermissionCheck()
            ->with(['clientType', 'creator'])
            ->withCount('cases');

        // Handle search
        if ($request->has('search') && ! empty($request->search)) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%')
                    ->orWhere('phone', 'like', '%'.$request->search.'%')
                    ->orWhere('client_id', 'like', '%'.$request->search.'%')
                    ->orWhere('company_name', 'like', '%'.$request->search.'%');
            });
        }

        // Handle client type filter
        if ($request->has('client_type_id') && ! empty($request->client_type_id) && $request->client_type_id !== 'all') {
            $query->where('client_type_id', $request->client_type_id);
        }

        // Handle status filter
        if ($request->has('status') && ! empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Handle sorting (default to latest)
        if ($request->has('sort_field') && !empty($request->sort_field)) {
            $query->orderBy($request->sort_field, $request->sort_direction ?? 'asc');
        } else {
            $query->latest('id');
        }

        $clients = $query->paginate($request->per_page ?? 10);

        // Transform clients to include client_type translations
        $clients->getCollection()->transform(function ($client) {
            if ($client->clientType) {
                $client->clientType->name_translations = $client->clientType->getTranslations('name');
                $client->clientType->description_translations = $client->clientType->getTranslations('description');
            }
            return $client;
        });

        $clientTypes = ClientType::withPermissionCheck()
            ->where('status', 'active')
            ->get(['id', 'name'])
            ->map(function (ClientType $clientType) {
                return [
                    'id' => $clientType->id,
                    'name' => $clientType->name,
                    'name_translations' => $clientType->getTranslations('name'),
                ];
            });

        $authUser = auth()->user();
        $planLimits = null;
        if ($authUser->type === 'company' && $authUser->plan) {
            $currentClients = Client::where('tenant_id', $authUser->tenant_id)->count();
            $maxClients = $authUser->plan->max_clients;
            $isUnlimited = $authUser->plan->isUnlimitedLimit($maxClients);
            $planLimits = [
                'current_clients' => $currentClients,
                'max_clients' => $maxClients,
                'can_create' => $isUnlimited ? true : $currentClients < $maxClients,
            ];
        } elseif ($authUser->type !== 'superadmin' && $authUser->tenant_id) {
            $companyUser = User::find($authUser->tenant_id);
            if ($companyUser && $companyUser->type === 'company' && $companyUser->plan) {
                $currentClients = Client::where('tenant_id', $companyUser->tenant_id)->count();
                $maxClients = $companyUser->plan->max_clients;
                $isUnlimited = $companyUser->plan->isUnlimitedLimit($maxClients);
                $planLimits = [
                    'current_clients' => $currentClients,
                    'max_clients' => $maxClients,
                    'can_create' => $isUnlimited ? true : $currentClients < $maxClients,
                ];
            }
        }

        return Inertia::render('clients/index', [
            'clients' => $clients,
            'clientTypes' => $clientTypes,
            'planLimits' => $planLimits,
            'filters' => $request->only(['search', 'client_type_id', 'status', 'per_page', 'sort_field', 'sort_direction'])
        ]);
    }

    public function create()
    {
        return Inertia::render('clients/create', $this->getClientFormProps());
    }

    public function edit(Client $client)
    {
        $client->load(['clientType', 'creator', 'documents']);

        if ($client->clientType) {
            $client->clientType->name_translations = $client->clientType->getTranslations('name');
            $client->clientType->description_translations = $client->clientType->getTranslations('description');
        }

        // Map documents for repeater: document_name, document_type_id, file (file_path)
        if ($client->relationLoaded('documents')) {
            $client->documents = $client->documents->map(function ($doc) {
                return [
                    'document_name' => $doc->document_name,
                    'document_type_id' => $doc->document_type_id ? (string) $doc->document_type_id : '',
                    'file' => $doc->file_path,
                ];
            })->values()->all();
        }

        return Inertia::render(
            'clients/edit',
            array_merge(
                ['client' => $client],
                $this->getClientFormProps()
            )
        );
    }

    private function getClientFormProps(): array
    {
        // Get client types for filter dropdown
        $clientTypes = ClientType::withPermissionCheck()
            ->where('status', 'active')
            ->get(['id', 'name'])
            ->map(function (ClientType $clientType) {
                return [
                    'id' => $clientType->id,
                    'name' => $clientType->name, // Translated value for current locale
                    'name_translations' => $clientType->getTranslations('name'), // Full translations
                ];
            });

        // Get countries for nationality dropdown
        $countries = Country::where('is_active', true)
            ->orderByRaw("JSON_EXTRACT(name, '$.en')")
            ->get(['id', 'name', 'nationality_name', 'country_code'])
            ->map(function ($country) {
                return [
                    'value' => $country->id,
                    'name' => $country->name, // Spatie automatically returns translated value
                    'label' => $country->nationality_name, // Spatie automatically returns translated value
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

        // Get plan limits for clients (same pattern as UserController)
        $authUser = auth()->user();
        $planLimits = null;
        if ($authUser->type === 'company' && $authUser->plan) {
            $currentClients = Client::where('created_by', $authUser->id)->count();
            $maxClients = $authUser->plan->max_clients;
            $isUnlimited = $authUser->plan->isUnlimitedLimit($maxClients);
            $planLimits = [
                'current_clients' => $currentClients,
                'max_clients' => $maxClients,
                'can_create' => $isUnlimited ? true : $currentClients < $maxClients,
            ];
        } elseif ($authUser->type !== 'superadmin' && $authUser->created_by) {
            $companyUser = User::find($authUser->created_by);
            if ($companyUser && $companyUser->type === 'company' && $companyUser->plan) {
                $currentClients = Client::where('created_by', $companyUser->id)->count();
                $maxClients = $companyUser->plan->max_clients;
                $isUnlimited = $companyUser->plan->isUnlimitedLimit($maxClients);
                $planLimits = [
                    'current_clients' => $currentClients,
                    'max_clients' => $maxClients,
                    'can_create' => $isUnlimited ? true : $currentClients < $maxClients,
                ];
            }
        }

        $documentTypes = DocumentType::withPermissionCheck()
            ->where('status', 'active')
            ->get(['id', 'name', 'color']);

        return [
            'clientTypes' => $clientTypes,
            'countries' => $countries,
            'phoneCountries' => $phoneCountries,
            'defaultCountry' => getSetting('defaultCountry', ''),
            'defaultTaxRate' => getSetting('defaultTaxRate', ''),
            'planLimits' => $planLimits,
            'documentTypes' => $documentTypes,
        ];
    }

    public function store(Request $request)
    {
        // Check client limit (same pattern as UserController)
        $authUser = auth()->user();
        if ($authUser->type === 'company' && $authUser->plan) {
            $currentClients = Client::where('created_by', $authUser->id)->count();
            $maxClients = $authUser->plan->max_clients;
            $isUnlimited = $authUser->plan->isUnlimitedLimit($maxClients);

            if (!$isUnlimited && $currentClients >= $maxClients) {
                return redirect()->back()->with('error', __('Client limit exceeded. Your plan allows maximum :max clients. Please upgrade your plan.', ['max' => $maxClients]));
            }
        } elseif ($authUser->type !== 'superadmin' && $authUser->created_by) {
            $companyUser = User::find($authUser->created_by);
            if ($companyUser && $companyUser->type === 'company' && $companyUser->plan) {
                $currentClients = Client::where('created_by', $companyUser->id)->count();
                $maxClients = $companyUser->plan->max_clients;
                $isUnlimited = $companyUser->plan->isUnlimitedLimit($maxClients);

                if (!$isUnlimited && $currentClients >= $maxClients) {
                    return redirect()->back()->with('error', __('Client limit exceeded. Your company plan allows maximum :max clients. Please contact your administrator.', ['max' => $maxClients]));
                }
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email',
            'password' => 'required|string|min:6',
            'country_id' => 'required|exists:countries,id',
            'phone' => 'required|string|unique:clients,phone',
            'business_type' => 'required|string|in:b2c,b2b',
            'nationality_id' => 'nullable|exists:countries,id',
            'gender' => 'nullable|string|in:male,female',
            'id_number' => 'nullable|string|max:100',
            'unified_number' => 'nullable|string|max:100',
            'cr_number' => 'nullable|string|max:100',
            'cr_issuance_date' => 'nullable|date',
            'tax_id' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'client_type_id' => 'nullable|exists:client_types,id',
            'status' => 'nullable|in:active,inactive',
            'company_name' => 'nullable|string|max:255',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'date_of_birth' => 'nullable|date',
            'notes' => 'nullable|string',
            'documents' => 'nullable|array',
            'documents.*.document_name' => 'required_with:documents|string|max:255',
            'documents.*.document_type_id' => 'required_with:documents|exists:document_types,id',
            'documents.*.file' => 'required_with:documents|string',
            'documents.*.description' => 'nullable|string',
            'documents.*.status' => 'nullable|in:active,archived',
        ]);

        $phoneCountry = Country::where('id', $validated['country_id'])
            ->whereNotNull('country_code')
            ->first();

        if (! $phoneCountry) {
            return redirect()->back()->withErrors(['country_id' => __('Invalid phone country')]);
        }

        $phoneValidator = Validator::make(
            ['phone' => $validated['phone']],
            ['phone' => 'phone:'.$phoneCountry->country_code],
            ['phone.phone' => __('Please enter a valid phone number for the selected country.')],
        );

        if ($phoneValidator->fails()) {
            return redirect()->back()->withErrors($phoneValidator)->withInput();
        }

        $documents = $validated['documents'] ?? [];
        unset($validated['documents']);

        $validated['created_by'] = createdBy();
        $validated['status'] = $validated['status'] ?? 'active';

        // Check if client type belongs to the current user's company
        if (! empty($validated['client_type_id'])) {
            $clientType = ClientType::where('id', $validated['client_type_id'])
                ->where('created_by', createdBy())
                ->first();

            if (! $clientType) {
                return redirect()->back()->with('error', __('Invalid client type selected.'));
            }
        }

        // Check if client with same email already exists for this company
        if (! empty($validated['email'])) {
            $exists = Client::where('email', $validated['email'])
                ->where('created_by', createdBy())
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', __(':model with this email already exists.', ['model' => __('Client')]));
            }
        }

        $client = Client::create($validated);

        if (! empty($documents)) {
            foreach ($documents as $document) {
                $filePath = $this->convertToRelativePath($document['file'] ?? '');
                ClientDocument::create([
                    'client_id' => $client->id,
                    'document_name' => $document['document_name'] ?? '',
                    'document_type_id' => $document['document_type_id'] ?? null,
                    'description' => $document['description'] ?? null,
                    'status' => $document['status'] ?? 'active',
                    'file_path' => $filePath,
                    'created_by' => createdBy(),
                ]);
            }
        }

        // Create user account for client if email and password provided
        if (! empty($validated['email']) && ! empty($validated['password'])) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                'type' => 'client',
                'created_by' => createdBy(),
            ]);

            // Assign client role
            $clientRole = \Spatie\Permission\Models\Role::where('name', 'client')->first();
            if ($clientRole) {
                $user->assignRole($clientRole);
            }
        }

        // Trigger notifications
        event(new \App\Events\NewClientCreated($client, $request->all()));

        // Check for errors and combine them
        $emailError = session()->pull('email_error');
        $slackError = session()->pull('slack_error');
        $twilioError = session()->pull('twilio_error');

        $errors = [];
        if ($emailError) {
            $errors[] = __('Email send failed: ').$emailError;
        }
        if ($slackError) {
            $errors[] = __('Slack send failed: ').$slackError;
        }
        if ($twilioError) {
            $errors[] = __('SMS send failed: ').$twilioError;
        }

        if (! empty($errors)) {
            $message = __('Client created successfully, but ').implode(', ', $errors);

            return redirect()->back()->with('warning', $message);
        }

        return redirect()->route('clients.index')->with('success', __(':model created successfully.', ['model' => __('Client')]));
    }

    private function convertToRelativePath(string $url): string
    {
        if (! $url) {
            return $url;
        }

        if (! str_starts_with($url, 'http')) {
            return $url;
        }

        $storageIndex = strpos($url, '/storage/');
        if ($storageIndex !== false) {
            return substr($url, $storageIndex);
        }

        return $url;
    }

    public function update(Request $request, Client $client)
    {
        try {
            $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:clients,email,'.$client->id,
                    'country_id' => 'required|exists:countries,id',
                    'phone' => 'required|string|unique:clients,phone,'.$client->id,
                    'business_type' => 'required|string|in:b2c,b2b',
                    'nationality_id' => 'nullable|exists:countries,id',
                    'gender' => 'nullable|string|in:male,female',
                    'id_number' => 'nullable|string|max:100',
                    'unified_number' => 'nullable|string|max:100',
                    'cr_number' => 'nullable|string|max:100',
                    'cr_issuance_date' => 'nullable|date',
                    'tax_id' => 'nullable|string|max:100',
                    'address' => 'nullable|string',
                    'client_type_id' => 'nullable|exists:client_types,id',
                    'status' => 'nullable|in:active,inactive',
                    'company_name' => 'nullable|string|max:255',
                    'tax_rate' => 'nullable|numeric|min:0|max:100',
                    'date_of_birth' => 'nullable|date',
                    'notes' => 'nullable|string',
                    'documents' => 'nullable|array',
                    'documents.*.document_name' => 'required_with:documents|string|max:255',
                    'documents.*.document_type_id' => 'required_with:documents|exists:document_types,id',
                    'documents.*.file' => 'required_with:documents|string',
                    'documents.*.description' => 'nullable|string',
                    'documents.*.status' => 'nullable|in:active,archived',
                ], [
                    'name.required' => __('Client name is required.'),
                    'email.required' => __('Email is required.'),
                    'email.email' => __('Please enter a valid email address.'),
                    'country_id.required' => __('Country is required.'),
                    'phone.required' => __('Phone number is required.'),
                    'business_type.required' => __('Business type is required.'),
                ]);

                $phoneCountry = Country::where('id', $validated['country_id'])
                    ->whereNotNull('country_code')
                    ->first();

                if (! $phoneCountry) {
                    return redirect()->back()->withErrors(['country_id' => __('Invalid phone country')]);
                }

                $phoneValidator = Validator::make(
                    ['phone' => $validated['phone']],
                    ['phone' => 'phone:'.$phoneCountry->country_code],
                    ['phone.phone' => __('Please enter a valid phone number for the selected country.')],
                );

                if ($phoneValidator->fails()) {
                    return redirect()->back()->withErrors($phoneValidator)->withInput();
                }

                // Check if client type belongs to the current user's company
                if (! empty($validated['client_type_id'])) {
                    $clientType = ClientType::withPermissionCheck()
                        ->where('id', $validated['client_type_id'])
                        ->first();

                    if (! $clientType) {
                        return redirect()->back()->with('error', __('Invalid client type selected.'));
                    }
                }

                // Check if client with same email already exists for this company (excluding current)
                if (! empty($validated['email'])) {
                    $exists = Client::where('email', $validated['email'])
                        ->where('created_by', createdBy())
                        ->where('id', '!=', $client->id)
                        ->exists();

                    if ($exists) {
                        return redirect()->back()->with('error', __(':model with this email already exists.', ['model' => __('Client')]));
                    }
                }

                $documents = $validated['documents'] ?? [];
                unset($validated['documents']);

                $client->update($validated);

                // Replace client documents with repeater payload
                ClientDocument::where('client_id', $client->id)->delete();
                if (! empty($documents)) {
                    foreach ($documents as $document) {
                        $filePath = $this->convertToRelativePath($document['file'] ?? '');
                        ClientDocument::create([
                            'client_id' => $client->id,
                            'document_name' => $document['document_name'] ?? '',
                            'document_type_id' => $document['document_type_id'] ?? null,
                            'description' => $document['description'] ?? null,
                            'status' => $document['status'] ?? 'active',
                            'file_path' => $filePath,
                            'created_by' => createdBy(),
                        ]);
                    }
                }

                return redirect()->back()->with('success', __(':model updated successfully', ['model' => __('Client')]));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage() ?: __('Failed to update :model', ['model' => __('Client')]));
        }
    }

    public function show(Request $request, Client $client)
    {
        $client->load(['clientType', 'creator', 'billingInfo', 'nationality']);

        // Add translations for client_type if it exists
        if ($client->clientType) {
            $client->clientType->name_translations = $client->clientType->getTranslations('name');
            $client->clientType->description_translations = $client->clientType->getTranslations('description');
        }

        // Load currency name if billing info exists
        if ($client->billingInfo && $client->billingInfo->currency) {
            $currency = \App\Models\Currency::where('code', $client->billingInfo->currency)->first();
            $client->billingInfo->currency_name = $currency?->name;
            $client->billingInfo->currency_code = $currency?->code;
            $client->billingInfo->currency_symbol = $currency?->symbol;
        }

        // Documents with pagination and filters
        $documentsQuery = ClientDocument::withPermissionCheck()
            ->with(['documentType'])
            ->where('client_id', $client->id);

        if ($documentSearch = $request->get('document_search')) {
            $documentsQuery->where(function ($q) use ($documentSearch) {
                $q->where('document_name', 'like', "%{$documentSearch}%")
                    ->orWhere('description', 'like', "%{$documentSearch}%");
            });
        }
        if ($request->has('document_type_id') && $request->document_type_id !== '' && $request->document_type_id !== 'all') {
            $documentsQuery->where('document_type_id', $request->document_type_id);
        }
        if ($request->has('document_status') && $request->document_status !== '' && $request->document_status !== 'all') {
            $documentsQuery->where('status', $request->document_status);
        }
        $documentsQuery->orderBy('created_at', 'desc');
        $documents = $documentsQuery->paginate($request->get('document_per_page', 10), ['*'], 'document_page');

        $documentTypes = DocumentType::withPermissionCheck()
            ->where('status', 'active')
            ->get(['id', 'name', 'color'])
            ->map(function (DocumentType $dt) {
                return [
                    'id' => $dt->id,
                    'name' => $dt->name,
                    'name_translations' => $dt->getTranslations('name'),
                    'color' => $dt->color,
                ];
            });

        // Handle cases with pagination and search
        $casesQuery = \App\Models\CaseModel::withPermissionCheck()
            ->with(['caseType', 'caseStatus', 'client'])
            ->where('client_id', $client->id);

        // Apply search filter
        if ($caseSearch = $request->get('search')) {
            $casesQuery->where(function ($q) use ($caseSearch) {
                $q->where('title', 'like', "%{$caseSearch}%")
                    ->orWhere('case_id', 'like', "%{$caseSearch}%")
                    ->orWhere('description', 'like', "%{$caseSearch}%")
                    ->orWhereHas('client', function ($clientQuery) use ($caseSearch) {
                        $clientQuery->where('name', 'like', "%{$caseSearch}%");
                    });
            });
        }

        if ($request->has('case_type_id') && ! empty($request->case_type_id) && $request->case_type_id !== 'all') {
            $casesQuery->where('case_type_id', $request->case_type_id);
        }

        if ($request->has('case_status_id') && ! empty($request->case_status_id) && $request->case_status_id !== 'all') {
            $casesQuery->where('case_status_id', $request->case_status_id);
        }

        if ($request->has('priority') && ! empty($request->priority) && $request->priority !== 'all') {
            $casesQuery->where('priority', $request->priority);
        }

        if ($request->has('status') && ! empty($request->status) && $request->status !== 'all') {
            $casesQuery->where('status', $request->status);
        }

        if ($request->has('court_id') && ! empty($request->court_id) && $request->court_id !== 'all') {
            $casesQuery->where('court_id', $request->court_id);
        }

        // Apply sorting
        if ($request->has('sort_field') && ! empty($request->sort_field)) {
            $casesQuery->orderBy($request->sort_field, $request->sort_direction ?? 'asc');
        } else {
            $casesQuery->latest();
        }

        $cases = $casesQuery->paginate($request->per_page ?? 10);

        $caseTypes = CaseType::where('created_by', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name'])
            ->map(function (CaseType $caseType) {
                return [
                    'id' => $caseType->id,
                    'name' => $caseType->name,
                    'name_translations' => $caseType->getTranslations('name'),
                ];
            });

        $caseStatuses = CaseStatus::where('created_by', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        $courts = Court::where('created_by', createdBy())
            ->where('status', 'active')
            ->get(['id', 'name']);

        // Load invoices for this client
        $invoicesQuery = \App\Models\Invoice::withPermissionCheck()
            ->with(['client', 'case', 'currency', 'payments'])
            ->where('client_id', $client->id);

        // Apply search filter for invoices
        if ($invoiceSearch = $request->get('invoice_search')) {
            $invoicesQuery->where(function ($q) use ($invoiceSearch) {
                $q->where('invoice_number', 'like', "%{$invoiceSearch}%")
                    ->orWhere('notes', 'like', "%{$invoiceSearch}%");
            });
        }

        // Apply sorting for invoices
        if ($request->has('invoice_sort_field') && ! empty($request->invoice_sort_field)) {
            $invoicesQuery->orderBy($request->invoice_sort_field, $request->invoice_sort_direction ?? 'asc');
        } else {
            $invoicesQuery->latest('invoice_date');
        }

        $invoices = $invoicesQuery->paginate($request->invoice_per_page ?? 10, ['*'], 'invoice_page');
        
        // Calculate and append remaining_amount to each invoice
        $invoices->getCollection()->transform(function ($invoice) {
            $totalPaid = $invoice->payments->sum('amount');
            $invoice->remaining_amount = max(0, $invoice->total_amount - $totalPaid);
            return $invoice;
        });

        // Load payments for this client (through invoices)
        $paymentsQuery = \App\Models\Payment::withPermissionCheck()
            ->with(['invoice.client', 'creator'])
            ->whereHas('invoice', function ($q) use ($client) {
                $q->where('client_id', $client->id);
            });

        // Apply search filter for payments
        if ($paymentSearch = $request->get('payment_search')) {
            $paymentsQuery->where(function ($q) use ($paymentSearch) {
                $q->where('transaction_id', 'like', "%{$paymentSearch}%")
                    ->orWhere('notes', 'like', "%{$paymentSearch}%")
                    ->orWhereHas('invoice', function ($invoiceQuery) use ($paymentSearch) {
                        $invoiceQuery->where('invoice_number', 'like', "%{$paymentSearch}%");
                    });
            });
        }

        // Apply sorting for payments
        if ($request->has('payment_sort_field') && ! empty($request->payment_sort_field)) {
            $paymentsQuery->orderBy($request->payment_sort_field, $request->payment_sort_direction ?? 'asc');
        } else {
            $paymentsQuery->latest('payment_date');
        }

        $payments = $paymentsQuery->paginate($request->payment_per_page ?? 10, ['*'], 'payment_page');
        
        // Transform payments to convert attachment array to comma-separated string for frontend
        $payments->getCollection()->transform(function ($payment) {
            $paymentData = $payment->toArray();
            
            // Convert attachment array to comma-separated string for frontend
            if (isset($paymentData['attachment']) && is_array($paymentData['attachment'])) {
                $paymentData['attachment'] = implode(',', array_filter($paymentData['attachment']));
            }
            
            // Ensure invoice_id is included and invoice relationship is preserved
            if (!isset($paymentData['invoice_id']) && $payment->invoice_id) {
                $paymentData['invoice_id'] = $payment->invoice_id;
            }
            
            // Ensure invoice relationship is properly included
            if ($payment->invoice) {
                $paymentData['invoice'] = [
                    'id' => $payment->invoice->id,
                    'invoice_number' => $payment->invoice->invoice_number,
                    'client' => $payment->invoice->client ? [
                        'id' => $payment->invoice->client->id,
                        'name' => $payment->invoice->client->name,
                    ] : null,
                ];
            }
            
            return $paymentData;
        });

        // Get all invoices for payment modal (needed for select field)
        $allInvoices = \App\Models\Invoice::withPermissionCheck()
            ->with('client')
            ->select('id', 'invoice_number', 'client_id')
            ->get();

        // Currencies for billing info edit modal
        $currencies = \App\Models\Currency::where('status', true)
            ->orderBy('code')
            ->get(['id', 'name', 'code', 'symbol'])
            ->map(fn ($c) => ['value' => $c->code, 'label' => $c->name.' ('.$c->code.')']);

        return Inertia::render('clients/show', [
            'client' => $client,
            'currencies' => $currencies,
            'documents' => $documents,
            'documentTypes' => $documentTypes,
            'cases' => $cases,
            'caseTypes' => $caseTypes,
            'caseStatuses' => $caseStatuses,
            'courts' => $courts,
            'invoices' => $invoices,
            'payments' => $payments,
            'allInvoices' => $allInvoices,
            'filters' => $request->all([
                'search',
                'case_type_id',
                'case_status_id',
                'priority',
                'status',
                'court_id',
                'sort_field',
                'sort_direction',
                'per_page',
                'invoice_search',
                'invoice_sort_field',
                'invoice_sort_direction',
                'invoice_per_page',
                'payment_search',
                'payment_sort_field',
                'payment_sort_direction',
                'payment_per_page',
                'document_search',
                'document_type_id',
                'document_status',
                'document_page',
                'document_per_page',
            ]),
        ]);
    }

    public function destroy(Client $client)
    {
        try {
            $client->delete();
            return redirect()->back()->with('success', __(':model deleted successfully', ['model' => __('Client')]));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage() ?: __('Failed to delete :model', ['model' => __('Client')]));
        }
    }

    public function toggleStatus(Client $client)
    {
        $this->authorize('update', $client);
        try {
            $client->status = $client->status === 'active' ? 'inactive' : 'active';
            $client->save();
            return redirect()->back()->with('success', __(':model status updated successfully', ['model' => __('Client')]));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage() ?: __('Failed to update :model status', ['model' => __('Client')]));
        }
    }

    public function resetPassword(Request $request, Client $client)
    {
        $this->authorize('update', $client);

        if (! auth()->user()->can('reset-client-password')) {
            return redirect()->back()->with('error', __('You do not have permission to reset :model passwords.', ['model' => __('Client')]));
        }

        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        if (empty($client->email)) {
            return redirect()->back()->with('error', __(':model does not have an email address.', ['model' => __('Client')]));
        }

        // Find the user account associated with this client
        $user = User::where('email', $client->email)
            ->where('type', 'client')
            ->where('created_by', createdBy())
            ->first();

        if (! $user) {
            return redirect()->back()->with('error', __('No user account found for this :model.', ['model' => __('Client')]));
        }

        try {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $user->save();

            return redirect()->back()->with('success', __(':model password reset successfully.', ['model' => __('Client')]));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage() ?: __('Failed to reset :model password', ['model' => __('Client')]));
        }
    }
}
