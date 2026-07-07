<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi_event', function (Blueprint $table) {
            $table->unsignedSmallInteger('jumlah_peserta_sesi')->nullable()->after('peserta_sampai');
            $table->unsignedSmallInteger('baris_tambahan')->default(0)->after('jumlah_peserta_sesi');
        });
    }

    public function down(): void
    {
        Schema::table('absensi_event', function (Blueprint $table) {
            $table->dropColumn(['jumlah_peserta_sesi', 'baris_tambahan']);
        });
    }
};
