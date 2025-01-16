<?php

namespace App\Livewire\Peserta\Portofolio;

use App\Livewire\Forms\PendidikanRequestForm;
use App\Models\RefJenjangPendidikan;
use App\Models\RwPendidikan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Portofolio'])]
class PendidikanForm extends Component
{
    public PendidikanRequestForm $form;
    public $isUpdate = false;

    #[Locked]
    public $id;

    public function render()
    {
        $option_jenjang_pendidikan = RefJenjangPendidikan::pluck('jenjang', 'id');

        return view('livewire..peserta.portofolio._partials.pendidikan.form', [
            'option_jenjang_pendidikan' => $option_jenjang_pendidikan
        ]);
    }

    public function mount($id = null)
    {
        try {
            if ($id) {
                $this->isUpdate = true;

                $data = RwPendidikan::findOrFail($id);
                $this->form->jenjang_pendidikan_id = $data->jenjang_pendidikan_id;
                $this->form->nama_sekolah = $data->nama_sekolah;
                $this->form->thn_masuk = $data->thn_masuk;
                $this->form->thn_lulus = $data->thn_lulus;
                $this->form->jurusan = $data->jurusan;
                $this->form->ipk = $data->ipk;
            }
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isUpdate) {
                RwPendidikan::whereId($this->id)->update($this->validate());
                
                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil ubah data'
                ]);
                
                $this->redirect(route('peserta.pendidikan'), true);
            } else {
                RwPendidikan::create([
                    'event_id' => Auth::guard('peserta')->user()->event_id,
                    'peserta_id' => Auth::guard('peserta')->user()->id,
                    'jenjang_pendidikan_id' => $this->form->jenjang_pendidikan_id,
                    'nama_sekolah' => $this->form->nama_sekolah,
                    'thn_masuk' => $this->form->thn_masuk,
                    'thn_lulus' => $this->form->thn_lulus,
                    'jurusan' => $this->form->jurusan,
                    'ipk' => $this->form->ipk,
                ]);

                session()->flash('toast', [
                    'type' => 'success',
                    'message' => 'berhasil tambah data'
                ]);
    
                $this->redirect(route('peserta.pendidikan'), true);
            }
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
