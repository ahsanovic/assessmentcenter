<?php

namespace App\Livewire\Admin\JawabanPeserta\LiterasiDigital;

use App\Services\LiterasiDigital\LiterasiDigitalButirService;
use Livewire\Attributes\Url;
use Livewire\Component;

class Analisa extends Component
{
    public int $eventId;

    #[Url(as: 'pilar')]
    public string $pilar = 'budaya';

    public string $filterKeputusan = 'semua';

    public function mount(int $eventId): void
    {
        $this->eventId = $eventId;
        $this->normalizePilar();
    }

    public function updatedPilar(): void
    {
        $this->normalizePilar();
        $this->filterKeputusan = 'semua';
    }

    public function setPilar(string $kode): void
    {
        $this->pilar = $kode;
        $this->normalizePilar();
        $this->filterKeputusan = 'semua';
    }

    public function setFilterKeputusan(string $key): void
    {
        if (! in_array($key, ['semua', 'layak', 'ditinjau', 'lemah'], true)) {
            return;
        }

        $this->filterKeputusan = $key;
    }

    public function render()
    {
        $bundle = app(LiterasiDigitalButirService::class)->analyzeEvent($this->eventId);
        $activePillar = $bundle['pillars'][$this->pilar] ?? $bundle['pillars']['budaya'];

        return view('livewire.admin.jawaban-peserta.literasi-digital.analisa', [
            'pillarTabs' => collect(LiterasiDigitalButirService::PILAR)
                ->map(fn (string $label, string $kode) => [
                    'kode' => $kode,
                    'label' => $bundle['pillars'][$kode]['label'] ?? $label,
                ])
                ->values()
                ->all(),
            'activePillar' => $activePillar,
            'stratified' => $bundle['stratified'],
            'optionLetters' => LiterasiDigitalButirService::LETTERS,
        ]);
    }

    private function normalizePilar(): void
    {
        if (! array_key_exists($this->pilar, LiterasiDigitalButirService::PILAR)) {
            $this->pilar = 'budaya';
        }
    }
}
