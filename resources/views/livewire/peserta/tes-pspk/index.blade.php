<div>
    <x-breadcrumb :breadcrumbs="[
        ['url' => route('peserta.dashboard'), 'title' => 'Dashboard'],
        ['url' => null, 'title' => 'Tes PSPK'],
    ]" />
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row mt-3 justify-content-center">
                        <div class="col-sm-6 col-md-6 col-lg-4">
                            <div class="mb-3">
                                <form wire:submit="submit" class="d-flex align-items-center gap-2">
                                    <input
                                        wire:model.live.debounce="pin_ujian"
                                        class="form-control"
                                        placeholder="masukkan PIN ujian"
                                        autofocus
                                    />
                                    <button
                                        wire.model="submit"
                                        class="btn btn-success"
                                        @disabled(strlen($pin_ujian) !== 4)
                                    >
                                        Masuk
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
