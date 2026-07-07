<?php

use Database\Migrations\Concerns\ManagesMysqlConstraints;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use ManagesMysqlConstraints;

    public function up(): void
    {
        $this->dropForeignOnColumnIfExists('absensi_event', 'event_id');

        if (Schema::hasColumn('absensi_event', 'assessor_id')) {
            $this->dropIndexIfExists('absensi_event', 'absensi_event_event_id_assessor_id_unique');

            Schema::table('absensi_event', function (Blueprint $table) {
                $table->dropColumn('assessor_id');
            });
        }

        $this->dropIndexIfExists('absensi_event', 'absensi_event_event_id_unique');

        Schema::table('absensi_event', function (Blueprint $table) {
            if (! Schema::hasColumn('absensi_event', 'peserta_dari')) {
                $table->unsignedSmallInteger('peserta_dari')->nullable()->after('sesi');
            }

            if (! Schema::hasColumn('absensi_event', 'peserta_sampai')) {
                $table->unsignedSmallInteger('peserta_sampai')->nullable()->after('peserta_dari');
            }
        });

        if (! $this->indexExists('absensi_event', 'absensi_event_event_id_sesi_unique')) {
            Schema::table('absensi_event', function (Blueprint $table) {
                $table->unique(['event_id', 'sesi'], 'absensi_event_event_id_sesi_unique');
            });
        }

        $this->addForeignEventIdIfMissing();
    }

    public function down(): void
    {
        $this->dropForeignOnColumnIfExists('absensi_event', 'event_id');
        $this->dropIndexIfExists('absensi_event', 'absensi_event_event_id_sesi_unique');

        Schema::table('absensi_event', function (Blueprint $table) {
            if (Schema::hasColumn('absensi_event', 'peserta_dari')) {
                $table->dropColumn('peserta_dari');
            }

            if (Schema::hasColumn('absensi_event', 'peserta_sampai')) {
                $table->dropColumn('peserta_sampai');
            }
        });

        if (! Schema::hasColumn('absensi_event', 'assessor_id')) {
            Schema::table('absensi_event', function (Blueprint $table) {
                $table->unsignedBigInteger('assessor_id')->default(0)->after('event_id');
            });
        }

        if (! $this->indexExists('absensi_event', 'absensi_event_event_id_assessor_id_unique')) {
            Schema::table('absensi_event', function (Blueprint $table) {
                $table->unique(['event_id', 'assessor_id'], 'absensi_event_event_id_assessor_id_unique');
            });
        }

        $this->addForeignEventIdIfMissing();
    }
};
