<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('research_sources', function (Blueprint $table) {
            $table->id();
            $table->json('source_name');
            $table->string('source_type');
            $table->json('description')->nullable();
            $table->string('url')->nullable();
            $table->text('access_info')->nullable();
            $table->json('credentials')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['created_by', 'status']);
            $table->index(['source_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_sources');
    }
};