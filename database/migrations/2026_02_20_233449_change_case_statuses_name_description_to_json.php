<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('case_statuses', function (Blueprint $table) {
            $table->json('name_json')->after('id');
            $table->json('description_json')->nullable()->after('name_json');
        });

        $rows = DB::table('case_statuses')->get();
        foreach ($rows as $row) {
            $nameVal = $row->name;
            $nameJson = is_string($nameVal) || $nameVal === null
                ? json_encode(['en' => $nameVal ?? '', 'ar' => $nameVal ?? ''])
                : (is_array($nameVal) ? json_encode($nameVal) : $nameVal);
            $desc = $row->description ?? '';
            $descriptionJson = is_string($desc)
                ? json_encode(['en' => $desc, 'ar' => $desc])
                : (is_array($row->description) ? json_encode($row->description) : $row->description);

            DB::table('case_statuses')->where('id', $row->id)->update([
                'name_json' => $nameJson,
                'description_json' => $descriptionJson,
            ]);
        }

        Schema::table('case_statuses', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });

        Schema::table('case_statuses', function (Blueprint $table) {
            $table->json('name')->after('id');
            $table->json('description')->nullable()->after('name');
        });

        foreach (DB::table('case_statuses')->get() as $row) {
            DB::table('case_statuses')->where('id', $row->id)->update([
                'name' => $row->name_json,
                'description' => $row->description_json,
            ]);
        }

        Schema::table('case_statuses', function (Blueprint $table) {
            $table->dropColumn(['name_json', 'description_json']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('case_statuses', function (Blueprint $table) {
            $table->string('name_old')->after('id');
            $table->text('description_old')->nullable()->after('name_old');
        });

        $rows = DB::table('case_statuses')->get();
        foreach ($rows as $row) {
            $nameDecoded = is_string($row->name) ? json_decode($row->name, true) : $row->name;
            $nameStr = is_array($nameDecoded) ? ($nameDecoded['en'] ?? $nameDecoded['ar'] ?? '') : (string) $row->name;
            $descDecoded = is_string($row->description) ? json_decode($row->description, true) : $row->description;
            $descStr = is_array($descDecoded) ? ($descDecoded['en'] ?? $descDecoded['ar'] ?? '') : (string) ($row->description ?? '');

            DB::table('case_statuses')->where('id', $row->id)->update([
                'name_old' => $nameStr,
                'description_old' => $descStr,
            ]);
        }

        Schema::table('case_statuses', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });

        Schema::table('case_statuses', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->text('description')->nullable()->after('name');
        });

        foreach (DB::table('case_statuses')->get() as $row) {
            DB::table('case_statuses')->where('id', $row->id)->update([
                'name' => $row->name_old ?? '',
                'description' => $row->description_old ?? '',
            ]);
        }

        Schema::table('case_statuses', function (Blueprint $table) {
            $table->dropColumn(['name_old', 'description_old']);
        });
    }
};
