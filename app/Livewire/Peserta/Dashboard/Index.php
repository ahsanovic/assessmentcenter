<?php

namespace App\Livewire\Peserta\Dashboard;

use App\Services\PesertaParticipationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Dashboard'])]
class Index extends Component
{
    public function mount(PesertaParticipationService $participationService): void
    {
        $participationService->clearActivePesertaContext();
    }

    public function startTest(int $pesertaId, PesertaParticipationService $participationService)
    {
        $participation = $participationService->getCurrentGroupParticipations()->firstWhere('id', $pesertaId);

        if (! $participation || ! $participationService->canStartTest($participation)) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Tes tidak dapat dimulai.']);
            return;
        }

        $participationService->setActivePesertaContext($pesertaId);

        return $this->redirect(
            $participationService->routeForMetodeTes($participation->event->metode_tes_id),
            navigate: true
        );
    }

    public function render(PesertaParticipationService $participationService)
    {
        $participations = $participationService->getCurrentGroupParticipations();
        $groupLabel = $participations->first()?->event->eventGroup?->nama
            ?? $participations->first()?->event->nama_event;

        return view('livewire.peserta.dashboard.index', [
            'participations' => $participations,
            'groupLabel' => $groupLabel,
            'participationService' => $participationService,
        ]);
    }
}
