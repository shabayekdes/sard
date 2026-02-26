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
        Schema::create('case_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_id');
            $table->string('document_name');
            $table->string('file_path');
            $table->text('description')->nullable();
            $table->enum('confidentiality', ['public', 'confidential', 'privileged'])->default('confidential');
            $table->date('document_date')->nullable();
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->foreignUuid('tenant_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            
            // Index for better performance
            $table->index(['tenant_id', 'status']);
            $table->unique(['tenant_id', 'document_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_documents');
    }
};