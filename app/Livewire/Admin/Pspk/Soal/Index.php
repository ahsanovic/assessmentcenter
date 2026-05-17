<?php

namespace App\Livewire\Admin\Pspk\Soal;

use App\Livewire\Forms\SoalPspkForm;
use App\Models\Pspk\PspkKasusLampiran;
use App\Models\Pspk\RefLevelPspk;
use App\Models\Pspk\SoalPspk;
use App\Models\RefAspekPspk;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'PSPK'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;

    public $level_pspk_id;

    public $aspek;

    public $jenis_soal;

    public $jenis_soal_options = [
        1 => 'Ankas',
        2 => 'SJT',
    ];

    public $showModal = false;

    public $isUpdate = false;

    public SoalPspkForm $form;

    public $editId;

    #[Url(as: 'q')]
    public ?string $search = '';

    #[Url(as: 'level')]
    public ?int $filter_level_pspk = null;

    #[Url(as: 'aspek')]
    public ?int $filter_aspek = null;

    #[Url(as: 'jenis_soal')]
    public ?int $filter_jenis_soal = null;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterLevelPspk()
    {
        $this->resetPage();
    }

    public function updatedFilterJenisSoal()
    {
        $this->resetPage();
    }

    public function updatedFilterAspek()
    {
        $this->resetPage();
    }

    public function updatedFormLevelPspkId(mixed $value): void
    {
        if (! in_array((int) $value, [3, 4], true)) {
            $this->form->jenis_soal = null;
            $this->form->kasus_lampiran_id = null;
        }
    }

    public function updatedFormJenisSoal(mixed $value): void
    {
        if ((int) $value !== SoalPspk::JENIS_ANKAS) {
            $this->form->kasus_lampiran_id = null;
        }
    }

    public function render()
    {
        $aspek_options = RefAspekPspk::pluck('nama_aspek', 'id');
        $level_pspk_options = RefLevelPspk::pluck('level_pspk', 'id');

        $kasus_lampiran_options = collect();
        if ($this->form->perluPaketKasusPdf()) {
            $kasus_lampiran_options = PspkKasusLampiran::query()
                ->where('level_pspk_id', $this->form->level_pspk_id)
                ->orderBy('nama')
                ->orderBy('id')
                ->get()
                ->mapWithKeys(fn (PspkKasusLampiran $k) => [
                    $k->id => $k->nama ? $k->nama : 'Paket #'.$k->id,
                ]);
        }

        $data = SoalPspk::when($this->search, function ($query) {
            $query->where('soal', 'like', '%'.$this->search.'%');
        })
            ->when($this->filter_level_pspk, function ($query) {
                $query->where('level_pspk_id', $this->filter_level_pspk);
            })
            ->when($this->filter_aspek, function ($query) {
                $query->where('aspek_id', $this->filter_aspek);
            })
            ->when($this->filter_jenis_soal, function ($query) {
                $query->where('jenis_soal', $this->filter_jenis_soal);
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.pspk.soal.index', compact(
            'data',
            'aspek_options',
            'level_pspk_options',
            'kasus_lampiran_options'
        ));
    }

    public function resetFilters()
    {
        $this->resetPage();
        $this->filter_level_pspk = null;
        $this->filter_aspek = null;
        $this->filter_jenis_soal = null;
        $this->search = '';
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->form->reset();
        $this->form->editing = false;
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
            $data = SoalPspk::findOrFail($id);
            $this->editId = $data->id;
            $this->form->level_pspk_id = $data->level_pspk_id;
            $this->form->jenis_soal = in_array((int) $data->level_pspk_id, [3, 4], true)
                ? $data->jenis_soal
                : null;
            $this->form->aspek = $data->aspek_id;
            $this->form->soal = $data->soal;
            $this->form->opsi_a = $data->opsi_a;
            $this->form->poin_opsi_a = $data->poin_opsi_a;
            $this->form->opsi_b = $data->opsi_b;
            $this->form->poin_opsi_b = $data->poin_opsi_b;
            $this->form->opsi_c = $data->opsi_c;
            $this->form->poin_opsi_c = $data->poin_opsi_c;
            $this->form->opsi_d = $data->opsi_d;
            $this->form->poin_opsi_d = $data->poin_opsi_d;
            $this->form->opsi_e = $data->opsi_e;
            $this->form->poin_opsi_e = $data->poin_opsi_e;
            $this->form->kunci_jawaban = $data->kunci_jawaban;
            $this->form->kasus_lampiran_id = $data->kasus_lampiran_id;
            $this->form->editing = true;
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
            $kasusId = $this->form->perluPaketKasusPdf()
                ? $this->form->kasus_lampiran_id
                : null;

            $jenisDisimpan = in_array((int) $this->form->level_pspk_id, [3, 4], true)
                ? $this->form->jenis_soal
                : null;

            if ($this->isUpdate) {
                $data = SoalPspk::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $data->level_pspk_id = $this->form->level_pspk_id;
                $data->jenis_soal = $jenisDisimpan;
                $data->aspek_id = $this->form->aspek;
                $data->soal = $this->form->soal;
                $data->opsi_a = $this->form->opsi_a;
                $data->poin_opsi_a = $this->form->poin_opsi_a;
                $data->opsi_b = $this->form->opsi_b;
                $data->poin_opsi_b = $this->form->poin_opsi_b;
                $data->opsi_c = $this->form->opsi_c;
                $data->poin_opsi_c = $this->form->poin_opsi_c;
                $data->opsi_d = $this->form->opsi_d;
                $data->poin_opsi_d = $this->form->poin_opsi_d;
                $data->opsi_e = $this->form->opsi_e;
                $data->poin_opsi_e = $this->form->poin_opsi_e;
                $data->kunci_jawaban = $this->form->kunci_jawaban;
                $data->kasus_lampiran_id = $kasusId;
                $data->save();

                activity_log($data, 'update', 'soal-pspk', $old_data);
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $model = SoalPspk::create([
                    'level_pspk_id' => $this->form->level_pspk_id,
                    'jenis_soal' => $jenisDisimpan,
                    'aspek_id' => $this->form->aspek,
                    'soal' => $this->form->soal,
                    'opsi_a' => $this->form->opsi_a,
                    'poin_opsi_a' => $this->form->poin_opsi_a,
                    'opsi_b' => $this->form->opsi_b,
                    'poin_opsi_b' => $this->form->poin_opsi_b,
                    'opsi_c' => $this->form->opsi_c,
                    'poin_opsi_c' => $this->form->poin_opsi_c,
                    'opsi_d' => $this->form->opsi_d,
                    'poin_opsi_d' => $this->form->poin_opsi_d,
                    'opsi_e' => $this->form->opsi_e,
                    'poin_opsi_e' => $this->form->poin_opsi_e,
                    'kunci_jawaban' => $this->form->kunci_jawaban,
                    'kasus_lampiran_id' => $kasusId,
                ]);

                activity_log($model, 'create', 'soal-pspk');
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }
            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
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
            $data = SoalPspk::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'soal-pspk', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
