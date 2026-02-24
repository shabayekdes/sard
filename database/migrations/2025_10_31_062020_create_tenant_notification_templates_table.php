<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_notification_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('is_active')->default(0);
            $table->string('type')->default('slack'); // twilio, slack, email
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('notification_templates')->onDelete('cascade');
            $table->unique(['tenant_id', 'template_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_notification_templates');
    }
};
