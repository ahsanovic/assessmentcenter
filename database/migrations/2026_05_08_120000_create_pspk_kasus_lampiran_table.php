<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('soal_pspk', function (Blueprint $table) {
            if (! Schema::hasColumn('soal_pspk', 'jenis_soal')) {
                $table->unsignedTinyInteger('jenis_soal')->nullable()->after('level_pspk_id');
            }
        });

        Schema::create('pspk_kasus_lampiran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('level_pspk_id');
            $table->string('nama', 191)->nullable();
            $table->string('lampiran_pdf_path', 512);
            $table->timestamps();

            $table->index('level_pspk_id');
        });

        Schema::table('soal_pspk', function (Blueprint $table) {
            $table->foreignId('kasus_lampiran_id')->nullable()->after('kunci_jawaban')
                ->constrained('pspk_kasus_lampiran')->nullOnDelete();
        });

        if (Schema::hasColumn('soal_pspk', 'lampiran_pdf_path')) {
            $pairs = DB::table('soal_pspk')
                ->select('level_pspk_id', 'lampiran_pdf_path')
                ->whereNotNull('lampiran_pdf_path')
                ->whereIn('level_pspk_id', [3, 4])
                ->distinct()
                ->get();

            foreach ($pairs as $row) {
                $kasusId = DB::table('pspk_kasus_lampiran')->insertGetId([
                    'level_pspk_id' => $row->level_pspk_id,
                    'nama' => null,
                    'lampiran_pdf_path' => $row->lampiran_pdf_path,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('soal_pspk')
                    ->where('level_pspk_id', $row->level_pspk_id)
                    ->where('lampiran_pdf_path', $row->lampiran_pdf_path)
                    ->update(['kasus_lampiran_id' => $kasusId]);
            }

            Schema::table('soal_pspk', function (Blueprint $table) {
                $table->dropColumn('lampiran_pdf_path');
            });
        }
    }

    public function down(): void
    {
        Schema::table('soal_pspk', function (Blueprint $table) {
            $table->string('lampiran_pdf_path', 512)->nullable()->after('kunci_jawaban');
        });

        if (Schema::hasTable('pspk_kasus_lampiran')) {
            $kasusRows = DB::table('pspk_kasus_lampiran')->get();
            foreach ($kasusRows as $k) {
                DB::table('soal_pspk')
                    ->where('kasus_lampiran_id', $k->id)
                    ->update(['lampiran_pdf_path' => $k->lampiran_pdf_path]);
            }
        }

        Schema::table('soal_pspk', function (Blueprint $table) {
            $table->dropForeign(['kasus_lampiran_id']);
            $table->dropColumn('kasus_lampiran_id');
        });

        Schema::dropIfExists('pspk_kasus_lampiran');
    }
};
