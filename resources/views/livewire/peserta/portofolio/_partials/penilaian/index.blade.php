<div>
    <!-- Breadcrumb -->
    <div class="d-flex justify-content-end">
        <x-breadcrumb :breadcrumbs="[
            ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
            ['url' => route('peserta.portofolio'), 'title' => 'Portofolio'],
            ['url' => null, 'title' => 'Penilaian Pribadi'],
        ]" />
    </div>

    <!-- Header Card -->
    <div class="card border-0 shadow-sm mb-4" 
        style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
        <div class="card-body p-4">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-3 me-3"
                    style="background: rgba(102, 126, 234, 0.13); color: #667eea;" wire:ignore>
                    <i data-feather="folder" style="width: 32px; height: 32px;"></i>
                </div>
                <div>
                    <h3 class="mb-1" style="color: #3c3264; font-weight: 700;">
                        Kelengkapan Portofolio
                    </h3>
                    <p class="mb-0" style="color: #585e74; opacity: .85; font-weight: 500;">
                        Lengkapi data diri Anda dengan teliti
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="card border-0 shadow-sm mb-4 border-start border-4 border-info">
        <div class="card-body p-3">
            <div class="d-flex align-items-start">
                <div class="rounded-circle bg-info bg-opacity-10 p-2 me-3 flex-shrink-0" wire:ignore>
                    <i data-feather="info" class="text-info" style="width: 20px; height: 20px;"></i>
                </div>
                <div>
                    <strong class="text-info d-block mb-1">Petunjuk Pengisian</strong>
                    <small class="text-muted">
                        Lengkapi portofolio Anda dengan detail yang mencerminkan perjalanan profesional dan pribadi Anda secara menyeluruh.
                        Mulai dari biodata, pendidikan, pelatihan yang telah diikuti, riwayat karir, pengalaman kerja, hingga penilaian pribadi.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="row">
            <x-tab-nav :nav="[
                ['url' => route('peserta.biodata'), 'title' => 'Biodata', 'active' => null],
                ['url' => route('peserta.pendidikan'), 'title' => 'Pendidikan', 'active' => null],
                ['url' => route('peserta.pelatihan'), 'title' => 'Pelatihan', 'active' => null],
                ['url' => route('peserta.karir'), 'title' => 'Karir', 'active' => null],
                ['url' => route('peserta.pengalaman'), 'title' => 'Pengalaman Spesifik', 'active' => null],
                ['url' => route('peserta.penilaian'), 'title' => 'Penilaian Pribadi', 'active' => 'active'],
            ]" />
            <div class="col-8 col-md-10 ps-0">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-2 me-3" style="background: rgba(111, 66, 193, 0.1);" wire:ignore>
                                <i data-feather="user-check" style="width: 20px; height: 20px; color: #6f42c1;"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-semibold">Penilaian Pribadi</h5>
                                <small class="text-muted">Refleksi dan penilaian diri Anda</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form wire:submit="save">
                            @foreach ($pertanyaan as $index => $item)
                                <div class="card border mb-4" style="border-color: #e9ecef !important;">
                                    <div class="card-header py-3" style="background: rgba(111, 66, 193, 0.05);">
                                        <div class="d-flex align-items-start">
                                            <span class="badge rounded-pill me-3" style="background-color: #6f42c1;">{{ $index + 1 }}</span>
                                            <label class="form-label fw-medium mb-0">{{ $item->pertanyaan }}</label>
                                        </div>
                                    </div>
                                    <div class="card-body p-3" wire:ignore>
                                        <livewire:quill-text-editor wire:model="jawaban.{{ $index }}"
                                            theme="snow" class="quill-text-editor" />
                                    </div>
                                </div>
                            @endforeach

                            <hr class="my-4">

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn text-white" style="background-color: #6f42c1;">
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
