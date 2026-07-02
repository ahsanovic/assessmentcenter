<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref_pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nip', 18)->unique();
            $table->string('qrcode_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_pegawai');
    }
};
