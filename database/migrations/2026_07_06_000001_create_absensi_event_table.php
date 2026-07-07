<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_event', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->unique()->constrained('event')->cascadeOnDelete();
            $table->text('judul');
            $table->string('hari', 50)->nullable();
            $table->date('tanggal');
            $table->unsignedTinyInteger('sesi')->nullable();
            $table->string('waktu_mulai', 20);
            $table->string('waktu_selesai', 20)->nullable();
            $table->string('zona_waktu', 10)->default('WIB');
            $table->string('tempat');
            $table->unsignedTinyInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_event');
    }
};
