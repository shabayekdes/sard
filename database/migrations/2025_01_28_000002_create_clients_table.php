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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('client_id'); // Auto-generated client ID
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('client_type_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('company_name')->nullable(); // For corporate clients
            $table->string('tax_id')->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->date('date_of_birth')->nullable();
            $table->text('notes')->nullable();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            // Index for better performance
            $table->index(['tenant_id', 'status']);
            $table->unique(['tenant_id', 'client_id']);
            $table->unique(['tenant_id', 'email']);
            $table->unique(['tenant_id', 'phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};