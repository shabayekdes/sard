<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courts', function (Blueprint $table) {
            $table->foreignId('circle_type_id')->nullable()->after('court_type_id')->constrained('circle_types')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('courts', function (Blueprint $table) {
            $table->dropForeign(['circle_type_id']);
            $table->dropColumn('circle_type_id');
        });
    }
};

