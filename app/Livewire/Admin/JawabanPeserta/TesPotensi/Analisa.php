<?php

namespace App\Livewire\Admin\JawabanPeserta\TesPotensi;

use App\Services\TesPotensi\TesPotensiJawabanService;
use Livewire\Attributes\Isolate;
use Livewire\Component;

#[Isolate]
class Analisa extends Component
{
    public int $eventId;

    public string $subtes;

    public function render()
    {
        $service = app(TesPotensiJawabanService::class);
        $bundle = $service->analyzeSubtest($this->eventId, $this->subtes);
        $definition = $service->definition($this->subtes);

        return view('livewire.admin.jawaban-peserta.tes-potensi.analisa', [
            'items' => $bundle['items'],
            'conclusion' => $bundle['conclusion'],
            'optionLetters' => $definition['letters'],
            'subtesLabel' => $definition['label'],
        ]);
    }
}
