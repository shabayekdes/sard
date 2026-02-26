<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('case_id')->nullable();
            $table->foreignId('currency_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number');
            $table->enum('status', ['draft', 'sent', 'paid', 'partial_paid', 'overdue', 'cancelled'])->default('draft');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->date('invoice_date');
            $table->date('due_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('case_id')->references('id')->on('cases')->onDelete('set null');
            $table->index(['tenant_id', 'status']);
            $table->index(['client_id', 'status']);

            $table->unique(['tenant_id', 'invoice_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};