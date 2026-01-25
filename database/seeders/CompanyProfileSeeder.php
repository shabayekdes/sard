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

        $advocateNames = [
            'Abdullah Al-Qahtani', 'Maha Al-Harbi', 'Fahad Al-Otaibi',
            'Noura Al-Ghamdi', 'Yousef Al-Zahrani', 'Hanan Al-Dosari'
        ];
        
        $specializations = [
            'Commercial Law, Contracts, Corporate Governance',
            'Family Law, Personal Status, Custody',
            'Criminal Defense, Appeals, Investigations',
            'Personal Injury, Medical Malpractice, Insurance Claims',
            'Real Estate, Property Law, Lease Disputes',
            'Labor Law, Employment Contracts, Workplace Disputes'
        ];
        
        $universities = [
            'King Saud University College of Law',
            'King Abdulaziz University Faculty of Law',
            'Imam Mohammad Ibn Saud Islamic University',
            'Princess Nourah University College of Law',
            'Umm Al-Qura University Faculty of Law',
            'Qassim University College of Law'
        ];
        
        foreach ($companyUsers as $index => $companyUser) {
            $establishmentYear = rand(2005, 2020);
            $experience = date('Y') - $establishmentYear;
            
            CompanyProfile::firstOrCreate([
                'created_by' => $companyUser->id
            ], [
                // Personal Details
                'advocate_name' => $advocateNames[$index % count($advocateNames)],
                'bar_registration_number' => 'SCBA/' . $establishmentYear . '/' . str_pad($companyUser->id, 5, '0', STR_PAD_LEFT),
                'years_of_experience' => $experience,
                
                // Contact Details
                'email' => $companyUser->email,
                'phone' => '+966-5' . rand(10000000, 99999999),
                'website' => 'https://' . strtolower(str_replace(' ', '', $companyUser->name)) . '.sa',
                'address' => 'Prince Turki St, Al Khobar ' . (31952 + $index),
                
                // Professional Details
                'law_degree' => 'JD, LLM (' . explode(',', $specializations[$index % count($specializations)])[0] . ')',
                'university' => $universities[$index % count($universities)],
                'specialization' => $specializations[$index % count($specializations)],
                
                // Court & Jurisdiction
                'court_jurisdictions' => 'Saudi Courts, Board of Grievances, Commercial Courts',
                'languages_spoken' => 'Arabic, English',
                
                // Business Details
                'consultation_fees' => rand(300, 1200),
                'office_hours' => 'Sunday to Thursday: 9:00 AM - 6:00 PM',
                'success_rate' => rand(75, 95),
                
                // Company Details
                'name' => $companyUser->name,
                'registration_number' => 'CR-' . $establishmentYear . '-' . str_pad($companyUser->id, 6, '0', STR_PAD_LEFT),
                'establishment_date' => $establishmentYear . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                'company_size' => ['small', 'medium', 'large'][rand(0, 2)],
                'business_type' => 'law_firm',
                
                // Services
                'services_offered' => 'Legal Consultation, Contract Drafting, Legal Advisory, Litigation Support, Court Representation, Legal Documentation',
                'notable_cases' => 'Successfully handled major cases in ' . ($establishmentYear + 2) . ', ' . ($establishmentYear + 4) . ', and ' . ($establishmentYear + 6) . '. Recognized for excellence in legal practice.',
                'description' => $companyUser->name . ' is a professional law firm specializing in ' . explode(',', $specializations[$index % count($specializations)])[0] . '. With ' . $experience . ' years of experience, we provide comprehensive legal solutions.',
                'status' => 'active',
                'created_by' => $companyUser->id
            ]);
        }
    }
}