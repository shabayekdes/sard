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
        Schema::create('practice_areas', function (Blueprint $table) {
            $table->id();
            $table->string('area_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('expertise_level', ['beginner', 'intermediate', 'expert'])->default('intermediate');
            $table->boolean('is_primary')->default(false);
            $table->text('certifications')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignUuid('tenant_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            
            // Index for better performance
            $table->index(['tenant_id', 'status']);
            $table->index(['name', 'tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_areas');
    }
};