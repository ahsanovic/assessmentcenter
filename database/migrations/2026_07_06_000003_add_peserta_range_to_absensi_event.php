<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi_event', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropUnique(['event_id', 'assessor_id']);
            $table->dropColumn('assessor_id');
        });

        Schema::table('absensi_event', function (Blueprint $table) {
            $table->unsignedSmallInteger('peserta_dari')->nullable()->after('sesi');
            $table->unsignedSmallInteger('peserta_sampai')->nullable()->after('peserta_dari');
            $table->unique(['event_id', 'sesi']);
            $table->foreign('event_id')->references('id')->on('event')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('absensi_event', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropUnique(['event_id', 'sesi']);
            $table->dropColumn(['peserta_dari', 'peserta_sampai']);
        });

        Schema::table('absensi_event', function (Blueprint $table) {
            $table->unsignedBigInteger('assessor_id')->default(0)->after('event_id');
            $table->unique(['event_id', 'assessor_id']);
            $table->foreign('event_id')->references('id')->on('event')->cascadeOnDelete();
        });
    }
};
