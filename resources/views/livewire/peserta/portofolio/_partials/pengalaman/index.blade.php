<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('peserta.portofolio'), 'title' => 'Portofolio'],
        ['url' => null, 'title' => 'Pengalaman Spesifik'],
    ]" />
    <x-alert :type="'danger'" :teks="'Lengkapi portofolio Anda dengan detail yang mencerminkan perjalanan profesional dan pribadi Anda secara menyeluruh.
        Mulai dari biodata, pendidikan, pelatihan yang telah diikuti, riwayat karir, pengalaman kerja, hingga penilaian pribadi. 
        Tunjukkan potensi terbaik Anda dan ciptakan kesan mendalam melalui portofolio yang informatif dan menarik!'" />
    <div class="row">
        <div class="row">
            <x-tab-nav :nav="[
                ['url' => route('peserta.biodata'), 'title' => 'Biodata', 'active' => null],
                ['url' => route('peserta.pendidikan'), 'title' => 'Pendidikan', 'active' => null],
                ['url' => route('peserta.pelatihan'), 'title' => 'Pelatihan', 'active' => null],
                ['url' => route('peserta.karir'), 'title' => 'Karir', 'active' => null],
                ['url' => route('peserta.pengalaman'), 'title' => 'Pengalaman Spesifik', 'active' => 'active'],
                ['url' => route('peserta.penilaian'), 'title' => 'Penilaian Pribadi', 'active' => null],
            ]" />
            <div class="col-8 col-md-10 ps-0">
                <div class="tab-content tab-content-vertical border p-3" id="v-tabContent">
                    <div class="tab-pane fade show active" role="tabpanel">
                        <h5 class="mb-4">Pengalaman Spesifik</h5>
                        <form wire:submit="save">
                            @foreach ($pertanyaan as $index => $item)
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="mb-1">
                                            <label class="form-label">{{ $item->pertanyaan }}</label>
                                        </div>
                                    </div><!-- Col -->
                                </div><!-- Row -->
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="mb-4" wire:ignore>
                                            <div id="editor-{{ $index }}"></div>
                                            <livewire:quill-text-editor wire:model="jawaban.{{ $index }}"
                                                theme="snow" />
                                        </div>
                                    </div><!-- Col -->
                                </div><!-- Row -->
                            @endforeach

                            <button type="submit" class="btn btn-sm btn-inverse-success">
                                Simpan Semua Jawaban
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
