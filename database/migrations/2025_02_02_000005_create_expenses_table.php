<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('case_id')->nullable();
            $table->unsignedBigInteger('expense_category_id');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->boolean('is_billable')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->json('receipt_file')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreignUuid('tenant_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('expense_category_id')->references('id')->on('expense_categories')->onDelete('cascade');
            $table->index(['tenant_id', 'is_billable']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};