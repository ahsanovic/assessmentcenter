<?php

namespace App\Livewire\Admin\Peserta;

use App\Models\Event;
use App\Models\Peserta;
use App\Models\RefJenisPeserta;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Peserta'])]
class Index extends Component
{
    use WithPagination;

    public $selected_id;
    public $event;
    public $is_active;
    public $jenis_peserta_id;
    public $is_portofolio_completed;

    #[Url(as: 'q')]
    public ?string $search =  '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedEvent()
    {
        $this->resetPage();
    }

    public function updatedIsActive()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset();
        $this->resetPage();
        $this->render();
        $this->dispatch('reset-select2');
    }

    public function render()
    {
        $data = Peserta::with(['event', 'jenisPeserta'])->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('nip', 'like', '%' . $this->search . '%')
                    ->orWhere('nik', 'like', '%' . $this->search . '%')
                    ->orWhere('jabatan', 'like', '%' . $this->search . '%')
                    ->orWhere('instansi', 'like', '%' . $this->search . '%');
            });
        })
            ->when($this->jenis_peserta_id, function ($query) {
                $query->where('jenis_peserta_id', $this->jenis_peserta_id);
            })
            ->when($this->event, function ($query) {
                $query->where('event_id', $this->event);
            })
            ->when($this->is_active, function ($query) {
                $query->where('is_active', $this->is_active);
            })
            ->when(!is_null($this->is_portofolio_completed), function ($query) {
                if ($this->is_portofolio_completed === 'true') {
                    // Peserta lengkap
                    $query->whereNotNull('tempat_lahir')
                        ->whereNotNull('tgl_lahir')
                        ->whereNotNull('jk')
                        ->whereNotNull('agama_id')
                        ->whereNotNull('alamat')
                        ->whereNotNull('no_hp')
                        ->whereNotNull('foto')
                        ->where(function ($q) {
                            $q->where(function ($q1) {
                                $q1->where('jenis_peserta_id', 1)
                                    ->whereNotNull('nip')
                                    ->whereNotNull('gol_pangkat_id');
                            })->orWhere(function ($q2) {
                                $q2->where('jenis_peserta_id', 2)
                                    ->whereNotNull('nik');
                            });
                        });
                } elseif ($this->is_portofolio_completed === 'false') {
                    // Peserta belum lengkap (minimal satu field null)
                    $query->where(function ($q) {
                        $q->whereNull('tempat_lahir')
                            ->orWhereNull('tgl_lahir')
                            ->orWhereNull('jk')
                            ->orWhereNull('agama_id')
                            ->orWhereNull('alamat')
                            ->orWhereNull('no_hp')
                            ->orWhereNull('foto')
                            ->orWhere(function ($q2) {
                                $q2->where(function ($q3) {
                                    $q3->where('jenis_peserta_id', 1)
                                        ->where(function ($qq) {
                                            $qq->whereNull('nip')
                                                ->orWhereNull('gol_pangkat_id');
                                        });
                                })->orWhere(function ($q4) {
                                    $q4->where('jenis_peserta_id', 2)
                                        ->whereNull('nik');
                                });
                            });
                    });
                }
            })
            ->orderByDesc('id')
            ->paginate(10);

        $option_event = Event::pluck('nama_event', 'id');
        $option_status = ['true' => 'aktif', 'false' => 'tidak aktif'];
        $option_jenis_peserta = RefJenisPeserta::pluck('jenis_peserta', 'id');

        return view('livewire.admin.peserta.index', compact('data', 'option_event', 'option_status', 'option_jenis_peserta'));
    }

    public function deleteConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function changeStatusPesertaConfirmation($id)
    {
        $this->selected_id = $id;
        $this->dispatch('change-status-peserta-confirmation');
    }

    #[On('changeStatusPeserta')]
    public function changeStatusPeserta()
    {
        try {
            $data = Peserta::find($this->selected_id);

            if ($data->is_active == 'true') {
                $data->update(['is_active' => 'false']);
            } else {
                $data->update(['is_active' => 'true']);
            }

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil merubah status peserta']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal merubah status peserta']);
        }
    }

    #[On('delete')]
    public function destroy()
    {
        try {
            $data = Peserta::find($this->selected_id);
            $old_data = $data->getOriginal();

            activity_log($data, 'delete', 'peserta', $old_data);

            $data->delete();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'berhasil menghapus data']);
        } catch (\Throwable $th) {
            // throw $th;
            $this->dispatch('toast', ['type' => 'error', 'message' => 'gagal menghapus data']);
        }
    }
}
