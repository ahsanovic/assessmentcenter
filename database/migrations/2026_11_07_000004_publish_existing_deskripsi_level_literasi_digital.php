<?php

use App\Services\LiterasiDigital\DeskripsiLevelLiterasiDigitalService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        app(DeskripsiLevelLiterasiDigitalService::class)->publikasikanDraftAwal();
    }

    public function down(): void
    {
        // Terbitan awal boleh tetap ada. Menghapusnya ikut menghapus riwayat yang dibuat sesudahnya.
    }
};
