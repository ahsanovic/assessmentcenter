<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['ujian_literasi_digital', 'hasil_literasi_digital'] as $table) {
            if (Schema::hasColumn($table, 'persentase') && ! Schema::hasColumn($table, 'jpm')) {
                DB::statement("ALTER TABLE `{$table}` CHANGE `persentase` `jpm` DECIMAL(6,2) NOT NULL DEFAULT 0");
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if (! Schema::hasColumn($table, 'kategori')) {
                    $blueprint->string('kategori', 32)->nullable()->after('jpm');
                }
                if (! Schema::hasColumn($table, 'level_budaya')) {
                    $blueprint->unsignedTinyInteger('level_budaya')->default(1)->after('skor_budaya');
                }
                if (! Schema::hasColumn($table, 'level_cakap')) {
                    $blueprint->unsignedTinyInteger('level_cakap')->default(1)->after('skor_cakap');
                }
                if (! Schema::hasColumn($table, 'level_keamanan')) {
                    $blueprint->unsignedTinyInteger('level_keamanan')->default(1)->after('skor_keamanan');
                }
                if (! Schema::hasColumn($table, 'level_etika')) {
                    $blueprint->unsignedTinyInteger('level_etika')->default(1)->after('skor_etika');
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['hasil_literasi_digital', 'ujian_literasi_digital'] as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                foreach (['kategori', 'level_budaya', 'level_cakap', 'level_keamanan', 'level_etika'] as $column) {
                    if (Schema::hasColumn($table, $column)) {
                        $blueprint->dropColumn($column);
                    }
                }
            });

            if (Schema::hasColumn($table, 'jpm') && ! Schema::hasColumn($table, 'persentase')) {
                DB::statement("ALTER TABLE `{$table}` CHANGE `jpm` `persentase` DECIMAL(5,2) NOT NULL DEFAULT 0");
            }
        }
    }
};
