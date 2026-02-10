<?php

namespace App\Livewire\Admin\NomorLaporan;

use App\Models\Event;
use App\Models\NomorLaporan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Nomor Laporan Penilaian'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $tanggal;
    public $event_id_modal;
    public $nomor;
    public $tanggal_modal;
    public $showModal = false;
    public $isUpdate = false;

    // Filter properties
    public $event_id;

    #[Url(as: 'q')]
    public ?string $search = '';

    #[Locked]
    public $editId;

    // Validation rules
    protected function rules()
    {
        return [
            'event_id_modal' => 'required',
            'nomor' => 'required',
            'tanggal_modal' => 'required',
        ];
    }

    protected $messages = [
        'event_id_modal.required' => 'Event harus dipilih',
        'nomor.required' => 'Nomor laporan harus diisi',
        'tanggal_modal.required' => 'Tanggal harus dipilih',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->event_id_modal = '';
        $this->nomor = '';
        $this->tanggal_modal = '';
        $this->showModal = true;
        $this->isUpdate = false;
        $this->editId = null;
        $this->dispatch('modalOpened');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->event_id_modal = '';
        $this->nomor = '';
        $this->tanggal_modal = '';
        $this->isUpdate = false;
        $this->editId = null;
    }

    public function edit($id)
    {
        try {
            $data = NomorLaporan::findOrFail($id);
            $this->editId = $data->id;
            $this->event_id_modal = $data->event_id;
            $this->nomor = $data->nomor;
            $this->tanggal_modal = $data->tanggal;
            $this->isUpdate = true;
            $this->showModal = true;
            $this->resetValidation();
            $this->dispatch('set-flatpickr', [
                'model' => 'tanggal_modal',
                'value' => $this->tanggal_modal,
            ]);
            // $this->dispatch('modalOpened');
        } catch (\Throwable $th) {
            //throw new \Exception($th->getMessage());
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function save()
    {
        $this->validate();
        
        try {
            // Convert tanggal from d-m-Y to Y-m-d
            $tanggal = date('Y-m-d', strtotime($this->tanggal_modal));

            if ($this->isUpdate) {
                $data = NomorLaporan::findOrFail($this->editId);
                $old_data = $data->getOriginal();

                $data->event_id = $this->event_id_modal;
                $data->nomor = $this->nomor;
                $data->tanggal = $tanggal;
                $data->save();

                activity_log($data, 'update', 'nomor-laporan', $old_data);

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil ubah data']);
            } else {
                $check_duplicate = NomorLaporan::where('nomor', $this->nomor)
                    ->where('tanggal', $tanggal)
                    ->exists();

                if ($check_duplicate) {
                    $this->dispatch('toast', ['type' => 'error', 'message' => 'data dengan nomor ' . $this->nomor . ' sudah ada!']);
                    return;
                }

                $data = NomorLaporan::create([
                    'event_id' => $this->event_id_modal,
                    'nomor' => $this->nomor,
                    'tanggal' => $tanggal,
                ]);

                activity_log($data, 'create', 'nomor-laporan');

                $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil tambah data']);
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'terjadi kesalahan']);
        }
    }

    public function render()
    {
        $data = NomorLaporan::with('event')
            ->when($this->search, fn($query) => $query->where('nomor', 'like', '%' . $this->search . '%'))
            ->when($this->tanggal, function ($query) {
                $tanggal = date('Y-m-d', strtotime($this->tanggal));
                $query->where('tanggal', $tanggal);
            })
            ->when($this->event_id, fn($query) => $query->where('event_id', $this->event_id))
            ->orderByDesc('id')
            ->paginate(10);

        $options_event = Event::pluck('nama_event', 'id');

        return view('livewire.admin.nomor-laporan.index', compact('data', 'options_event'));
    }

    public function resetFilters()
    {
        $this->reset(['search', 'tanggal', 'event_id']);
        $this->resetPage();
        $this->dispatch('reset-select2');
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
            $data = NomorLaporan::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'nomor-laporan', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
