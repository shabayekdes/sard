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
        Schema::table('cases', function (Blueprint $table) {
            $table->string('file_number')->nullable()->after('case_id');
            $table->enum('attributes', ['petitioner', 'respondent'])->nullable()->after('file_number');
            $table->dropColumn([
                'opposing_party',
                'court_details'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->dropColumn(['file_number']);
            $table->string('opposing_party')->nullable()->after('case_id');
            $table->text('court_details')->nullable()->after('opposing_party');
        });
    }
};
