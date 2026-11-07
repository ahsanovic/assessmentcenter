<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref_saran_pengembangan_literasi_digital', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilar_id')->constrained('ref_pilar_literasi_digital')->restrictOnDelete();
            $table->unsignedTinyInteger('level');
            $table->text('saran')->nullable();
            $table->timestamps();

            $table->unique(['pilar_id', 'level']);
        });

        Schema::create('ref_saran_literasi_digital_versi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilar_id')->constrained(
                table: 'ref_pilar_literasi_digital',
                indexName: 'saran_ld_versi_pilar_fk'
            )->restrictOnDelete();
            $table->unsignedTinyInteger('level');
            $table->text('saran');
            $table->timestamps();

            $table->index(['pilar_id', 'level'], 'saran_ld_versi_pilar_level');
        });

        Schema::create('ref_saran_literasi_digital_terbitan', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('ref_saran_literasi_digital_terbitan_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terbitan_id')->constrained(
                table: 'ref_saran_literasi_digital_terbitan',
                indexName: 'saran_ld_item_terbitan_fk'
            )->cascadeOnDelete();
            $table->foreignId('pilar_id')->constrained(
                table: 'ref_pilar_literasi_digital',
                indexName: 'saran_ld_item_pilar_fk'
            )->restrictOnDelete();
            $table->unsignedTinyInteger('level');
            $table->foreignId('versi_id')->nullable()->constrained(
                table: 'ref_saran_literasi_digital_versi',
                indexName: 'saran_ld_item_versi_fk'
            )->restrictOnDelete();
            $table->timestamps();

            $table->unique(['terbitan_id', 'pilar_id', 'level'], 'saran_ld_terbitan_item_unik');
        });

        Schema::table('event', function (Blueprint $table) {
            $table->foreignId('saran_literasi_digital_terbitan_id')
                ->nullable()
                ->constrained(
                    table: 'ref_saran_literasi_digital_terbitan',
                    indexName: 'event_saran_ld_terbitan_fk'
                )
                ->restrictOnDelete();
        });

        $now = now();
        $baris = [];

        foreach (DB::table('ref_pilar_literasi_digital')->orderBy('urutan')->orderBy('id')->get() as $pilar) {
            for ($level = 1; $level <= 5; $level++) {
                $baris[] = [
                    'pilar_id' => $pilar->id,
                    'level' => $level,
                    'saran' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if ($baris !== []) {
            DB::table('ref_saran_pengembangan_literasi_digital')->insert($baris);
        }
    }

    public function down(): void
    {
        Schema::table('event', function (Blueprint $table) {
            $table->dropForeign('event_saran_ld_terbitan_fk');
            $table->dropColumn('saran_literasi_digital_terbitan_id');
        });

        Schema::dropIfExists('ref_saran_literasi_digital_terbitan_item');
        Schema::dropIfExists('ref_saran_literasi_digital_terbitan');
        Schema::dropIfExists('ref_saran_literasi_digital_versi');
        Schema::dropIfExists('ref_saran_pengembangan_literasi_digital');
    }
};
