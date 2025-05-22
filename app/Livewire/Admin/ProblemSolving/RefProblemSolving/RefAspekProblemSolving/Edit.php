<?php

namespace App\Livewire\Admin\ProblemSolving\RefProblemSolving\RefAspekProblemSolving;

use App\Http\Requests\RefAspekProblemSolvingRequest;
use App\Models\ProblemSolving\RefAspekProblemSolving;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Aspek Problem Solving'])]
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
            $data = RefAspekProblemSolving::findOrFail($id);
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
        return view('livewire.admin.problem-solving.referensi.aspek.edit');
    }

    protected function rules()
    {
        $request = new RefAspekProblemSolvingRequest();
        return $request->rules();
    }

    protected function messages()
    {
        $request = new RefAspekProblemSolvingRequest();
        return $request->messages();
    }

    public function save()
    {
        $this->validate();

        try {
            $data = RefAspekProblemSolving::find($this->id);
            $old_data = $data->getOriginal();

            $check_duplicate = RefAspekProblemSolving::where('aspek_nomor', '!=', $this->aspek_nomor)->get(['aspek_nomor']);
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

            activity_log($data, 'update', 'ref-aspek-problem-solving', $old_data);

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil ubah data'
            ]);

            $this->redirect(route('admin.ref-aspek-problem-solving'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
