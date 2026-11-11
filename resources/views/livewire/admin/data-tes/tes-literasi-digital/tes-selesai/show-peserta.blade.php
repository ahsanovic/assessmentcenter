<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.tes-selesai.literasi-digital'), 'title' => 'Hasil Tes Literasi Digital'],
        ['url' => null, 'title' => 'Peserta']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <x-monitoring.event-header :nama="$event->nama_event" :nama-grup="$event->eventGroup?->nama" />
                    <x-monitoring.filter-panel>
                            <div class="row g-2 align-items-end">
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i data-feather="search" style="width:16px;height:16px;"></i></span>
                                        <input wire:model.live.debounce="search" class="form-control" placeholder="cari peserta..." autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group flatpickr" id="flatpickr-date">
                                        <input type="text" wire:model.live="tanggal_tes"
                                            class="form-control flatpickr-input" placeholder="tanggal tes"
                                            data-input="" readonly="readonly">
                                        <span class="input-group-text input-group-addon" data-toggle="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-calendar">
                                                <rect x="3" y="4" width="18" height="18" rx="2"
                                                    ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <x-btn-reset :text="'Reset'" />
                                </div>
                            </div>
                    </x-monitoring.filter-panel>
                    <x-monitoring.skor-ringkasan-panel :ringkasan="$ringkasanSkor" />
                    <div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
                        <x-btn-download
                            :route="'admin.tes-selesai.literasi-digital.download-all-laporan'"
                            :params="[$event->id]"
                            :query="['tanggalTes' => $tanggal_tes ? \Carbon\Carbon::parse($tanggal_tes)->format('Y-m-d') : '']"
                            text="Laporan PDF (.zip)"
                            icon="download"
                            color="dark"
                            :disabled="$data->isEmpty()"
                        />
                        <button type="button" class="btn btn-sm btn-icon-text btn-primary" wire:click="hitungUlangMassalConfirmation">
                            <span wire:ignore>
                                <i class="btn-icon-prepend" data-feather="repeat"></i>
                            </span>
                            Hitung ulang JPM
                        </button>
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
                                    <th>Mulai / Selesai Tes</th>
                                    <th class="text-center">Budaya</th>
                                    <th class="text-center">Cakap</th>
                                    <th class="text-center">Keamanan</th>
                                    <th class="text-center">Etika</th>
                                    <th>Total</th>
                                    <th>JPM</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $item)
                                    <tr wire:key="hasil-literasi-{{ $item->hasil_literasi_digital_id }}" class="@if($loop->iteration % 2 == 1) bg-body @endif border-bottom">
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
                                        <td class="text-wrap">{{ $item->jabatan }}</td>
                                        <td class="text-wrap">
                                            <span class="fw-medium text-dark">{{ $item->instansi }}</span>
                                            <br>
                                            <span class="text-muted small">{{ $item->unit_kerja }}</span>
                                        </td>
                                        <td class="text-wrap">
                                            {{ \Carbon\Carbon::parse($item->waktu_mulai)->translatedFormat('d F Y / H:i:s') }} <br/>
                                            {{ \Carbon\Carbon::parse($item->waktu_selesai)->translatedFormat('d F Y / H:i:s') }}
                                        </td>
                                        <td class="text-center">
                                            <x-literasi-digital.pilar-skor :skor="$item->skor_budaya" :level="$item->level_budaya" />
                                        </td>
                                        <td class="text-center">
                                            <x-literasi-digital.pilar-skor :skor="$item->skor_cakap" :level="$item->level_cakap" />
                                        </td>
                                        <td class="text-center">
                                            <x-literasi-digital.pilar-skor :skor="$item->skor_keamanan" :level="$item->level_keamanan" />
                                        </td>
                                        <td class="text-center">
                                            <x-literasi-digital.pilar-skor :skor="$item->skor_etika" :level="$item->level_etika" />
                                        </td>
                                        <td class="fw-semibold">{{ $item->skor_total }}</td>
                                        <td>{{ number_format((float) $item->jpm, 2, ',', '.') }}%</td>
                                        <td>
                                            @if ($item->kategori === 'Optimal')
                                                <span class="badge bg-success">Optimal</span>
                                            @elseif ($item->kategori === 'Cukup Optimal')
                                                <span class="badge bg-warning text-dark">Cukup Optimal</span>
                                            @else
                                                <span class="badge bg-danger">Kurang Optimal</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item->is_finished == 'true')
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-primary btn-icon rounded-circle border-0 shadow-sm"
                                                    wire:click="hitungUlangConfirmation('{{ $item->ujian_literasi_digital_id }}')"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Hitung ulang JPM dan level"
                                                    style="transition: background 0.2s;"
                                                >
                                                    <span wire:ignore class="d-inline-flex align-items-center justify-content-center">
                                                        <i class="link-icon" data-feather="repeat"></i>
                                                    </span>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-warning btn-icon rounded-circle border-0 shadow-sm"
                                                    wire:click="setUjianKeBelumSelesaiConfirmation('{{ $item->ujian_literasi_digital_id }}')"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="Set ujian ke belum selesai"
                                                    style="transition: background 0.2s;"
                                                >
                                                    <i class="link-icon" data-feather="refresh-cw"></i>
                                                </button>
                                                <x-table.btn-delete :id="$item->hasil_literasi_digital_id" />
                                                @if ($item->nip || $item->nik)
                                                    <x-table.btn-link
                                                        :route="'admin.tes-selesai.literasi-digital.download'"
                                                        :params="['idEvent' => $event->id, 'identifier' => $item->nip ?: $item->nik]"
                                                        :icon="'download'"
                                                        :tooltip="'Download Pdf'"
                                                        :color="'success'"
                                                        :target="'_blank'"
                                                    />
                                                @endif
                                            @endif
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
    <script>
        window.addEventListener('hitung-ulang-confirmation', () => {
            Swal.fire({
                title: 'Hitung ulang JPM dan level?',
                html: 'Skor benar/salah peserta tidak diubah. Skor pilar, total, level, JPM, dan kategori dihitung dengan rumus yang berlaku sekarang.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hitung ulang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('hitungUlang');
                }
            });
        });
        window.addEventListener('hitung-ulang-massal-confirmation', () => {
            Swal.fire({
                title: 'Hitung ulang semua hasil event ini?',
                html: 'Semua peserta <b>selesai</b> pada event ini dihitung ulang, termasuk yang tidak tampil di halaman ini. Jawaban peserta tidak diubah.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hitung ulang semua',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('hitungUlangMassal');
                }
            });
        });
        window.addEventListener('set-ujian-ke-belum-selesai-confirmation', () => {
            Swal.fire({
                title: 'Set ujian ke belum selesai?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, ubah status',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('setUjianKeBelumSelesai');
                }
            });
        });
        window.addEventListener('set-ujian-ke-belum-selesai-massal-confirmation', () => {
            Swal.fire({
                title: 'Set semua ujian Literasi Digital ke belum selesai?',
                html: 'Semua ujian pada event ini yang berstatus <b>selesai</b> akan diubah menjadi <b>belum selesai</b>.',
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
