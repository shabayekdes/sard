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
        Schema::table('hearings', function (Blueprint $table) {
            $table->dropForeign(['court_id']);
        });

        Schema::table('hearings', function (Blueprint $table) {
            $table->unsignedBigInteger('court_id')->nullable()->change();
        });

        Schema::table('hearings', function (Blueprint $table) {
            $table->foreign('court_id')->references('id')->on('courts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hearings', function (Blueprint $table) {
            $table->dropForeign(['court_id']);
        });

        Schema::table('hearings', function (Blueprint $table) {
            $table->unsignedBigInteger('court_id')->nullable(false)->change();
        });

        Schema::table('hearings', function (Blueprint $table) {
            $table->foreign('court_id')->references('id')->on('courts')->onDelete('cascade');
        });
    }
};
