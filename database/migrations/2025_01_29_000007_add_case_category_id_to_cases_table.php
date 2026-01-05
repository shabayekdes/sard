<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->foreignId('case_category_id')->nullable()->after('case_type_id')->constrained('case_categories')->onDelete('set null');
            $table->foreignId('case_subcategory_id')->nullable()->after('case_category_id')->constrained('case_categories')->onDelete('set null');
            $table->index(['case_category_id']);
            $table->index(['case_subcategory_id']);
        });
    }

    public function down(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->dropForeign(['case_category_id']);
            $table->dropIndex(['case_category_id']);
            $table->dropColumn('case_category_id');
            $table->dropForeign(['case_subcategory_id']);
            $table->dropIndex(['case_subcategory_id']);
            $table->dropColumn('case_subcategory_id');
        });
    }
};

