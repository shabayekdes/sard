<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CompanyProfileSeeder extends Seeder
{
    public function run(): void
    {
        $companyUsers = User::where('type', 'company')->get();
        
        if ($companyUsers->isEmpty()) {
            $this->command->info('No company users found. Please create company users first.');
            return;
        }

        $specializations = [
            'Commercial Law, Contracts, Corporate Governance',
            'Family Law, Personal Status, Custody',
            'Criminal Defense, Appeals, Investigations',
            'Personal Injury, Medical Malpractice, Insurance Claims',
            'Real Estate, Property Law, Lease Disputes',
            'Labor Law, Employment Contracts, Workplace Disputes'
        ];
        
        foreach ($companyUsers as $index => $companyUser) {
            $establishmentYear = rand(2005, 2020);
            $experience = date('Y') - $establishmentYear;
            
            CompanyProfile::firstOrCreate([
                'created_by' => $companyUser->id
            ], [
                // Contact Details
                'email' => $companyUser->email,
                'phone' => '+966-5' . rand(10000000, 99999999),
                'address' => 'Prince Turki St, Al Khobar ' . (31952 + $index),
                
                // Business Details
                'consultation_fees' => rand(300, 1200),
                'office_hours' => 'Sunday to Thursday: 9:00 AM - 6:00 PM',
                'success_rate' => rand(75, 95),
                
                // Company Details
                'name' => $companyUser->name,
                'registration_number' => 'CR-' . $establishmentYear . '-' . str_pad($companyUser->id, 6, '0', STR_PAD_LEFT),
                'establishment_date' => $establishmentYear . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'cr' => 'CR-' . str_pad($companyUser->id, 8, '0', STR_PAD_LEFT),
                'tax_number' => 'TAX-' . str_pad($companyUser->id, 8, '0', STR_PAD_LEFT),
                'company_size' => ['small', 'medium', 'large'][rand(0, 2)],
                'business_type' => 'law_firm',
                'default_setup' => 'Standard',
                
                // Services
                'services_offered' => 'Legal Consultation, Contract Drafting, Legal Advisory, Litigation Support, Court Representation, Legal Documentation',
                'description' => $companyUser->name . ' is a professional law firm specializing in ' . explode(',', $specializations[$index % count($specializations)])[0] . '. With ' . $experience . ' years of experience, we provide comprehensive legal solutions.',
                'created_by' => $companyUser->id
            ]);
        }
    }
}