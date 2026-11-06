<?php

namespace Tests\Feature;

use Tests\TestCase;

class JawabanTesPotensiRouteTest extends TestCase
{
    public function test_tamu_diarahkan_dan_id_event_non_angka_ditolak(): void
    {
        $this->get('/bkdac/jawaban-peserta/tes-potensi')
            ->assertRedirect();

        $this->get('/bkdac/jawaban-peserta/tes-potensi/bukan-angka')
            ->assertNotFound();
    }
}
