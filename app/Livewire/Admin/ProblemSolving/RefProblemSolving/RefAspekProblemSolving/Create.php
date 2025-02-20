<?php

namespace App\Livewire\Admin\ProblemSolving\RefProblemSolving\RefAspekProblemSolving;

use App\Http\Requests\RefAspekProblemSolvingRequest;
use App\Models\ProblemSolving\RefAspekProblemSolving;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Aspek Problem Solving'])]
class Create extends Component
{   
    public $aspek;
    public $aspek_nomor;
    public $indikator_nomor = [];
    
    public function mount()
    {
        
    }

    public function render()
    {
        return view('livewire.admin.problem-solving.referensi.aspek.create');
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
            $check_duplicate = RefAspekProblemSolving::where('aspek_nomor', $this->aspek_nomor)->exists();
            if ($check_duplicate) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor aspek ' . $this->aspek_nomor . ' sudah ada!']);
                return;
            }

            $data = new RefAspekProblemSolving();
            $data->aspek = $this->aspek;
            $data->aspek_nomor = $this->aspek_nomor;
            $data->indikator_nomor = implode(',', $this->indikator_nomor);
            $data->save();

            session()->flash('toast', [
                'type' => 'success',
                'message' => 'berhasil tambah data'
            ]);

            $this->redirect(route('admin.ref-aspek-problem-solving'), true);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
