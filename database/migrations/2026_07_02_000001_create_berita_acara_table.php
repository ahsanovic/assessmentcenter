<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita_acara', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('event')->cascadeOnDelete();
            $table->string('judul');
            $table->string('hari')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('waktu_mulai', 20)->nullable();
            $table->string('waktu_selesai', 20)->nullable();
            $table->string('pejabat')->nullable();
            $table->string('di_lingkungan_pemerintah')->nullable();
            $table->string('ruang')->nullable();
            $table->unsignedInteger('jumlah_peserta_seharusnya')->nullable();
            $table->unsignedInteger('jumlah_peserta_tidak_hadir')->default(0);
            $table->unsignedInteger('jumlah_peserta_hadir')->default(0);
            $table->text('nomor_tidak_hadir')->nullable();
            $table->text('catatan')->nullable();
            $table->string('admin_nama')->nullable();
            $table->string('admin_nip', 50)->nullable();
            $table->string('tester_nama')->nullable();
            $table->string('tester_nip', 50)->nullable();
            $table->unsignedTinyInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita_acara');
    }
};
