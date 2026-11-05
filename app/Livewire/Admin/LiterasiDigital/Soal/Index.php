<?php

namespace App\Livewire\Admin\LiterasiDigital\Soal;

use App\Livewire\Forms\SoalLiterasiDigitalForm;
use App\Models\LiterasiDigital\JawabanLiterasiDigital;
use App\Models\LiterasiDigital\RefPilarLiterasiDigital;
use App\Models\LiterasiDigital\SoalLiterasiDigital;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Soal Literasi Digital'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;

    public $pilar_id;

    public $showModal = false;

    public $isUpdate = false;

    public SoalLiterasiDigitalForm $form;

    public $editId;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPilarId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = SoalLiterasiDigital::with('pilar')
            ->when($this->search, function ($query) {
                $query->where('soal', 'like', '%'.$this->search.'%');
            })
            ->when($this->pilar_id, function ($query) {
                $query->where('pilar_id', $this->pilar_id);
            })
            ->orderByDesc('id')
            ->paginate(10);

        $pilar = RefPilarLiterasiDigital::orderBy('urutan')->get();

        return view('livewire.admin.literasi-digital.soal.index', compact('data', 'pilar'));
    }

    public function resetFilters()
    {
        $this->reset(['search', 'pilar_id']);
        $this->resetPage();
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
            if (JawabanLiterasiDigital::where('soal_id', $this->selected_id)->exists()) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'soal sudah dipakai peserta']);

                return;
            }

            $data = SoalLiterasiDigital::findOrFail($this->selected_id);
            $old = $data->getOriginal();
            activity_log($data, 'delete', 'soal-literasi-digital', $old);
            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->form->reset();
        $this->form->is_active = 'true';
        $this->isUpdate = false;
        $this->editId = null;
        $this->showModal = true;
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->form->reset();
        $this->isUpdate = false;
        $this->editId = null;
    }

    public function edit($id)
    {
        try {
            $data = SoalLiterasiDigital::findOrFail($id);
            $this->editId = $data->id;
            $this->form->pilar_id = $data->pilar_id;
            $this->form->soal = $data->soal;
            $this->form->opsi_a = $data->opsi_a;
            $this->form->opsi_b = $data->opsi_b;
            $this->form->opsi_c = $data->opsi_c;
            $this->form->opsi_d = $data->opsi_d;
            $this->form->opsi_e = $data->opsi_e;
            $this->form->kunci_jawaban = $data->kunci_jawaban;
            $this->form->is_active = $data->is_active;
            $this->isUpdate = true;
            $this->showModal = true;
            $this->resetValidation();
            $this->dispatch('modalOpened');
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $payload = [
                'pilar_id' => $this->form->pilar_id,
                'soal' => $this->form->soal,
                'opsi_a' => $this->form->opsi_a,
                'opsi_b' => $this->form->opsi_b,
                'opsi_c' => $this->form->opsi_c,
                'opsi_d' => $this->form->opsi_d,
                'opsi_e' => $this->form->opsi_e,
                'kunci_jawaban' => strtoupper($this->form->kunci_jawaban),
                'is_active' => $this->form->is_active,
            ];

            if ($this->isUpdate) {
                $data = SoalLiterasiDigital::findOrFail($this->editId);
                $dipakai = JawabanLiterasiDigital::where('soal_id', $data->id)->exists();

                if ($dipakai && ((int) $data->pilar_id !== (int) $payload['pilar_id'] || strcasecmp((string) $data->kunci_jawaban, $payload['kunci_jawaban']) !== 0)) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'soal sudah dipakai peserta. pilar dan kunci tidak dapat diubah']);

                    return;
                }

                $old = $data->getOriginal();
                $data->fill($payload);
                $data->save();
                activity_log($data, 'update', 'soal-literasi-digital', $old);
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $model = SoalLiterasiDigital::create($payload);
                activity_log($model, 'create', 'soal-literasi-digital');
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }
}
