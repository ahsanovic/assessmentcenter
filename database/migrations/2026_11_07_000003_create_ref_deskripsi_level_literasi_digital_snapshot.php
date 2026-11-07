<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref_deskripsi_literasi_digital_versi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilar_id')->constrained(
                table: 'ref_pilar_literasi_digital',
                indexName: 'deskripsi_ld_versi_pilar_fk'
            )->restrictOnDelete();
            $table->unsignedTinyInteger('level');
            $table->text('deskripsi');
            $table->timestamps();

            $table->index(['pilar_id', 'level'], 'deskripsi_ld_versi_pilar_level');
        });

        Schema::create('ref_deskripsi_literasi_digital_terbitan', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('ref_deskripsi_literasi_digital_terbitan_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terbitan_id')->constrained(
                table: 'ref_deskripsi_literasi_digital_terbitan',
                indexName: 'deskripsi_ld_item_terbitan_fk'
            )->cascadeOnDelete();
            $table->foreignId('pilar_id')->constrained(
                table: 'ref_pilar_literasi_digital',
                indexName: 'deskripsi_ld_item_pilar_fk'
            )->restrictOnDelete();
            $table->unsignedTinyInteger('level');
            $table->foreignId('versi_id')->nullable()->constrained(
                table: 'ref_deskripsi_literasi_digital_versi',
                indexName: 'deskripsi_ld_item_versi_fk'
            )->restrictOnDelete();
            $table->timestamps();

            $table->unique(['terbitan_id', 'pilar_id', 'level'], 'deskripsi_ld_terbitan_item_unik');
        });

        Schema::table('event', function (Blueprint $table) {
            $table->foreignId('deskripsi_literasi_digital_terbitan_id')
                ->nullable()
                ->constrained(
                    table: 'ref_deskripsi_literasi_digital_terbitan',
                    indexName: 'event_deskripsi_ld_terbitan_fk'
                )
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('event', function (Blueprint $table) {
            $table->dropForeign('event_deskripsi_ld_terbitan_fk');
            $table->dropColumn('deskripsi_literasi_digital_terbitan_id');
        });

        Schema::dropIfExists('ref_deskripsi_literasi_digital_terbitan_item');
        Schema::dropIfExists('ref_deskripsi_literasi_digital_terbitan');
        Schema::dropIfExists('ref_deskripsi_literasi_digital_versi');
    }
};
