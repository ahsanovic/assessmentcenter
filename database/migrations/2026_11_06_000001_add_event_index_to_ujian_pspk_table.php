<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ujian_pspk')) {
            return;
        }

        if (! Schema::hasIndex('ujian_pspk', ['event_id', 'updated_at'])) {
            Schema::table('ujian_pspk', function (Blueprint $table) {
                $table->index(['event_id', 'updated_at']);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('ujian_pspk')) {
            return;
        }

        if (Schema::hasIndex('ujian_pspk', ['event_id', 'updated_at'])) {
            Schema::table('ujian_pspk', function (Blueprint $table) {
                $table->dropIndex(['event_id', 'updated_at']);
            });
        }
    }
};
