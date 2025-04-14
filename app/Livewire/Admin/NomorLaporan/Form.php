<?php

namespace App\Livewire\Admin\NomorLaporan;

use App\Livewire\Forms\NomorLaporanForm;
use App\Models\Event;
use App\Models\NomorLaporan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Nomor Laporan Penilaian'])]
class Form extends Component
{   
    public NomorLaporanForm $form;
    public $isUpdate = false;

    #[Locked]
    public $id;
    
    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;
    
                $data = NomorLaporan::findOrFail($id);
                $this->id = $data->id;
                $this->form->event_id = $data->event_id;
                $this->form->nomor = $data->nomor;
                $this->form->tanggal = $data->tanggal;
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
    
    public function render()
    {
        $event = Event::pluck('nama_event', 'id');

        return view('livewire.admin.nomor-laporan.form', compact('event'));
    }

    public function save()
    {
        $this->validate();
        try {
            if ($this->isUpdate) {
                $data = NomorLaporan::findOrFail($this->id);
                $data->event_id = $this->form->event_id;
                $data->nomor = $this->form->nomor;
                $data->tanggal = $this->form->tanggal;
                $data->save();
                
                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);
                
                $this->redirect(route('admin.nomor-laporan'), true);
            } else {
                $tanggal = date('Y-m-d', strtotime($this->form->tanggal));
                $check_duplicate = NomorLaporan::where('nomor', $this->form->nomor)->where('tanggal', $tanggal)->exists();

                if ($check_duplicate) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor ' . $this->form->nomor . ' sudah ada!']);
                    return;
                }
    
                NomorLaporan::create([
                    'event_id' => $this->form->event_id,
                    'nomor' => $this->form->nomor,
                    'tanggal' => $this->form->tanggal,
                ]);
    
                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);
    
                $this->redirect(route('admin.nomor-laporan'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
