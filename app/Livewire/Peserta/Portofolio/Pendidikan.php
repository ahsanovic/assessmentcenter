<?php

namespace App\Livewire\Peserta\Portofolio;

use App\Models\RwPendidikan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Portofolio'])]
class Pendidikan extends Component
{
    public $selected_id;

    public function render()
    {
        $pendidikan = RwPendidikan::wherePesertaEvent(
            Auth::guard('peserta')->user()->id,
            Auth::guard('peserta')->user()->event_id
        )
            ->orderByDesc('thn_lulus')
            ->get();

        return view('livewire..peserta.portofolio._partials.pendidikan.index', [
            'pendidikan' => $pendidikan,
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
            RwPendidikan::find($this->selected_id)->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
