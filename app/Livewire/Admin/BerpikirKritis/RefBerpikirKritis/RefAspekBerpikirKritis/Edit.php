<?php

namespace App\Livewire\Admin\BerpikirKritis\RefBerpikirKritis\RefAspekBerpikirKritis;

use App\Http\Requests\RefAspekBerpikirKritisRequest;
use App\Models\BerpikirKritis\RefAspekBerpikirKritis;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Aspek Berpikir Kritis'])]
class Edit extends Component
{   
    public $aspek;
    public $aspek_nomor;
    public $indikator_nomor = [];

    #[Locked]
    public $id;
    
    public function mount($id)
    {
        try {
            $data = RefAspekBerpikirKritis::findOrFail($id);
            $this->id = $data->id;
            $this->aspek = $data->aspek;
            $this->aspek_nomor = $data->aspek_nomor;
            $this->indikator_nomor = explode(",", $data->indikator_nomor);
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        return view('livewire.admin.berpikir-kritis.referensi.aspek.edit');
    }

    protected function rules()
    {
        $request = new RefAspekBerpikirKritisRequest();
        return $request->rules();
    }

    protected function messages()
    {
        $request = new RefAspekBerpikirKritisRequest();
        return $request->messages();
    }

    public function save()
    {
        $this->validate();

        try {
            $data = RefAspekBerpikirKritis::find($this->id);

            $check_duplicate = RefAspekBerpikirKritis::where('aspek_nomor', '!=', $this->aspek_nomor)->get(['aspek_nomor']);
            foreach ($check_duplicate as $value) {
                if ($value->aspek_nomor == $data->aspek_nomor) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor aspek ' . $this->aspek_nomor . ' sudah ada!']);
                    return;
                }
            }

            $data->aspek = $this->aspek;
            $data->aspek_nomor = $this->aspek_nomor;
            $data->indikator_nomor = implode(',', $this->indikator_nomor);
            $data->save();

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil ubah data'
            ]);

            $this->redirect(route('admin.ref-aspek-berpikir-kritis'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
