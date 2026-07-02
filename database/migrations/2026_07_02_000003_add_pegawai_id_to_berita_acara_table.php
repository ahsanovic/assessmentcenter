<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('berita_acara', function (Blueprint $table) {
            $table->foreignId('admin_pegawai_id')->nullable()->after('admin_nip')->constrained('ref_pegawai')->nullOnDelete();
            $table->foreignId('tester_pegawai_id')->nullable()->after('tester_nip')->constrained('ref_pegawai')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('berita_acara', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_pegawai_id');
            $table->dropConstrainedForeignId('tester_pegawai_id');
        });
    }
};
