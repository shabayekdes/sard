<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_judgments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases')->cascadeOnDelete();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('judgment_number');
            $table->date('judgment_date');
            $table->date('receipt_date')->nullable();
            $table->date('appeal_deadline_date')->nullable();
            $table->boolean('appeal_reminder_enabled')->default(false);
            $table->string('appeal_reminder_duration', 32)->default('one_day_before');
            $table->unsignedSmallInteger('appeal_reminder_custom_days')->nullable();
            $table->string('status', 32)->default('pending_issuance');
            $table->json('attachment_paths')->nullable();
            $table->text('grounds')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();

            $table->index(['case_id', 'judgment_date'], 'case_judgments_case_id_judgment_date_idx');
            $table->index(['tenant_id', 'appeal_deadline_date'], 'cj_tenant_appeal_deadline_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_judgments');
    }
};
