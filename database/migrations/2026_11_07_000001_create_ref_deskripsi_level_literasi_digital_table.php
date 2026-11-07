<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref_deskripsi_level_literasi_digital', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilar_id')->constrained('ref_pilar_literasi_digital')->restrictOnDelete();
            $table->unsignedTinyInteger('level');
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->unique(['pilar_id', 'level']);
        });

        $now = now();
        $baris = [];

        foreach (DB::table('ref_pilar_literasi_digital')->orderBy('urutan')->orderBy('id')->get() as $pilar) {
            for ($level = 1; $level <= 5; $level++) {
                $baris[] = [
                    'pilar_id' => $pilar->id,
                    'level' => $level,
                    'deskripsi' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if ($baris !== []) {
            DB::table('ref_deskripsi_level_literasi_digital')->insert($baris);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_deskripsi_level_literasi_digital');
    }
};
