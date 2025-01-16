<?php

namespace App\Livewire\Peserta\Portofolio;

use App\Models\RwPelatihan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Portofolio'])]
class Pelatihan extends Component
{
    public $selected_id;

    public function render()
    {
        $pelatihan = RwPelatihan::wherePesertaEvent(
            Auth::guard('peserta')->user()->id,
            Auth::guard('peserta')->user()->event_id
        )
            ->orderByRaw('YEAR(tgl_selesai) IS NULL, YEAR(tgl_selesai) DESC')
            ->get();

        return view('livewire..peserta.portofolio._partials.pelatihan.index', [
            'pelatihan' => $pelatihan,
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
            RwPelatihan::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
