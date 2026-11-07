<?php

namespace Tests\Feature;

use App\Livewire\Admin\LiterasiDigital\RefPilar\Index;
use App\Models\LiterasiDigital\RefPilarLiterasiDigital;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Tests\TestCase;

class RefPilarLiterasiDigitalTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_bisa_menyimpan_deskripsi_pilar(): void
    {
        $admin = User::query()->where('role', 'admin')->where('is_active', 't')->first();
        $this->assertNotNull($admin);
        $this->actingAs($admin, 'admin');

        $etika = RefPilarLiterasiDigital::query()->where('kode', 'etika')->firstOrFail();
        $awal = $etika->deskripsi;

        $this->get(route('admin.ref-pilar-literasi-digital'))
            ->assertOk()
            ->assertSee('Referensi Pilar')
            ->assertSee('Deskripsi');

        Livewire::test(Index::class)
            ->call('edit', $etika->id)
            ->assertSet('nama', 'Etika Digital')
            ->assertSet('deskripsi', $awal)
            ->set('deskripsi', "  Narasi etika\nuntuk laporan  ")
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false)
            ->assertSee('Narasi etika');

        $this->assertSame("Narasi etika\nuntuk laporan", $etika->refresh()->deskripsi);

        Livewire::test(Index::class)
            ->call('edit', $etika->id)
            ->set('deskripsi', '   ')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertNull($etika->refresh()->deskripsi);

        Livewire::test(Index::class)
            ->call('edit', $etika->id)
            ->set('deskripsi', str_repeat('a', 5001))
            ->call('save')
            ->assertHasErrors(['deskripsi' => 'max']);
    }
}
