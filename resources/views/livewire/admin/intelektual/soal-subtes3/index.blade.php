<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Intelektual'],
        ['url' => null, 'title' => 'Soal Sub Tes 3']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-btn-add :url="route('admin.soal-intelektual-subtes3.create')" class="mb-4" />
                    <div class="table-responsive">
                        <table class="table table-hover align-middle shadow-sm border rounded" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Model Soal</th>
                                    <th>Soal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td>{{ $item->modelSoal->jenis }}</td>
                                        <td class="text-wrap">
                                            @if($item->image_soal)
                                                <div class="mt-2">
                                                    <img src="{{ asset('storage/'.$item->image_soal) }}" class="img-fluid" style="max-height:200px;">
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <x-table.btn-show :id="$item->id" />
                                            <x-table.btn-link
                                                :route="'admin.soal-intelektual-subtes3.edit'"
                                                :params="['id' => $item->id]"
                                                :icon="'edit-3'"
                                                :tooltip="'Edit'"
                                                :color="'success'"
                                                :navigate="true"
                                            />
                                            <x-table.btn-delete :id="$item->id" :disabled="auth()->user()->role == 'user'" />
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data soal sub tes 3...</div>
                                        </td>
                                    </tr>
                                @endif
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
                                            <a href="{{ route('admin.soal-intelektual-subtes3.edit', $selectedSoal->id) }}" class="btn btn-warning">Edit</a>
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
