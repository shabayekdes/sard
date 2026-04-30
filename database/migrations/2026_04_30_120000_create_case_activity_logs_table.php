<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases')->cascadeOnDelete();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestampTz('occurred_at');
            $table->string('source', 16); // automatic | manual
            $table->string('category', 32); // case, hearing, judgment, referral, document, task, note, assignee, timeline
            $table->string('event_key', 64);
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('case_timeline_id')->nullable()->unique()->constrained('case_timelines')->nullOnDelete();
            $table->nullableMorphs('subject');
            $table->timestamps();

            $table->index(['case_id', 'occurred_at']);
            $table->index(['tenant_id', 'case_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_activity_logs');
    }
};
