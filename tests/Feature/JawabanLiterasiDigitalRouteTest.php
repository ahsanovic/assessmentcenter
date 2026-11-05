<?php

namespace Tests\Feature;

use Tests\TestCase;

class JawabanLiterasiDigitalRouteTest extends TestCase
{
    public function test_tamu_diarahkan_dan_id_event_non_angka_ditolak(): void
    {
        $this->get('/bkdac/jawaban-peserta/literasi-digital')
            ->assertRedirect();

        $this->get('/bkdac/jawaban-peserta/literasi-digital/bukan-angka')
            ->assertNotFound();
    }
}
