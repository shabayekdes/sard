<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hearings', function (Blueprint $table) {
            $table->dropForeign(['judge_id']);
            $table->dropColumn('judge_id');
        });

        Schema::dropIfExists('judges');
    }

    public function down(): void
    {
        Schema::create('judges', function (Blueprint $table) {
            $table->id();
            $table->string('judge_id')->unique();
            $table->string('name');
            $table->foreignId('court_id')->nullable()->constrained('courts')->onDelete('set null');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->text('preferences')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::table('hearings', function (Blueprint $table) {
            $table->foreignId('judge_id')->nullable()->after('circle_number')->constrained('judges')->onDelete('set null');
        });
    }
};
