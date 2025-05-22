<?php

namespace App\Livewire\Admin\ActivityLog;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin.app', ['title' => 'Activity Log'])]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public ?string $search = '';

    public $action;
    public $tgl_aktifitas;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = ActivityLog::with('user')
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->where('username', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->action, function ($query) {
                $query->where('action', $this->action);
            })
            ->when($this->tgl_aktifitas, function ($query) {
                $query->whereDate('created_at', Carbon::parse($this->tgl_aktifitas));
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('livewire.admin.log-activity.index', compact('data'));
    }

    public function resetFilters()
    {
        $this->reset();
        $this->resetPage();
        $this->render();
    }
}
