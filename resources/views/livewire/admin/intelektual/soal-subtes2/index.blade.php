<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Intelektual'],
        ['url' => null, 'title' => 'Soal Sub Tes 2']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Data Soal Sub Tes 2</h6>
                    <a href="{{ route('admin.soal-intelektual-subtes2.create') }}" wire:navigate class="btn btn-xs btn-outline-primary mt-3">Tambah</a>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Model Soal</th>
                                    <th>Soal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->modelSoal->jenis }}</td>
                                        <td class="text-wrap">
                                            {{ $item->soal }}
                                            @if($item->image_soal)
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/'.$item->image_soal) }}" class="img-fluid" style="max-height:200px;">
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button wire:click="showDetail({{ $item->id }})" 
                                                        class="btn btn-xs btn-outline-info">
                                                    Detail
                                                </button>
                                                <a
                                                    class="btn btn-xs btn-outline-warning"
                                                    wire:navigate
                                                    href="{{ route('admin.soal-intelektual-subtes2.edit', $item->id) }}"
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

                        {{-- Modal Detail --}}
                        <div class="modal fade @if($showDetailModal) show d-block @endif" tabindex="-1" 
                            style="@if($showDetailModal) display:block; background:rgba(0,0,0,.5) @endif">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    @if($selectedSoal)
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detail Soal</h5>
                                            <button type="button" class="btn-close" wire:click="$set('showDetailModal', false)"></button>
                                        </div>
                                        <div class="modal-body table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>Model Soal</th>
                                                    <td>{{ $selectedSoal->modelSoal->jenis }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Soal</th>
                                                    <td>
                                                        {{ $selectedSoal->soal }}
                                                        @if($selectedSoal->image_soal)
                                                            <div class="mt-2">
                                                                <img src="{{ asset('storage/'.$selectedSoal->image_soal) }}" class="img-fluid" style="max-height:200px;">
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @foreach (['a','b','c','d','e'] as $opsi)
                                                    <tr>
                                                        <th>Opsi {{ strtoupper($opsi) }}</th>
                                                        <td>
                                                            {{ $selectedSoal->{'opsi_'.$opsi} }}
                                                            @if($selectedSoal->{'image_opsi_'.$opsi})
                                                                <div class="mt-2">
                                                                    <img src="{{ asset('storage/'.$selectedSoal->{'image_opsi_'.$opsi}) }}" class="img-fluid" style="max-height:150px;">
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th>Kunci Jawaban</th>
                                                    <td>
                                                        {{ $selectedSoal->kunci_jawaban }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" wire:click="$set('showDetailModal', false)">Tutup</button>
                                            <a href="{{ route('admin.soal-intelektual-subtes2.edit', $selectedSoal->id) }}" class="btn btn-warning">Edit</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>        
        </div>
        <x-pagination :items="$data" />
    </div>
</div>
