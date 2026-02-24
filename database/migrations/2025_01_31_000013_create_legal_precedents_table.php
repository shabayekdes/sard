<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_precedents', function (Blueprint $table) {
            $table->id();
            $table->string('case_name');
            $table->string('citation');
            $table->string('jurisdiction');
            $table->text('summary');
            $table->foreignId('category_id')->nullable()->constrained('research_categories')->onDelete('set null');
            $table->integer('relevance_score')->default(5);
            $table->date('decision_date')->nullable();
            $table->string('court_level')->nullable();
            $table->json('key_points')->nullable();
            $table->enum('status', ['active', 'overruled', 'questioned', 'archived'])->default('active');
            $table->foreignUuid('tenant_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['tenant_id', 'status']);
            $table->index(['category_id']);
            $table->index(['jurisdiction']);
            $table->index(['relevance_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_precedents');
    }
};