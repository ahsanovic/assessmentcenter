<?php

namespace App\Livewire\Peserta\Portofolio;

use App\Livewire\Forms\KarirRequestForm;
use App\Models\RwKarir;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Portofolio'])]
class KarirForm extends Component
{
    public KarirRequestForm $form;
    public $isUpdate = false;

    #[Locked]
    public $id;

    public function render()
    {
        return view('livewire..peserta.portofolio._partials.karir.form');
    }

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;

                $data = RwKarir::findOrFail($id);
                $this->form->bulan_mulai = $data->bulan_mulai;
                $this->form->tahun_mulai = $data->tahun_mulai;
                $this->form->bulan_selesai = $data->bulan_selesai;
                $this->form->tahun_selesai = $data->tahun_selesai;
                $this->form->instansi = $data->instansi;
                $this->form->jabatan = $data->jabatan;
                $this->form->uraian_tugas = $data->uraian_tugas;
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
                $selisih = $this->_getDifferenceYears(strtotime($this->form->tahun_selesai));
                if ($selisih > 5) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data yang dapat dimasukkan hanya 5 tahun terakhir']);
                    return;
                }

                if (strtotime($this->form->tahun_selesai) < strtotime($this->form->tahun_mulai)) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'tanggal selesai harus lebih besar dari tanggal mulai']);
                    return;
                }

                RwKarir::whereId($this->id)->update($this->validate());
                
                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);
                
                $this->redirect(route('peserta.karir'), true);
            } else {
                $selisih = $this->_getDifferenceYears(strtotime($this->form->tahun_selesai));
                if ($selisih > 5) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data yang dapat dimasukkan hanya 5 tahun terakhir']);
                    return;
                }

                if (strtotime($this->form->tahun_selesai) < strtotime($this->form->tahun_mulai)) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'tanggal selesai harus lebih besar dari tanggal mulai']);
                    return;
                }

                RwKarir::create([
                    'event_id' => Auth::guard('peserta')->user()->event_id,
                    'peserta_id' => Auth::guard('peserta')->user()->id,
                    'bulan_mulai' => $this->form->bulan_mulai,
                    'bulan_selesai' => $this->form->bulan_selesai,
                    'tahun_mulai' => $this->form->tahun_mulai,
                    'tahun_selesai' => $this->form->tahun_selesai,
                    'instansi' => $this->form->instansi,
                    'jabatan' => $this->form->jabatan,
                    'uraian_tugas' => $this->form->uraian_tugas,
                ]);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);
    
                $this->redirect(route('peserta.karir'), true);
            }
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
