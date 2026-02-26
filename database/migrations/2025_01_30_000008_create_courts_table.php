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
        Schema::create('courts', function (Blueprint $table) {
            $table->id();
            $table->string('court_id'); // Auto-generated court ID
            $table->string('name');
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('facilities')->nullable(); // JSON field for courtroom facilities
            $table->text('notes')->nullable();
            $table->foreignUuid('tenant_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            
            // Index for better performance
            $table->index(['tenant_id', 'status']);
            $table->unique(['tenant_id', 'court_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courts');
    }
};