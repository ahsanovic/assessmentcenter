<?php

namespace Tests\Feature;

use App\Livewire\Admin\EventGroup\Index;
use App\Models\EventGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class EventGroupRenameTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Auth::login(User::query()->firstOrFail());
    }

    public function test_nama_grup_bisa_diubah(): void
    {
        $group = EventGroup::create([
            'nama' => 'Grup lama '.uniqid(),
            'is_active' => 'true',
        ]);

        Livewire::test(Index::class)
            ->call('edit', $group->id)
            ->assertSet('nama', $group->nama)
            ->set('nama', '  Grup baru '.uniqid().'  ')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showModal', false);

        $group->refresh();
        $this->assertStringStartsWith('Grup baru ', $group->nama);
        $this->assertFalse(str_starts_with($group->nama, ' '));
    }

    public function test_nama_grup_yang_sudah_dipakai_ditolak(): void
    {
        $sudahAda = EventGroup::create([
            'nama' => 'Grup terpakai '.uniqid(),
            'is_active' => 'true',
        ]);
        $group = EventGroup::create([
            'nama' => 'Grup asal '.uniqid(),
            'is_active' => 'true',
        ]);

        Livewire::test(Index::class)
            ->call('edit', $group->id)
            ->set('nama', $sudahAda->nama)
            ->call('save')
            ->assertHasErrors(['nama' => 'unique']);

        $this->assertSame($group->nama, $group->fresh()->nama);
    }

    public function test_menyimpan_nama_yang_sama_tetap_berhasil(): void
    {
        $group = EventGroup::create([
            'nama' => 'Grup tetap '.uniqid(),
            'is_active' => 'true',
        ]);

        Livewire::test(Index::class)
            ->call('edit', $group->id)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame($group->nama, $group->fresh()->nama);
    }
}
