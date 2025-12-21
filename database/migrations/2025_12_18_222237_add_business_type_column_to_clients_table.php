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
        Schema::table('clients', function (Blueprint $table) {
            $table->after('client_id', function (Blueprint $table) {
                $table->enum('business_type', ['b2c', 'b2b'])->default('b2c');
                $table->string('nationality')->nullable();
                $table->string('id_number')->nullable();
                $table->string('gender')->nullable();
                $table->string('unified_number')->nullable();
                $table->string('cr_number')->nullable();

                $table->date('cr_issuance_date')->nullable();
                $table->string('vat_number')->nullable();
            });

            $table->after('address', function (Blueprint $table) {
                $table->string('country')->nullable();
                $table->string('city')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'business_type',
                'nationality',
                'id_number',
                'gender',
                'unified_number',
                'cr_number',
                'cr_issuance_date',
                'vat_number',
                'country',
                'city'
            ]);
        });
    }
};
