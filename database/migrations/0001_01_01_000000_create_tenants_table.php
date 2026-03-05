<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Same as company User id for single-database

            // Plan-related columns (direct columns); FK added in 2025_06_18_000002 (after plans table exists)
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->date('plan_expire_date')->nullable();
            $table->integer('plan_is_active')->default(0);
            $table->integer('requested_plan')->default(0);
            $table->float('storage_limit', 15, 2)->default(0.00);
            $table->float('storage_used', 15, 2)->default(0.00);
            $table->string('is_trial')->nullable();
            $table->integer('trial_day')->default(0);
            $table->date('trial_expire_date')->nullable();
            $table->timestamp('activated_at')->nullable();

            $table->timestamps();
            // Custom tenant data (JSON): name, phone, email, city
            $table->json('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
