<?php

namespace App\Livewire\Peserta\Portofolio;

use App\Livewire\Forms\PelatihanRequestForm;
use App\Models\RwPelatihan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Portofolio'])]
class PelatihanForm extends Component
{
    public PelatihanRequestForm $form;
    public $isUpdate = false;

    #[Locked]
    public $id;

    public function render()
    {
        return view('livewire..peserta.portofolio._partials.pelatihan.form');
    }

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;

                $data = RwPelatihan::findOrFail($id);
                $this->form->nama_institusi = $data->nama_institusi;
                $this->form->tgl_mulai = $data->tgl_mulai;
                $this->form->tgl_selesai = $data->tgl_selesai;
                $this->form->subjek_pelatihan = $data->subjek_pelatihan;
            }
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    private function _getDifferenceYears($value)
    {
        $thn_selesai = date('Y', strtotime($value));
        $thn_sekarang = date('Y');
        $selisih = $thn_sekarang - $thn_selesai;

        return $selisih;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isUpdate) {
                $selisih = $this->_getDifferenceYears(strtotime($this->form->tgl_selesai));
                if ($selisih > 5) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data yang dapat dimasukkan hanya 5 tahun terakhir']);
                    return;
                }
                
                if (strtotime($this->form->tgl_selesai) < strtotime($this->form->tgl_mulai)) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'tanggal selesai harus lebih besar dari tanggal mulai']);
                    return;
                }

                $data = RwPelatihan::whereId($this->id)->first();
                $data->nama_institusi = $this->form->nama_institusi;
                $data->tgl_mulai = $this->form->tgl_mulai;
                $data->tgl_selesai = $this->form->tgl_selesai;
                $data->subjek_pelatihan = $this->form->subjek_pelatihan;
                $data->save();

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);

                $this->redirect(route('peserta.pelatihan'), true);
            } else {
                $selisih = $this->_getDifferenceYears(strtotime($this->form->tgl_selesai));
                if ($selisih > 5) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data yang dapat dimasukkan hanya 5 tahun terakhir']);
                    return;
                }

                if (strtotime($this->form->tgl_selesai) < strtotime($this->form->tgl_mulai)) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'tanggal selesai harus lebih besar dari tanggal mulai']);
                    return;
                }

                RwPelatihan::create([
                    'event_id' => Auth::guard('peserta')->user()->event_id,
                    'peserta_id' => Auth::guard('peserta')->user()->id,
                    'nama_institusi' => $this->form->nama_institusi,
                    'tgl_mulai' => $this->form->tgl_mulai,
                    'tgl_selesai' => $this->form->tgl_selesai,
                    'subjek_pelatihan' => $this->form->subjek_pelatihan,
                ]);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);

                $this->redirect(route('peserta.pelatihan'), true);
            }
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
