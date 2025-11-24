@push('css')
    <style>
        .form-check-input[type="checkbox"] {
            border: 2px solid #dee2e6;
        }
    </style>
@endpush
<div>
    <div class="row mb-4">
        <div class="col">
            <h3 class="text-center">Kuesioner Evaluasi Penilaian Kompetensi</h3>
        </div>
    </div>

    <x-alert :type="'danger'" :teks="'Berikan penilaian Anda terhadap pelaksanaan Penilaian Kompetensi (Uji Kompetensi) yang telah diikuti dengan cara memilih rentang jawaban yang telah disediakan.'" />

    <div class="card mt-3 mb-3">
        <div class="card-body">
            <form wire:submit="submit">
                @foreach ($kuesioner as $key => $item)
                    @if ($item->is_esai == 'f')
                    <div class="row">
                        <div class="col-md-6 col-lg-12">
                            <div class="row mb-2">
                                <div class="col-md-12 col-lg-12 mb-3">
                                    <h5>{{ $item->deskripsi }}</h5>
                                </div>
                            </div>
                            <div class="row d-flex justify-content-center mb-4">
                                <div class="col-md-12 col-lg-12 mb-3">
                                    <label class="me-3">Sangat Tidak Setuju</label>
                                    @for ($i = 1; $i <= 5; $i++)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                            wire:model.defer="jawaban_responden.{{ $item->id }}.skor" value="{{ $i }}"
                                        >
                                        {{ $i }}
                                    </div>
                                    @endfor
                                    <label class="me-3">Sangat Setuju</label>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="row">
                            <div class="col-md-12 col-lg-12 mb-3">
                                <h5>{{ $item->deskripsi }}</h5>
                            </div>
                            <div class="col-md-12 col-lg-12 mb-3">
                                <textarea class="form-control" wire:model.defer="jawaban_responden.{{ $item->id }}.jawaban_esai" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
                <div class="row mt-4">
                    <div class="col-md-12 col-lg-12">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="konfirmasi" wire:model="konfirmasi">
                            <label class="form-check-label" for="konfirmasi">
                                Saya telah mengisi semua pertanyaan dengan jujur
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12 col-lg-12">
                        <button class="btn btn-primary" id="submit" wire:click.prevent="submit" disabled>Kirim</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@push('js')
    <script>
        $(document).ready(function() {
            $('#konfirmasi').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#submit').removeAttr('disabled');
                } else {
                    $('#submit').attr('disabled', 'disabled');
                }
            });
        });
    </script>
@endpush
