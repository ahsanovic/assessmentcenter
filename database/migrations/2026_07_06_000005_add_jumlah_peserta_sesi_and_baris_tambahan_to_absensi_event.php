<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi_event', function (Blueprint $table) {
            if (! Schema::hasColumn('absensi_event', 'jumlah_peserta_sesi')) {
                $table->unsignedSmallInteger('jumlah_peserta_sesi')->nullable()->after('peserta_sampai');
            }

            if (! Schema::hasColumn('absensi_event', 'baris_tambahan')) {
                $table->unsignedSmallInteger('baris_tambahan')->default(0)->after('jumlah_peserta_sesi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('absensi_event', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('absensi_event', 'jumlah_peserta_sesi')) {
                $columns[] = 'jumlah_peserta_sesi';
            }

            if (Schema::hasColumn('absensi_event', 'baris_tambahan')) {
                $columns[] = 'baris_tambahan';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
