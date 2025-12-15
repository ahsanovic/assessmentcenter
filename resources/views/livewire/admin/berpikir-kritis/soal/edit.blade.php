<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('admin.dashboard'), 'title' => 'Dashboard'],
        ['url' => route('admin.soal-berpikir-kritis'), 'title' => 'Berpikir Kritis dan Strategis'],
        ['url' => null, 'title' => 'Soal']
    ]" />
    <div class="row">
        <div class="col-md-12 stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Form Soal</h6>
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label for="jenis-indikator" class="form-label">Jenis Aspek</label>
                                    <select wire:model="form.aspek_id" class="form-select @error('form.aspek_id') is-invalid @enderror" id="jenis-indikator">
                                        <option value="">pilih jenis aspek</option>
                                        @foreach ($aspek_option as $key => $item)
                                            <option value="{{ $key }}">{{ $key . ' - ' . $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.aspek_id')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label for="jenis-indikator" class="form-label">Jenis Indikator</label>
                                    <select wire:model="form.indikator_nomor" class="form-select @error('form.indikator_nomor') is-invalid @enderror" id="jenis-indikator">
                                        <option value="">pilih jenis indikator</option>
                                        @foreach ($indikator_option as $key => $item)
                                            <option value="{{ $key }}">{{ $key . ' - ' . $item }}</option>
                                        @endforeach
                                    </select>
                                    @error('form.indikator_nomor')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi Soal</label>
                                    <textarea wire:model="form.soal" class="form-control @error('form.soal') is-invalid @enderror" rows="5"></textarea>
                                    @error('form.soal')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan A</label>
                                    <textarea wire:model="form.opsi_a" class="form-control @error('form.opsi_a') is-invalid @enderror" rows="5"></textarea>
                                    @error('form.opsi_a')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Poin</label>
                                    <input type="number" wire:model="form.poin_opsi_a" class="form-control @error('form.poin_opsi_a') is-invalid @enderror">
                                    @error('form.poin_opsi_a')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan B</label>
                                    <textarea wire:model="form.opsi_b" class="form-control @error('form.opsi_b') is-invalid @enderror" rows="5"></textarea>
                                    @error('form.opsi_b')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Poin</label>
                                    <input type="number" wire:model="form.poin_opsi_b" class="form-control @error('form.poin_opsi_b') is-invalid @enderror">
                                    @error('form.poin_opsi_b')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan C</label>
                                    <textarea wire:model="form.opsi_c" class="form-control @error('form.opsi_c') is-invalid @enderror" rows="5"></textarea>
                                    @error('form.opsi_c')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Poin</label>
                                    <input type="number" wire:model="form.poin_opsi_c" class="form-control @error('form.poin_opsi_c') is-invalid @enderror">
                                    @error('form.poin_opsi_c')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan D</label>
                                    <textarea wire:model="form.opsi_d" class="form-control @error('form.opsi_d') is-invalid @enderror" rows="5"></textarea>
                                    @error('form.opsi_d')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Poin</label>
                                    <input
                                        type="number"
                                        wire:model="form.poin_opsi_d"
                                        class="form-control @error('form.poin_opsi_d') is-invalid @enderror"
                                        placeholder="masukkan skor jawaban D"
                                    >
                                    @error('form.poin_opsi_d')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label class="form-label">Pilihan E</label>
                                    <textarea wire:model="form.opsi_e" class="form-control @error('form.opsi_e') is-invalid @enderror" rows="5"></textarea>
                                    @error('form.opsi_e')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                            <div class="col-sm-4">
                                <div class="mb-3">
                                    <label class="form-label">Poin</label>
                                    <input
                                        type="number"
                                        wire:model="form.poin_opsi_e"
                                        class="form-control @error('form.poin_opsi_e') is-invalid @enderror"
                                        placeholder="masukkan skor jawaban E"
                                    >
                                    @error('form.poin_opsi_e')
                                    <label class="error invalid-feedback">{{ $message }}</label>
                                    @enderror
                                </div>
                            </div><!-- Col -->
                        </div><!-- Row -->

                        <x-form-action :cancelUrl="route('admin.soal-berpikir-kritis')" :isUpdate="true" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>