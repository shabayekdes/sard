<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_timelines', function (Blueprint $table) {
            $table->unsignedSmallInteger('duration_minutes')->nullable()->after('event_date');
            $table->time('event_time')->nullable()->after('event_date');
        });

        DB::statement("UPDATE case_timelines SET event_time = TIME(event_date) WHERE event_date IS NOT NULL");
    }

    public function down(): void
    {
        Schema::table('case_timelines', function (Blueprint $table) {
            $table->dropColumn('duration_minutes');
            $table->dropColumn('event_time');
        });
    }
};
