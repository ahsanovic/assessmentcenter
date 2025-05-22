<?php

namespace App\Livewire\Admin\MotivasiKomitmen\RefMotivasiKomitmen;

use App\Http\Requests\RefMotivasiKomitmenRequest;
use App\Models\MotivasiKomitmen\RefMotivasiKomitmen;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Motivasi dan Komitmen'])]
class Create extends Component
{
    public $indikator_nama;
    public $indikator_nomor;

    public function render()
    {
        return view('livewire.admin.motivasi-komitmen.referensi.create');
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
            $check_duplicate = RefMotivasiKomitmen::where('indikator_nomor', $this->indikator_nomor)->exists();
            if ($check_duplicate) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor indikator ' . $this->indikator_nomor . ' sudah ada!']);
                return;
            }

            $data = new RefMotivasiKomitmen();
            $data->indikator_nama = $this->indikator_nama;
            $data->indikator_nomor = $this->indikator_nomor;
            $data->save();

            activity_log($data, 'create', 'ref-motivasi-komitmen');

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil tambah data'
            ]);

            $this->redirect(route('admin.ref-motivasi-komitmen'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
