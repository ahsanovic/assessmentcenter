<?php

namespace App\Livewire\Admin\JawabanPeserta\Pspk;

use App\Services\Pspk\PspkButirService;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Lazy]
class Analisa extends Component
{
    public int $eventId;

    public int $levelPspk;

    #[Url(as: 'aspek')]
    public string $aspek = '';

    public string $filterKeputusan = 'semua';

    public function mount(int $eventId, int $levelPspk): void
    {
        $this->eventId = $eventId;
        $this->levelPspk = in_array($levelPspk, [1, 2, 3, 4], true) ? $levelPspk : 1;
    }

    public function setAspek(string $kode): void
    {
        $this->aspek = $kode;
        $this->filterKeputusan = 'semua';
    }

    public function setFilterKeputusan(string $key): void
    {
        if (! in_array($key, ['semua', 'layak', 'ditinjau', 'lemah'], true)) {
            return;
        }

        $this->filterKeputusan = $key;
    }

    public function placeholder()
    {
        return view('livewire.admin.jawaban-peserta.pspk.analisa-placeholder');
    }

    public function render()
    {
        $bundle = app(PspkButirService::class)->analyzeEvent($this->eventId, $this->levelPspk);
        $activeKode = array_key_exists($this->aspek, $bundle['aspek'])
            ? $this->aspek
            : (string) array_key_first($bundle['aspek']);

        return view('livewire.admin.jawaban-peserta.pspk.analisa', [
            'aspekTabs' => collect($bundle['aspek'])
                ->map(fn (array $aspek, string $kode) => [
                    'kode' => $kode,
                    'label' => $aspek['label'] ?? $kode,
                ])
                ->values()
                ->all(),
            'activeKode' => $activeKode,
            'activeAspek' => $activeKode !== '' ? $bundle['aspek'][$activeKode] : null,
            'stratified' => $bundle['stratified'],
            'optionLetters' => PspkButirService::LETTERS,
            'skorModel' => $bundle['skor_model'],
            'showJenis' => $bundle['show_jenis'],
        ]);
    }
}
