<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_group', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('periode')->nullable();
            $table->text('deskripsi')->nullable();
            $table->enum('is_active', ['true', 'false'])->default('true');
            $table->timestamps();
        });

        Schema::table('event', function (Blueprint $table) {
            $table->foreignId('event_group_id')
                ->nullable()
                ->after('nama_event')
                ->constrained('event_group')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('event', function (Blueprint $table) {
            $table->dropConstrainedForeignId('event_group_id');
        });

        Schema::dropIfExists('event_group');
    }
};
