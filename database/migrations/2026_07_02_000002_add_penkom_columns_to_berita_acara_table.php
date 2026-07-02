<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('berita_acara', function (Blueprint $table) {
            // Umum
            $table->string('mekanisme_penkom', 20)->default('tusi')->after('event_id');
            $table->string('jenis_penkom')->nullable()->after('mekanisme_penkom');
            $table->string('nama_kegiatan')->nullable()->after('jenis_penkom');

            // Khusus mekanisme retribusi
            $table->string('nomor_surat')->nullable()->after('judul');
            $table->string('tempat')->nullable()->after('ruang');
            $table->string('zona_waktu', 10)->nullable()->after('waktu_selesai');
            $table->unsignedInteger('pejabat_dinilai')->nullable()->after('pejabat');
            $table->text('alasan_tidak_hadir')->nullable()->after('nomor_tidak_hadir');
            $table->date('tanggal_penyerahan_rekap')->nullable()->after('catatan');
            $table->date('tanggal_penyerahan_laporan')->nullable()->after('tanggal_penyerahan_rekap');
            $table->string('panitia1_instansi')->nullable()->after('admin_pegawai_id');
            $table->string('panitia2_instansi')->nullable()->after('tester_pegawai_id');
        });
    }

    public function down(): void
    {
        Schema::table('berita_acara', function (Blueprint $table) {
            $table->dropColumn([
                'mekanisme_penkom',
                'jenis_penkom',
                'nama_kegiatan',
                'nomor_surat',
                'tempat',
                'zona_waktu',
                'pejabat_dinilai',
                'alasan_tidak_hadir',
                'tanggal_penyerahan_rekap',
                'tanggal_penyerahan_laporan',
                'panitia1_instansi',
                'panitia2_instansi',
            ]);
        });
    }
};
