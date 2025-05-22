<?php

namespace App\Livewire\Admin\BerpikirKritis\RefBerpikirKritis\RefAspekBerpikirKritis;

use App\Http\Requests\RefAspekBerpikirKritisRequest;
use App\Models\BerpikirKritis\RefAspekBerpikirKritis;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Aspek Berpikir Kritis'])]
class Create extends Component
{
    public $aspek;
    public $aspek_nomor;
    public $indikator_nomor = [];

    public function render()
    {
        return view('livewire.admin.berpikir-kritis.referensi.aspek.create');
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
            $check_duplicate = RefAspekBerpikirKritis::where('aspek_nomor', $this->aspek_nomor)->exists();
            if ($check_duplicate) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor aspek ' . $this->aspek_nomor . ' sudah ada!']);
                return;
            }

            $data = new RefAspekBerpikirKritis();
            $data->aspek = $this->aspek;
            $data->aspek_nomor = $this->aspek_nomor;
            $data->indikator_nomor = implode(',', $this->indikator_nomor);
            $data->save();

            activity_log($data, 'create', 'ref-aspek-berpikir-kritis');

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil tambah data'
            ]);

            $this->redirect(route('admin.ref-aspek-berpikir-kritis'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
