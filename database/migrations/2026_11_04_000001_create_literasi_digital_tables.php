<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref_pilar_literasi_digital', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 32)->unique();
            $table->string('nama');
            $table->unsignedTinyInteger('urutan');
            $table->enum('is_active', ['true', 'false'])->default('true');
            $table->timestamps();
        });

        Schema::create('soal_literasi_digital', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilar_id')->constrained('ref_pilar_literasi_digital')->restrictOnDelete();
            $table->text('soal');
            $table->text('opsi_a');
            $table->text('opsi_b');
            $table->text('opsi_c');
            $table->text('opsi_d');
            $table->text('opsi_e');
            $table->char('kunci_jawaban', 1);
            $table->enum('is_active', ['true', 'false'])->default('true');
            $table->timestamps();

            $table->index(['pilar_id', 'is_active']);
        });

        Schema::create('ujian_literasi_digital', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('event')->cascadeOnDelete();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete();
            $table->unsignedSmallInteger('jumlah_soal')->default(0);
            $table->unsignedSmallInteger('skor_budaya')->default(0);
            $table->unsignedSmallInteger('skor_cakap')->default(0);
            $table->unsignedSmallInteger('skor_keamanan')->default(0);
            $table->unsignedSmallInteger('skor_etika')->default(0);
            $table->unsignedSmallInteger('skor_total')->default(0);
            $table->decimal('persentase', 5, 2)->default(0);
            $table->enum('is_finished', ['true', 'false'])->default('false');
            $table->timestamp('waktu_tes_berakhir')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'peserta_id']);
        });

        Schema::create('jawaban_literasi_digital', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujian_literasi_digital')->cascadeOnDelete();
            $table->foreignId('soal_id')->constrained('soal_literasi_digital')->restrictOnDelete();
            $table->foreignId('pilar_id')->constrained('ref_pilar_literasi_digital')->restrictOnDelete();
            $table->unsignedSmallInteger('urutan');
            $table->char('jawaban', 1)->nullable();
            $table->unsignedTinyInteger('skor')->default(0);
            $table->timestamps();

            $table->unique(['ujian_id', 'urutan']);
            $table->unique(['ujian_id', 'soal_id']);
        });

        Schema::create('hasil_literasi_digital', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('event')->cascadeOnDelete();
            $table->foreignId('peserta_id')->constrained('peserta')->cascadeOnDelete();
            $table->foreignId('ujian_id')->unique()->constrained('ujian_literasi_digital')->cascadeOnDelete();
            $table->unsignedSmallInteger('jumlah_soal')->default(0);
            $table->unsignedSmallInteger('skor_budaya')->default(0);
            $table->unsignedSmallInteger('skor_cakap')->default(0);
            $table->unsignedSmallInteger('skor_keamanan')->default(0);
            $table->unsignedSmallInteger('skor_etika')->default(0);
            $table->unsignedSmallInteger('skor_total')->default(0);
            $table->decimal('persentase', 5, 2)->default(0);
            $table->timestamps();

            $table->index(['event_id', 'peserta_id']);
        });

        $now = now();
        DB::table('ref_pilar_literasi_digital')->insert([
            ['kode' => 'budaya', 'nama' => 'Budaya Digital', 'urutan' => 1, 'is_active' => 'true', 'created_at' => $now, 'updated_at' => $now],
            ['kode' => 'cakap', 'nama' => 'Cakap Digital', 'urutan' => 2, 'is_active' => 'true', 'created_at' => $now, 'updated_at' => $now],
            ['kode' => 'keamanan', 'nama' => 'Keamanan dan Keselamatan Digital', 'urutan' => 3, 'is_active' => 'true', 'created_at' => $now, 'updated_at' => $now],
            ['kode' => 'etika', 'nama' => 'Etika Digital', 'urutan' => 4, 'is_active' => 'true', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('ref_metode_tes')->where('id', 3)->update([
            'metode_tes' => 'Literasi Digital',
            'updated_at' => $now,
        ]);

        DB::table('ref_jenis_tes')->where('id', 2)->update([
            'jenis_tes' => 'Tes Literasi Digital',
            'updated_at' => $now,
        ]);

        $waktuAda = DB::table('setting_waktu_tes')->where('jenis_tes', 2)->exists();
        if ($waktuAda) {
            DB::table('setting_waktu_tes')->where('jenis_tes', 2)->update([
                'waktu' => 60,
                'is_active' => 'true',
                'updated_at' => $now,
            ]);
        } else {
            DB::table('setting_waktu_tes')->insert([
                'jenis_tes' => 2,
                'waktu' => 60,
                'is_active' => 'true',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_literasi_digital');
        Schema::dropIfExists('jawaban_literasi_digital');
        Schema::dropIfExists('ujian_literasi_digital');
        Schema::dropIfExists('soal_literasi_digital');
        Schema::dropIfExists('ref_pilar_literasi_digital');

        DB::table('ref_metode_tes')->where('id', 3)->update([
            'metode_tes' => 'Literasi Digital & Emerging Skill',
        ]);

        DB::table('ref_jenis_tes')->where('id', 2)->update([
            'jenis_tes' => 'Tes Literasi Digital & Emerging',
        ]);
    }
};
