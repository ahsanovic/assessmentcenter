<?php

use App\Support\Migrations\ManagesMysqlConstraints;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use ManagesMysqlConstraints;

    public function up(): void
    {
        $this->dropForeignOnColumnIfExists('absensi_event', 'event_id');
        $this->dropIndexIfExists('absensi_event', 'absensi_event_event_id_sesi_unique');

        if (! $this->indexExists('absensi_event', 'absensi_event_event_id_tanggal_sesi_unique')) {
            Schema::table('absensi_event', function (Blueprint $table) {
                $table->unique(
                    ['event_id', 'tanggal', 'sesi'],
                    'absensi_event_event_id_tanggal_sesi_unique'
                );
            });
        }

        $this->addForeignEventIdIfMissing();
    }

    public function down(): void
    {
        $this->dropForeignOnColumnIfExists('absensi_event', 'event_id');
        $this->dropIndexIfExists('absensi_event', 'absensi_event_event_id_tanggal_sesi_unique');

        if (! $this->indexExists('absensi_event', 'absensi_event_event_id_sesi_unique')) {
            Schema::table('absensi_event', function (Blueprint $table) {
                $table->unique(['event_id', 'sesi'], 'absensi_event_event_id_sesi_unique');
            });
        }

        $this->addForeignEventIdIfMissing();
    }
};
