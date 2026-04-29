<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_referrals', function (Blueprint $table) {
            // Project pattern note: variable per-stage payload is stored in JSON (same approach as cases.authority_type_details).
            $table->id();
            $table->foreignId('case_id')->constrained('cases')->cascadeOnDelete();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('stage', 30)->default('amicable_settlement');
            $table->date('referral_date');
            $table->boolean('reminder_enabled')->default(false);
            $table->unsignedInteger('reminder_duration')->nullable();
            $table->json('stage_data')->nullable();
            $table->json('attachments')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['case_id', 'stage'], 'case_referrals_case_stage_idx');
            $table->index(['tenant_id', 'deleted_at'], 'case_referrals_tenant_deleted_idx');
        });

        Schema::create('case_referral_execution_requesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->constrained('case_referrals')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('national_id')->nullable();
            $table->timestamps();
        });

        Schema::create('case_referral_execution_respondents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->constrained('case_referrals')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('national_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_referral_execution_respondents');
        Schema::dropIfExists('case_referral_execution_requesters');
        Schema::dropIfExists('case_referrals');
    }
};
