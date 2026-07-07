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
            $table->dropUnique(['event_id', 'sesi']);
        });

        Schema::table('absensi_event', function (Blueprint $table) {
            $table->unique(['event_id', 'tanggal', 'sesi']);
            $table->foreign('event_id')->references('id')->on('event')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('absensi_event', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropUnique(['event_id', 'tanggal', 'sesi']);
        });

        Schema::table('absensi_event', function (Blueprint $table) {
            $table->unique(['event_id', 'sesi']);
            $table->foreign('event_id')->references('id')->on('event')->cascadeOnDelete();
        });
    }
};
