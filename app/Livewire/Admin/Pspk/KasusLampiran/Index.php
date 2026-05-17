<?php

namespace App\Livewire\Admin\Pspk\KasusLampiran;

use App\Models\Pspk\PspkKasusLampiran;
use App\Models\Pspk\RefLevelPspk;
use App\Models\Pspk\SoalPspk;
use App\Services\Pspk\StorePspkKasusPdf;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Paket kasus PSPK'])]
class Index extends Component
{
    use WithFileUploads, WithPagination;

    public $selected_id;

    public bool $showModal = false;

    public bool $isUpdate = false;

    public $editId;

    public $level_pspk_id;

    public $nama;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $lampiran_pdf;

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['level_pspk_id', 'nama', 'lampiran_pdf', 'editId', 'isUpdate']);
        $this->showModal = true;
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->reset(['level_pspk_id', 'nama', 'lampiran_pdf', 'editId', 'isUpdate']);
    }

    public function edit(int $id)
    {
        $row = PspkKasusLampiran::findOrFail($id);
        $this->editId = $row->id;
        $this->level_pspk_id = $row->level_pspk_id;
        $this->nama = $row->nama;
        $this->lampiran_pdf = null;
        $this->isUpdate = true;
        $this->showModal = true;
        $this->resetValidation();
        $this->dispatch('modalOpened');
    }

    public function save()
    {
        $rules = [
            'level_pspk_id' => ['required', Rule::in([3, 4])],
            'nama' => ['nullable', 'string', 'max:191'],
            'lampiran_pdf' => $this->isUpdate
                ? ['nullable', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:5120']
                : ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:5120'],
        ];

        $this->validate($rules, [
            'level_pspk_id.required' => 'pilih level',
            'level_pspk_id.in' => 'level harus 3 atau 4',
            'lampiran_pdf.required' => 'unggah PDF kasus (maks. 5 MB)',
        ]);

        try {
            if ($this->isUpdate) {
                $row = PspkKasusLampiran::findOrFail($this->editId);
                $prevPath = $row->lampiran_pdf_path;

                $row->level_pspk_id = $this->level_pspk_id;
                $row->nama = $this->nama;

                if ($this->lampiran_pdf) {
                    StorePspkKasusPdf::deleteIfExists($prevPath);
                    $row->lampiran_pdf_path = StorePspkKasusPdf::store($this->lampiran_pdf);
                }

                $row->save();
                $this->dispatch('toast', ['type' => 'success', 'message' => 'paket kasus diperbarui']);
            } else {
                $path = StorePspkKasusPdf::store($this->lampiran_pdf);
                PspkKasusLampiran::create([
                    'level_pspk_id' => $this->level_pspk_id,
                    'nama' => $this->nama,
                    'lampiran_pdf_path' => $path,
                ]);
                $this->dispatch('toast', ['type' => 'success', 'message' => 'paket kasus ditambahkan']);
            }

            $this->closeModal();
            $this->resetPage();
        } catch (InvalidArgumentException $e) {
            $this->dispatch('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function deleteConfirmation($id)
    {
        $this->selected_id = (int) $id;
        $this->dispatch('show-delete-confirmation');
    }

    #[On('delete')]
    public function destroy()
    {
        try {
            $row = PspkKasusLampiran::findOrFail($this->selected_id);

            if (SoalPspk::where('kasus_lampiran_id', $row->id)->exists()) {
                $this->dispatch('toast', ['type' => 'error', 'message' => 'paket masih dipakai soal Ankas; ubah soal terlebih dahulu']);

                return;
            }

            StorePspkKasusPdf::deleteIfExists($row->lampiran_pdf_path);
            $row->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'paket dihapus']);
            $this->resetPage();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus']);
        }
    }

    public function render()
    {
        $level_options = RefLevelPspk::query()
            ->whereIn('id', [3, 4])
            ->orderBy('id')
            ->pluck('level_pspk', 'id');

        $data = PspkKasusLampiran::query()
            ->withCount('soalPspk')
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.pspk.kasus-lampiran.index', compact('data', 'level_options'));
    }
}
