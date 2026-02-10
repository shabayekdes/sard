<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('invoice_id');
            $table->string('payment_method');
            $table->string('approval_status')->default('approved');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();

            $table->decimal('amount', 10, 2);
            $table->date('payment_date');

            $table->text('notes')->nullable();
            $table->json('attachment')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->index(['created_by', 'payment_date']);
            $table->index(['payment_method', 'approval_status'], 'payments_method_approval_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};