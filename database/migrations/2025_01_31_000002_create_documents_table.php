<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained('document_categories')->onDelete('cascade');
            $table->string('file_path');
            $table->enum('status', ['draft', 'review', 'final', 'archived'])->default('draft');
            $table->enum('confidentiality', ['public', 'internal', 'confidential', 'restricted'])->default('internal');
            $table->json('tags')->nullable();
            $table->foreignUuid('tenant_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            
            $table->index(['tenant_id', 'status']);
            $table->index(['category_id']);
            $table->index(['confidentiality']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};