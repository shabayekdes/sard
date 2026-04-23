<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hearings', function (Blueprint $table) {
            $table->string('minutes_title', 500)->nullable()->after('outcome');
            $table->date('minutes_date')->nullable()->after('minutes_title');
            $table->longText('minutes_content')->nullable()->after('minutes_date');
        });
    }

    public function down(): void
    {
        Schema::table('hearings', function (Blueprint $table) {
            $table->dropColumn(['minutes_title', 'minutes_date', 'minutes_content']);
        });
    }
};
