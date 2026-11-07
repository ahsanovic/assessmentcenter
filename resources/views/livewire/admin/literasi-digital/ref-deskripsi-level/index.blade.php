<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Literasi Digital'],
        ['url' => null, 'title' => 'Referensi Deskripsi']
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-3">Deskripsi di bawah ini nanti tampil di laporan sesuai level yang diperoleh peserta pada tiap pilar. Mengisi sel kosong belum mengunci event yang sudah punya hasil; event itu terkunci pada rumusan sebelum revisi berikutnya. Tes yang baru selesai terkunci pada rumusan yang sedang berlaku. Rentang skor mengikuti rumus penilaian dan tidak diubah dari halaman ini.</p>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle shadow-sm border rounded" style="overflow:hidden;">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="text-center" style="width: 45px;">#</th>
                                    <th style="width: 110px;">Level</th>
                                    <th style="width: 140px;">Rentang skor</th>
                                    <th>Deskripsi</th>
                                    <th style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $nomor = 0; @endphp
                                @forelse ($data as $pilar)
                                    <tr wire:key="kelompok-{{ $pilar->id }}" class="table-light">
                                        <td colspan="5" class="fw-semibold">
                                            {{ $pilar->nama }}
                                            <code class="ms-2">{{ $pilar->kode }}</code>
                                        </td>
                                    </tr>
                                    @foreach ($pilar->deskripsiLevel as $item)
                                        @php
                                            $nomor++;
                                            $level = (int) $item->level;
                                            $badgeClass = match ($level) {
                                                1 => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                                2 => 'bg-info-subtle text-info border-info-subtle',
                                                3 => 'bg-primary-subtle text-primary border-primary-subtle',
                                                4 => 'bg-warning-subtle text-dark border-warning-subtle',
                                                default => 'bg-success-subtle text-success border-success-subtle',
                                            };
                                        @endphp
                                        <tr wire:key="deskripsi-{{ $item->id }}" class="@if($nomor % 2 == 1) bg-body @endif border-bottom">
                                            <td class="text-center text-secondary fw-bold">{{ $nomor }}</td>
                                            <td>
                                                <span class="badge rounded-pill border {{ $badgeClass }} px-2 py-1 fw-medium">Level {{ $level }}</span>
                                            </td>
                                            <td>{{ \App\Services\LiterasiDigital\LiterasiDigitalUjianService::labelRentang($level) }}</td>
                                            <td class="text-wrap">
                                                @if (filled($item->deskripsi))
                                                    {{ \Illuminate\Support\Str::limit($item->deskripsi, 180) }}
                                                @else
                                                    <span class="badge bg-light text-muted border">Belum diisi</span>
                                                @endif
                                            </td>
                                            <td>
                                                <x-table.btn-edit :id="$item->id" />
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada data referensi deskripsi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($showModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1"
         wire:key="modal-edit-deskripsi-{{ $editId }}"
         x-data="{ init() { setTimeout(() => { if (typeof feather !== 'undefined') feather.replace(); }, 50); } }"
         x-init="init()">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="animation: slideDown 0.3s ease-out;">
            <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 24px 32px;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center"
                             style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; backdrop-filter: blur(10px);">
                            <i class="link-icon text-white" data-feather="edit-3" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title text-white fw-bold mb-0" style="font-size: 1.5rem;">Edit Deskripsi Level</h5>
                            <p class="text-white-50 mb-0 mt-1" style="font-size: 0.875rem;">Perbarui narasi yang tampil di laporan</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModal" class="btn-close btn-close-white"
                            style="filter: brightness(0) invert(1); opacity: 0.8; transition: opacity 0.2s;"
                            onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'"></button>
                </div>

                <div class="modal-body" style="padding: 32px; background: #f8f9fa; max-height: 70vh; overflow-y: auto;">
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-md-4">
                                <x-form.input
                                    label="Pilar"
                                    icon="bookmark"
                                    model="namaPilar"
                                    :readonly="true"
                                />
                            </div>
                            <div class="col-md-4">
                                <x-form.input
                                    label="Level"
                                    icon="bar-chart-2"
                                    model="level"
                                    :readonly="true"
                                />
                            </div>
                            <div class="col-md-4">
                                <x-form.input
                                    label="Rentang skor"
                                    icon="sliders"
                                    model="rentang"
                                    :readonly="true"
                                />
                            </div>
                        </div>
                        <x-form.textarea
                            label="Deskripsi"
                            icon="align-left"
                            model="deskripsi"
                            placeholder="Kosongkan bila narasi belum siap"
                            :rows="6"
                        />
                    </form>
                </div>

                <div class="modal-footer" style="background: white; border-top: 2px solid #f0f0f0; padding: 20px 32px; gap: 12px;">
                    <x-modal.btn-cancel />
                    <x-modal.btn-save :isUpdate="true" />
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    @endif
</div>

@push('js')
<script>
    function initFeatherIcons() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    document.addEventListener('DOMContentLoaded', initFeatherIcons);

    document.addEventListener('livewire:initialized', () => {
        initFeatherIcons();

        Livewire.hook('morphed', () => {
            setTimeout(() => {
                initFeatherIcons();
            }, 0);
        });

        Livewire.on('modalOpened', () => {
            setTimeout(() => {
                initFeatherIcons();
            }, 100);
        });
    });
</script>
@endpush
