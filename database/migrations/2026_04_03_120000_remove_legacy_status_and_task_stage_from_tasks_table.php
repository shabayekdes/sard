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
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'status')) {
                $table->dropIndex(['tenant_id', 'status']);
                $table->dropColumn('status');
            }
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->integer('progress')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (! Schema::hasColumn('tasks', 'status')) {
                $table->enum('status', ['not_started', 'in_progress', 'completed', 'on_hold'])->default('not_started');
                $table->index(['tenant_id', 'status']);
            }
        });
    }
};
