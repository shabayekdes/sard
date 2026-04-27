<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->longText('case_subject')->nullable()->after('description');
            $table->longText('plaintiff_requests')->nullable()->after('case_subject');
            $table->longText('plaintiff_evidence')->nullable()->after('plaintiff_requests');
            $table->longText('defendant_requests')->nullable()->after('plaintiff_evidence');
            $table->longText('defendant_evidence')->nullable()->after('defendant_requests');
        });
    }

    public function down(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->dropColumn([
                'case_subject',
                'plaintiff_requests',
                'plaintiff_evidence',
                'defendant_requests',
                'defendant_evidence',
            ]);
        });
    }
};
