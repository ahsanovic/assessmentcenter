<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.tes-selesai.intelektual'), 'title' => 'Hasil Tes Intelektual Sub Tes 3'],
        ['url' => null, 'title' => 'Peserta']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-monitoring.event-header :nama="$event->nama_event" />
                    <x-monitoring.filter-panel>
                            <div class="row g-2 align-items-end">
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i data-feather="search" style="width:16px;height:16px;"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari peserta..." autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                            </div>
                    </x-monitoring.filter-panel>
                    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-icon-text btn-warning text-dark" wire:click="setUjianKeBelumSelesaiMassalConfirmation">
                            <span wire:ignore>
                                <i class="btn-icon-prepend" data-feather="refresh-cw"></i>
                            </span>
                            Set belum selesai (massal)
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table ac-data-table table-hover align-middle" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th>Nama Peserta</th>
                                    <th>Jabatan</th>
                                    <th>Instansi<br><small class="text-muted">Unit Kerja</small></th>
                                    <th>Mulai Tes</th>
                                    <th>Selesai Tes</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
                                        <td class="text-center text-secondary fw-bold">{{ $data->firstItem() + $index }}</td>
                                        <td>
                                            <span class="fw-medium">{{ $item->nama }}</span><br>
                                            <span class="text-muted small">
                                            @if ($item->jenis_peserta_id == 1)
                                                <div class="fw-medium">{{ $item->nip }}</div>
                                                @if (!empty($item->golPangkat?->pangkat) && !empty($item->golPangkat?->golongan))
                                                    <span class="badge bg-secondary-subtle text-dark mt-1">
                                                        {{ $item->golPangkat->pangkat . ' - ' . $item->golPangkat->golongan }}
                                                    </span>
                                                @else
                                                    <span class="text-muted d-block mt-1"></span>
                                                @endif
                                            @elseif ($item->jenis_peserta_id == 2)
                                                <div class="fw-medium">{{ $item->nik }}</div>
                                            @endif
                                            </span>
                                        </td>
                                        <td class="text-wrap">
                                            <span class="text-dark fw-normal">{{ $item->jabatan }}</span>
                                        </td>
                                        <td class="text-wrap">
                                            <span class="fw-medium text-dark">{{ $item->instansi }}</span>
                                            <br>
                                            <span class="text-muted small">{{ $item->unit_kerja }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border-1 border">
                                                {{ \Carbon\Carbon::parse($item->waktu_mulai)->translatedFormat('d F Y') }}
                                                <span class="text-muted px-1">/</span>
                                                {{ \Carbon\Carbon::parse($item->waktu_mulai)->translatedFormat('H:i:s') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border-1 border">
                                                {{ \Carbon\Carbon::parse($item->waktu_selesai)->translatedFormat('d F Y') }}
                                                <span class="text-muted px-1">/</span>
                                                {{ \Carbon\Carbon::parse($item->waktu_selesai)->translatedFormat('H:i:s') }}
                                            </span>
                                        </td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-success btn-icon rounded-circle border-0 shadow-sm"
                                                wire:click="setUjianKeBelumSelesaiConfirmation('{{ $item->ujian_intelektual_subtes_3_id }}')"
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top" 
                                                title="Set Ujian Ke Belum Selesai"
                                                style="transition: background 0.2s;"
                                            >
                                                <i class="link-icon" data-feather="refresh-cw"></i>
                                            </button>
                                            @if ($item->is_finished == 'true')
                                                <x-table.btn-delete :id="$item->hasil_intelektual_id" />
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                @if($data->count() === 0)
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-5 ac-empty-state">
                                            <i class="link-icon" data-feather="inbox" style="font-size: 24px; opacity: 0.7;"></i>
                                            <div class="mt-2 fw-semibold">Tidak ada data peserta...</div>
                                        </td>
                                    </tr>
                                @endif
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
    <script>
        window.addEventListener('set-ujian-ke-belum-selesai-confirmation', data => {
            Swal.fire({
                title: 'Set Ujian Ke Belum Selesai?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Set Ujian Ke Belum Selesai!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('setUjianKeBelumSelesai');
                }
            });
        });
        window.addEventListener('set-ujian-ke-belum-selesai-massal-confirmation', () => {
            Swal.fire({
                title: 'Set semua ujian ke belum selesai?',
                html: 'Semua ujian <b>Sub Tes 3</b> pada event ini yang berstatus <b>selesai</b> akan diubah menjadi <b>belum selesai</b>.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, terapkan ke semua',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('setUjianKeBelumSelesaiMassal');
                }
            });
        });
    </script>
@endpush