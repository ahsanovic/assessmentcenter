<?php

namespace App\Livewire\Admin\DataTes\TesCakapDigital\TesSelesai;

use App\Models\CakapDigital\HasilCakapDigital;
use App\Models\Event;
use App\Models\Peserta;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Tes Selesai'])]
class ShowPeserta extends Component
{
    use WithPagination;

    public $event;
    public $id_event;
    public $selected_id;
    public $tanggal_tes;

    #[Url(as: 'q')]
    public ?string $search =  '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'tanggal_tes']);
        $this->resetPage();
        $this->render();
    }

    public function mount($idEvent)
    {
        $this->id_event = $idEvent;
        $this->event = Event::with(['pesertaTesCakapDigital'])->findOrFail($this->id_event);
    }

    public function render()
    {
        $data = Peserta::join('hasil_cakap_digital', 'hasil_cakap_digital.peserta_id', '=', 'peserta.id')
            ->join('ujian_cakap_digital', 'ujian_cakap_digital.peserta_id', '=', 'peserta.id')
            ->whereIn('peserta.id', $this->event->pesertaIdTesCakapDigital->pluck('peserta_id'))
            ->where('hasil_cakap_digital.event_id', $this->id_event)
            ->where('ujian_cakap_digital.event_id', $this->id_event)
            ->select(
                'peserta.*',
                'hasil_cakap_digital.id as hasil_cakap_digital_id',
                'hasil_cakap_digital.created_at as waktu_selesai',
                'ujian_cakap_digital.created_at as waktu_mulai',
                'is_finished'
            )
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nip', 'like', '%' . $this->search . '%')
                        ->orWhere('nik', 'like', '%' . $this->search . '%')
                        ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                        ->orWhere('instansi', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->tanggal_tes, function ($query) {
                $tanggal_tes = date('Y-m-d', strtotime($this->tanggal_tes));
                $query->whereDate('test_started_at', $tanggal_tes);
            })
            ->paginate(10);

        return view('livewire.admin.data-tes.tes-cakap-digital.tes-selesai.show-peserta', [
            'data' => $data
        ]);
    }

    public function deleteConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    #[On('delete')]
    public function destroy()
    {
        try {
            HasilCakapDigital::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
