<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Data Assessor']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Assessor</h6>
                    <a href="{{ route('admin.assessor.create') }}" wire:navigate class="btn btn-xs btn-outline-primary mt-3">Tambah</a>
                    <h6 class="mt-4 text-danger"><i class="link-icon" data-feather="filter"></i> Filter</h6>
                    <div class="row mt-2">
                        <div class="col-sm-3">
                            <div class="mb-3" wire:ignore>
                                <select wire:model.live="event" class="form-select form-select-sm" id="event">
                                    <option value="">event</option>
                                    @foreach ($option_event as $key => $item)
                                        <option value="{{ $key }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="mb-3">
                                <select wire:model.live="is_active" class="form-select form-select-sm" id="status">
                                    <option value="">status</option>
                                    @foreach ($option_status as $key => $item)
                                        <option value="{{ $key }}">{{  $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="mb-3">
                                <select wire:model.live="is_asn" class="form-select form-select-sm" id="jenis-assessor">
                                    <option value="">jenis assessor</option>
                                    <option value="true">ASN</option>
                                    <option value="false">Non ASN</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <input wire:model.live.debounce="search" class="form-control form-control-sm" placeholder="cari peserta berdasar nama/nip/nik/jabatan/instansi" />
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="mb-3">
                                <button wire:click="resetFilters" class="btn btn-sm btn-inverse-danger">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Assessor</th>
                                    <th>NIK / NIP - Pangkat/Gol</th>
                                    <th>Jabatan</th>
                                    <th>Instansi</th>
                                    <th>Jenis</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>
                                            @if ($item->is_asn == 'true')
                                                {{ $item->nip }} <br/> {{ $item->golPangkat->pangkat . ' - ' . $item->golPangkat->golongan }}
                                            @else
                                                {{ $item->nik }}
                                            @endif
                                        </td>
                                        <td>{{ $item->jabatan }}</td>
                                        <td>{{ $item->instansi }}</td>
                                        <td>
                                            @if ($item->is_asn == 'true')
                                                <span class="badge bg-primary">ASN</span>    
                                            @else
                                                <span class="badge bg-dark">Non ASN</span>
                                            @endif 
                                        </td>
                                        <td>
                                            @if ($item->is_active == 'true')
                                                <span class="badge bg-success">Aktif</span>    
                                            @else
                                                <span class="badge bg-danger">Non Aktif</span>
                                            @endif 
                                        </td>
                                        <td>
                                            <div class="btn-group dropstart">
                                                <a
                                                    class="btn btn-xs btn-outline-warning"
                                                    wire:navigate
                                                    href="{{ route('admin.assessor.edit', $item->id) }}"
                                                >
                                                    Edit
                                                </a>
                                                <button wire:click="deleteConfirmation('{{ $item->id }}')" class="btn btn-xs btn-outline-danger">
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