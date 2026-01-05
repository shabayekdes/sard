<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_types', function (Blueprint $table) {
            $table->foreignId('case_category_id')->nullable()->after('description')->constrained('case_categories')->onDelete('set null');
            $table->index(['case_category_id']);
        });
    }

    public function down(): void
    {
        Schema::table('case_types', function (Blueprint $table) {
            $table->dropForeign(['case_category_id']);
            $table->dropIndex(['case_category_id']);
            $table->dropColumn('case_category_id');
        });
    }
};

