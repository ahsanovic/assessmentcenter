<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ujian_pspk', function (Blueprint $table) {
            if (! Schema::hasColumn('ujian_pspk', 'pspk_lv34_entered_sjt_at')) {
                $table->timestamp('pspk_lv34_entered_sjt_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ujian_pspk', function (Blueprint $table) {
            if (Schema::hasColumn('ujian_pspk', 'pspk_lv34_entered_sjt_at')) {
                $table->dropColumn('pspk_lv34_entered_sjt_at');
            }
        });
    }
};
