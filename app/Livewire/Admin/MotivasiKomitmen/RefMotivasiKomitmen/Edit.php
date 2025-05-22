<?php

namespace App\Livewire\Admin\MotivasiKomitmen\RefMotivasiKomitmen;

use App\Http\Requests\RefMotivasiKomitmenRequest;
use App\Models\MotivasiKomitmen\RefMotivasiKomitmen;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Motivasi dan Komitmen'])]
class Edit extends Component
{
    public $indikator_nama;
    public $indikator_nomor;

    #[Locked]
    public $id;

    public function mount($id)
    {
        try {
            $data = RefMotivasiKomitmen::findOrFail($id);
            $this->id = $data->id;
            $this->indikator_nama = $data->indikator_nama;
            $this->indikator_nomor = $data->indikator_nomor;
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        return view('livewire.admin.motivasi-komitmen.referensi.edit');
    }

    protected function rules()
    {
        $request = new RefMotivasiKomitmenRequest();
        return $request->rules();
    }

    protected function messages()
    {
        $request = new RefMotivasiKomitmenRequest();
        return $request->messages();
    }

    public function save()
    {
        $this->validate();

        try {
            $data = RefMotivasiKomitmen::find($this->id);
            $old_data = $data->getOriginal();

            $check_duplicate = RefMotivasiKomitmen::where('indikator_nomor', '!=', $this->indikator_nomor)->get(['indikator_nomor']);
            foreach ($check_duplicate as $value) {
                if ($value->indikator_nomor == $data->indikator_nomor) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor indikator ' . $this->indikator_nomor . ' sudah ada!']);
                    return;
                }
            }

            $data->indikator_nama = $this->indikator_nama;
            $data->indikator_nomor = $this->indikator_nomor;
            $data->save();

            activity_log($data, 'update', 'ref-motivasi-komitmen', $old_data);

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil ubah data'
            ]);

            $this->redirect(route('admin.ref-motivasi-komitmen'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
