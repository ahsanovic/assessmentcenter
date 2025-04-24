<?php

namespace App\Livewire\Admin\DataTes\TesSelesai;

use App\Models\Event;
use App\Models\RefJabatanDiuji;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Event'])]
class Index extends Component
{
    use WithPagination;

    public $jabatan_diuji;
    public $tgl_mulai;
    public $selected_id;
    public $event;

    #[Url(as: 'q')]
    public ?string $search =  '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedJabatanDiuji()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['jabatan_diuji', 'tgl_mulai', 'search']);
        $this->resetPage();
        $this->render();
        $this->dispatch('reset-select2');
    }

    public function render()
    {
        $data = Event::withCount(['peserta', 'hasilInterpersonal', 'hasilKesadaranDiri', 'hasilBerpikirKritis', 'hasilProblemSolving', 'hasilPengembanganDiri', 
                                    'hasilKecerdasanEmosi', 'hasilMotivasiKomitmen', 'peserta as peserta_selesai_count' => function ($query) {
                $query->whereHas('ujianInterpersonal', function ($query) {
                    $query->where('is_finished', 'true');
                })
                ->whereHas('ujianKesadaranDiri', function ($query) {
                    $query->where('is_finished', 'true');
                })
                ->whereHas('ujianBerpikirKritis', function ($query) {
                    $query->where('is_finished', 'true');
                })
                ->whereHas('ujianPengembanganDiri', function ($query) {
                    $query->where('is_finished', 'true');
                })
                ->whereHas('ujianProblemSolving', function ($query) {
                    $query->where('is_finished', 'true');
                })
                ->whereHas('ujianKecerdasanEmosi', function ($query) {
                    $query->where('is_finished', 'true');
                })
                ->whereHas('ujianMotivasiKomitmen', function ($query) {
                    $query->where('is_finished', 'true');
                });
            }])
            ->with(['peserta', 'hasilInterpersonal', 'hasilKesadaranDiri', 'hasilBerpikirKritis', 'hasilProblemSolving', 'hasilPengembanganDiri', 'hasilKecerdasanEmosi', 'hasilMotivasiKomitmen'])
            ->when($this->event, function($query) {
                $query->where('id', $this->event);
            })
            ->when($this->jabatan_diuji, function ($query) {
                $query->where('jabatan_diuji_id', $this->jabatan_diuji);
            })
            ->when($this->tgl_mulai, function($query) {
                $tgl_mulai = date('Y-m-d', strtotime($this->tgl_mulai));
                $query->where('tgl_mulai', $tgl_mulai);
            })
            ->orderByDesc('tgl_mulai')
            ->paginate(10);

        $option_jabatan_diuji = RefJabatanDiuji::pluck('jenis', 'id');
        $option_event = Event::pluck('nama_event', 'id');

        return view('livewire.admin.data-tes.tes-selesai.index', compact('data', 'option_jabatan_diuji', 'option_event'));
    }
}
