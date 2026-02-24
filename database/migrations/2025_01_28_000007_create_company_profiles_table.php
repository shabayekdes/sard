<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('company_id')->unique();
            $table->string('name');
            $table->string('registration_number')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->date('establishment_date')->nullable();
            $table->enum('business_type', ['law_firm', 'corporate_legal', 'government', 'other'])->default('law_firm');
            $table->string('cr')->nullable();
            $table->string('tax_number')->nullable();
            $table->enum('company_size', ['solo', 'small', 'medium', 'large'])->default('solo');
            $table->string('office_hours')->nullable();
            $table->decimal('consultation_fees', 10, 2)->nullable();
            $table->integer('success_rate')->nullable();
            $table->text('services_offered')->nullable();
            $table->string('default_setup')->nullable();
            $table->text('description')->nullable();
            $table->foreignUuid('tenant_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            
            // Index for better performance
            $table->index(['tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};