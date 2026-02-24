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
        Schema::create('tenant_email_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('is_active')->default(1);
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('email_templates')->onDelete('cascade');
            $table->unique(['tenant_id', 'template_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_email_templates');
    }
};
