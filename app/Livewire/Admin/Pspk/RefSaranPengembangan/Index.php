<?php

namespace App\Livewire\Admin\Pspk\RefSaranPengembangan;

use App\Livewire\Forms\SaranPengembanganPspkForm;
use App\Models\Pspk\RefLevelPspk;
use App\Models\Pspk\RefSaranPengembangan;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Referensi Saran Pengembangan'])]
class Index extends Component
{
    use WithPagination;

    /** Kolom teks saran per kode aspek PSPK. */
    public const SARAN_COLUMNS = ['int', 'ks', 'kom', 'oph', 'pp', 'pdol', 'mp', 'pk', 'pb'];

    /** Label singkat tampilan tabel / form. */
    public const SARAN_LABELS = [
        'int' => 'INT',
        'ks' => 'KS',
        'kom' => 'KOM',
        'oph' => 'OPH',
        'pp' => 'PP',
        'pdol' => 'PDOL',
        'mp' => 'MP',
        'pk' => 'PK',
        'pb' => 'PB',
    ];

    public $selected_id;

    #[Url(as: 'level')]
    public ?int $filter_level_pspk = null;

    public $showModal = false;

    public $isUpdate = false;

    public SaranPengembanganPspkForm $form;

    public $editId;

    #[Url(as: 'q')]
    public ?string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterLevelPspk(): void
    {
        $this->resetPage();
    }

    public function getLevelPspkOptions()
    {
        return RefLevelPspk::pluck('level_pspk', 'id');
    }

    public function render()
    {
        $term = '%' . ($this->search ?? '') . '%';
        $data = RefSaranPengembangan::query()
            ->with('levelPspk')
            ->when($this->filter_level_pspk, function ($query) {
                $query->where('level_pspk_id', $this->filter_level_pspk);
            })
            ->when($this->search !== '' && $this->search !== null, function ($query) use ($term) {
                $query->where(function ($q) use ($term) {
                    foreach (self::SARAN_COLUMNS as $col) {
                        $q->orWhere($col, 'like', $term);
                    }
                });
            })
            ->orderByDesc('id')
            ->paginate(10);

        $level_pspk_options = $this->getLevelPspkOptions();

        return view('livewire.admin.pspk.ref-saran-pengembangan.index', [
            'data' => $data,
            'level_pspk_options' => $level_pspk_options,
            'saranColumns' => self::SARAN_COLUMNS,
            'saranLabels' => self::SARAN_LABELS,
        ]);
    }

    public function resetFilters(): void
    {
        $this->reset('filter_level_pspk', 'search');
        $this->resetPage();
    }

    public function openModal(): void
    {
        $this->resetValidation();
        $this->form->reset();
        $this->isUpdate = false;
        $this->editId = null;
        $this->showModal = true;
        $this->dispatch('modalOpened');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->form->reset();
        $this->isUpdate = false;
        $this->editId = null;
    }

    public function edit($id): void
    {
        try {
            $data = RefSaranPengembangan::findOrFail($id);
            $this->editId = $data->id;
            $this->form->level_pspk_id = $data->level_pspk_id;
            foreach (self::SARAN_COLUMNS as $col) {
                $this->form->{$col} = $data->{$col} ?? '';
            }
            $this->isUpdate = true;
            $this->showModal = true;
            $this->resetValidation();
            $this->dispatch('modalOpened');
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function save(): void
    {
        $rules = [
            'form.level_pspk_id' => [
                'required',
                Rule::unique('ref_saran_pengembangan', 'level_pspk_id')->ignore($this->editId),
            ],
        ];
        foreach (self::SARAN_COLUMNS as $col) {
            $rules['form.' . $col] = ['nullable', 'string'];
        }
        $rules['form.level_pspk_id'][] = 'exists:ref_level_pspk,id';

        $this->validate($rules, [
            'form.level_pspk_id.required' => 'level pspk harus dipilih',
            'form.level_pspk_id.unique' => 'level pspk ini sudah punya referensi saran',
            'form.level_pspk_id.exists' => 'level pspk tidak valid',
        ]);

        try {
            $payload = [
                'level_pspk_id' => $this->form->level_pspk_id,
            ];
            foreach (self::SARAN_COLUMNS as $col) {
                $payload[$col] = $this->form->{$col};
            }

            if ($this->isUpdate) {
                $data = RefSaranPengembangan::findOrFail($this->editId);
                $old_data = $data->getOriginal();
                $data->fill($payload);
                $data->save();

                activity_log($data, 'update', 'ref-pspk', $old_data);
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $model = RefSaranPengembangan::create($payload);

                activity_log($model, 'create', 'ref-pspk');
                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }
            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function deleteConfirmation($id): void
    {
        $this->selected_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    #[On('delete')]
    public function destroy(): void
    {
        try {
            $data = RefSaranPengembangan::find($this->selected_id);
            if (! $data) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'data tidak ditemukan']);

                return;
            }
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'ref-pspk', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
