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
        Schema::create('case_notes', function (Blueprint $table) {
            $table->id();
            $table->string('note_id')->unique();
            $table->string('title');
            $table->text('content');
            $table->enum('note_type', ['general', 'meeting', 'research', 'strategy', 'client_communication', 'court_appearance'])->default('general');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->boolean('is_private')->default(false);
            $table->date('note_date')->nullable();
            $table->json('tags')->nullable();
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->foreignUuid('tenant_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            
            // Index for better performance
            $table->index(['tenant_id', 'status']);
            $table->index(['note_type', 'tenant_id']);
            $table->index(['priority', 'tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_notes');
    }
};