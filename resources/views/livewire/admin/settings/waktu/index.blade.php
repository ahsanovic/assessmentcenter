<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Waktu Tes']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Setting Waktu Tes</h6>
                    <a href="{{ route('admin.settings.waktu.create') }}" wire:navigate class="btn btn-xs btn-outline-primary mt-3">Tambah</a>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Jenis Tes</th>
                                    <th>Waktu Tes</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->jenis_tes === 1 ? 'Tes Potensi' : 'Tes Literasi Digital & Emerging Skill' }}</td>
                                        <td>{{ $item->waktu }} menit</td>
                                        <td>
                                            @if ($item->is_active == 'true')
                                                <span
                                                    class="badge bg-success"
                                                    wire:click="changeStatusTimerConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                >
                                                    Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-danger"
                                                    wire:click="changeStatusTimerConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                >
                                                    Non Aktif
                                                </span>
                                            @endif
                                        <td>
                                            <div class="btn-group dropstart">
                                                <a
                                                    class="btn btn-xs btn-outline-warning"
                                                    wire:navigate
                                                    href="{{ route('admin.settings.waktu.edit', $item->id) }}"
                                                >
                                                    Edit
                                                </a>
                                                <button wire:click="deleteConfirmation('{{ $item->id }}')" tabindex="0" class="btn btn-xs btn-outline-danger">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>        
        </div>
    </div>
</div>
