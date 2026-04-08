<div>
    <!-- Breadcrumb -->
    <div class="d-flex justify-content-end">
        <x-breadcrumb :breadcrumbs="[
            ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
            ['url' => route('peserta.portofolio'), 'title' => 'Portofolio'],
            ['url' => null, 'title' => 'Pengalaman Spesifik'],
        ]" />
    </div>

    <!-- Header Card -->
    <x-portofolio.header-card />

    <x-portofolio.progress :progress="$portofolioProgress" />

    <div class="row">
        <div class="row">
            <x-portofolio.tab-nav :nav="[
                ['url' => route('peserta.biodata'), 'title' => 'Biodata', 'active' => null, 'icon' => 'user', 'color' => 'primary'],
                ['url' => route('peserta.pendidikan'), 'title' => 'Pendidikan', 'active' => null, 'icon' => 'book', 'color' => 'success'],
                ['url' => route('peserta.pelatihan'), 'title' => 'Pelatihan', 'active' => null, 'icon' => 'award', 'color' => 'warning'],
                ['url' => route('peserta.karir'), 'title' => 'Karir', 'active' => null, 'icon' => 'briefcase', 'color' => 'info'],
                ['url' => route('peserta.pengalaman'), 'title' => 'Pengalaman Spesifik', 'active' => 'active', 'icon' => 'star', 'color' => 'danger'],
                ['url' => route('peserta.penilaian'), 'title' => 'Penilaian Pribadi', 'active' => null, 'icon' => 'user-check', 'color' => 'primary'],
            ]" />
            <div class="col-8 col-md-10 ps-0">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-2 me-3" style="background: rgba(13, 110, 253, 0.1);" wire:ignore>
                                <i data-feather="star" class="text-primary" style="width: 20px; height: 20px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-semibold">Pengalaman Spesifik</h5>
                                <small class="text-muted">Ceritakan pengalaman kerja spesifik Anda</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form wire:submit="save">
                            @foreach ($pertanyaan as $index => $item)
                                <div class="card border mb-4" style="border-color: #e9ecef !important;">
                                    <div class="card-header bg-light py-3">
                                        <div class="d-flex align-items-start">
                                            <span class="badge bg-primary rounded-pill me-3">{{ $index + 1 }}</span>
                                            <label class="form-label fw-medium mb-0">{{ $item->pertanyaan }}</label>
                                        </div>
                                    </div>
                                    <div class="card-body p-3" wire:ignore>
                                        <livewire:quill-text-editor wire:model="jawaban.{{ $index }}"
                                            theme="snow" />
                                    </div>
                                </div>
                            @endforeach

                            <hr class="my-4">

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <span wire:ignore><i data-feather="save" style="width: 16px; height: 16px;" class="me-1"></i></span>
                                    Simpan Semua Jawaban
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
