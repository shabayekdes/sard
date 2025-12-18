<?php

namespace Database\Seeders;

use App\Models\ClientBillingCurrency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientBillingCurrencySeeder extends Seeder
{
    public function run(): void
    {
        $companyUsers = \App\Models\User::where('type', 'company')->get();

        foreach ($companyUsers as $companyUser) {
            // Create 3-7 currencies per company
            $currencyCount = rand(8, 10);
            $availableCurrencies = config('currencies.available_currencies', []);

            // Randomly select currencies for this company
            $selectedCurrencies = collect($availableCurrencies)->random($currencyCount);

            foreach ($selectedCurrencies as $index => $currency) {
                ClientBillingCurrency::firstOrCreate(
                    ['code' => $currency['code'] . '_' . $companyUser->id, 'created_by' => $companyUser->id],
                    [
                        'name' => $currency['name'],
                        'symbol' => $currency['symbol'],
                        'description' => $currency['description'],
                        'is_default' => $index === 0, // First currency is default for this company
                        'status' => true,
                        'created_by' => $companyUser->id,
                    ]
                );
            }
        }
    }
}
