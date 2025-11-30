<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Peserta']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('admin.peserta.create') }}" wire:navigate wire:ignore
                        class="btn btn-sm btn-outline-primary">
                        <i class="btn-icon-prepend" data-feather="edit"></i>
                        Tambah
                    </a>
                    <div class="card mt-4 mb-4 bg-light-subtle">
                        <div class="card-body">
                            <h6 class="text-danger" wire:ignore><i class="link-icon" data-feather="filter"></i> Filter</h6>
                            <div class="row mt-2">
                                <div class="col-sm-3">
                                    <div wire:ignore>
                                        <select wire:model.live="event" class="form-select" id="event">
                                            <option value="">event</option>
                                            @foreach ($option_event as $key => $item)
                                                <option value="{{ $key }}">{{ $item }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <select wire:model.live="is_portofolio_completed" class="form-select" id="status">
                                        <option value="">portofolio</option>
                                        <option value="true">sudah lengkap</option>
                                        <option value="false">belum lengkap</option>
                                    </select>
                                </div>
                                <div class="col-sm-1">
                                    <select wire:model.live="is_active" class="form-select" id="status">
                                        <option value="">status</option>
                                        @foreach ($option_status as $key => $item)
                                            <option value="{{ $key }}">{{  $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <select wire:model.live="jenis_peserta_id" class="form-select" id="jenis-peserta">
                                        <option value="">jenis peserta</option>
                                        @foreach ($option_jenis_peserta as $key => $item)
                                            <option value="{{ $key }}">{{  $item }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <div class="input-group" wire:ignore>
                                        <span class="input-group-text bg-white"><i data-feather="search"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari peserta...">
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <button wire:click="resetFilters" class="btn btn-sm btn-inverse-danger">
                                        <span wire:ignore><i class="btn-icon-prepend" data-feather="refresh-ccw"></i> Reset</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Peserta</th>
                                    <th>Jenis Peserta</th>
                                    <th>Jabatan</th>
                                    <th>Unit Kerja / Instansi</th>
                                    <th>Event / Metode Tes</th>
                                    <th>Portofolio</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td class="text-wrap">
                                            {{ $item->nama }} <br />
                                            @if ($item->jenis_peserta_id == 1)
                                                {{ $item->nip }}
                                            @elseif ($item->jenis_peserta_id == 2)
                                            {{ $item->nik }}
                                            @endif
                                        </td>
                                        <td class="text-wrap">{{ $item->jenisPeserta->jenis_peserta }}</td>
                                        <td class="text-wrap">{{ $item->jabatan }}</td>
                                        <td class="text-wrap">{{ $item->unit_kerja }} <br /> {{ $item->instansi }}</td>
                                        <td class="text-wrap">
                                            {{ $item->event->nama_event ?? '' }} <br />
                                            <span class="badge bg-dark">{{ $item->event->metodeTes->metode_tes }}</span>
                                        </td>
                                        <td>
                                            @if($item->is_portofolio_lengkap)
                                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="Lengkap" class="text-success">✔</span>
                                            @else
                                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="Belum Lengkap" class="text-danger">✖</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->is_active == 'false')
                                                <span
                                                    class="badge bg-danger"
                                                    wire:click="changeStatusPesertaConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                >
                                                    Non Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-success"
                                                    wire:click="changeStatusPesertaConfirmation('{{ $item->id }}')"
                                                    style="cursor: pointer;"
                                                >
                                                    Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <a
                                                class="btn btn-sm btn-inverse-success btn-icon"
                                                wire:navigate
                                                href="{{ route('admin.peserta.edit', $item->id) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"
                                            >
                                                <span wire:ignore><i class="link-icon" data-feather="edit-3"></i></span>
                                            </a>
                                            <button
                                                wire:click="deleteConfirmation('{{ $item->id }}')"
                                                class="btn btn-sm btn-inverse-danger btn-icon"
                                                @disabled($item->test_started_at != null)
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Hapus"
                                            >
                                                <span wire:ignore><i class="link-icon" data-feather="trash"></i></span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>        
        </div>
        <x-pagination :items="$data" />
    </div>
</div>
@push('js')
    @script()
        <script>
            $(document).ready(function() {
                $('#event').select2()
                    .on('change', function(e) {
                        @this.set('event', $(this).val());
                    });
                
                Livewire.on('reset-select2', () => {
                    $('#event').val(null).trigger('change');
                });
            })
        </script>
    @endscript
@endpush