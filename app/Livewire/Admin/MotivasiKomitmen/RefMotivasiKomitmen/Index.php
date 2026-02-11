<?php

namespace App\Livewire\Admin\MotivasiKomitmen\RefMotivasiKomitmen;

use App\Http\Requests\RefMotivasiKomitmenRequest;
use App\Models\MotivasiKomitmen\RefMotivasiKomitmen;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Motivasi dan Komitmen'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $showModal = false;
    public $isUpdate = false;
    public $indikator_nama;
    public $indikator_nomor;
    public $editId;

    public function render()
    {
        $data = RefMotivasiKomitmen::paginate(10);

        return view('livewire.admin.motivasi-komitmen.referensi.index', compact('data'));
    }

    public function deleteConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    #[On('delete')]
    public function destroy()
    {
        try {
            $data = RefMotivasiKomitmen::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'ref-motivasi-komitmen', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['indikator_nama', 'indikator_nomor']);
        $this->isUpdate = false;
        $this->editId = null;
        $this->showModal = true;
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->reset(['indikator_nama', 'indikator_nomor']);
        $this->isUpdate = false;
        $this->editId = null;
    }

    public function edit($id)
    {
        try {
            $data = RefMotivasiKomitmen::findOrFail($id);
            $this->editId = $data->id;
            $this->indikator_nama = $data->indikator_nama;
            $this->indikator_nomor = $data->indikator_nomor;
            $this->isUpdate = true;
            $this->showModal = true;
            $this->resetValidation();
            $this->dispatch('modalOpened');
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
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
            if ($this->isUpdate) {
                $data = RefMotivasiKomitmen::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $data->indikator_nama = $this->indikator_nama;
                $data->indikator_nomor = $this->indikator_nomor;
                $data->save();

                activity_log($data, 'update', 'ref-motivasi-komitmen', $old_data);
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $check_duplicate = RefMotivasiKomitmen::where('indikator_nomor', $this->indikator_nomor)->exists();
                if ($check_duplicate) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor indikator ' . $this->indikator_nomor . ' sudah ada!']);
                    return;
                }

                $model = new RefMotivasiKomitmen();
                $model->indikator_nama = $this->indikator_nama;
                $model->indikator_nomor = $this->indikator_nomor;
                $model->save();

                activity_log($model, 'create', 'ref-motivasi-komitmen');
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }
            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
