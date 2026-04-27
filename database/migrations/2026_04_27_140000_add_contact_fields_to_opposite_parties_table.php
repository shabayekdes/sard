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
        Schema::table('opposite_parties', function (Blueprint $table) {
            $table->enum('business_type', ['b2c', 'b2b'])->default('b2c')->after('lawyer_name');
            $table->date('date_of_birth')->nullable()->after('business_type');
            $table->string('phone')->nullable()->after('date_of_birth');
            $table->string('email')->nullable()->after('phone');
            $table->text('address')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opposite_parties', function (Blueprint $table) {
            $table->dropColumn([
                'business_type',
                'date_of_birth',
                'phone',
                'email',
                'address',
            ]);
        });
    }
};
