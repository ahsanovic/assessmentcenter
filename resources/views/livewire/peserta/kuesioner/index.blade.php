@push('css')
<style>
    .form-check-input[type="checkbox"] {
        border: 2px solid #dee2e6;
    }
    .form-check-input[type="radio"] {
        width: 1.2em;
        height: 1.2em;
        cursor: pointer;
    }
    .form-check-input[type="radio"]:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .question-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    .question-card:hover {
        border-left-color: #0d6efd;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .rating-label {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .rating-container {
        background: linear-gradient(90deg, #fee2e2 0%, #fef3c7 25%, #d1fae5 50%, #cffafe 75%, #ddd6fe 100%);
        border-radius: 50px;
        padding: 0.75rem 1.5rem;
    }
    .rating-number {
        font-weight: 600;
        color: #374151;
    }
</style>
@endpush

<div>
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient" 
                style="
                    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                    color: #23272f;
                "
                >
                <div class="card-body p-4" 
                    style="
                        color: #23272f; 
                        background: transparent;
                    ">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 me-3"
                            style="
                                background: rgba(103, 126, 234, 0.13);
                                color: #5f39af;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                box-shadow: 0 2px 8px rgba(110,88,213,.07);
                            "
                        >
                            <i data-feather="clipboard" style="width: 32px; height: 32px;"></i>
                        </div>
                        <div>
                            <h3 class="mb-1" style="color: #3c3264; font-weight: 700;">
                                Kuesioner Evaluasi
                            </h3>
                            <p class="mb-0" style="color: #585e74; opacity: .85; font-weight: 500;">
                                Penilaian Kompetensi
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm border-start border-4 border-info">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="me-3">
                            <i class="text-info" data-feather="info" style="width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <h6 class="mb-2 text-info">Petunjuk Pengisian</h6>
                            <p class="mb-0 text-muted">Berikan penilaian Anda terhadap pelaksanaan Penilaian Kompetensi (Uji Kompetensi) yang telah diikuti dengan cara memilih rentang jawaban yang telah disediakan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kuesioner Form -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form wire:submit="submit">
                @foreach ($kuesioner as $key => $item)
                    <div class="question-card card mb-4 border-0 bg-light rounded-3">
                        <div class="card-body p-4">
                            @if ($item->is_esai == 'f')
                                <!-- Rating Question -->
                                <div class="d-flex align-items-start mb-3">
                                    <span class="badge bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        {{ $loop->iteration }}
                                    </span>
                                    <h6 class="mb-0 flex-grow-1">{{ $item->deskripsi }}</h6>
                                </div>
                                <div class="d-flex flex-column flex-md-row align-items-center justify-content-center mt-4">
                                    <span class="rating-label me-md-3 mb-2 mb-md-0">Sangat Tidak Setuju</span>
                                    <div class="rating-container d-flex align-items-center gap-3">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <div class="form-check form-check-inline m-0">
                                                <input 
                                                    class="form-check-input" 
                                                    type="radio"
                                                    wire:model.defer="jawaban_responden.{{ $item->id }}.skor" 
                                                    value="{{ $i }}"
                                                    id="rating_{{ $item->id }}_{{ $i }}"
                                                >
                                                <label class="form-check-label rating-number" for="rating_{{ $item->id }}_{{ $i }}">
                                                    {{ $i }}
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
                                    <span class="rating-label ms-md-3 mt-2 mt-md-0">Sangat Setuju</span>
                                </div>
                            @else
                                <!-- Essay Question -->
                                <div class="d-flex align-items-start mb-3">
                                    <span class="badge bg-success rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        {{ $loop->iteration }}
                                    </span>
                                    <h6 class="mb-0 flex-grow-1">{{ $item->deskripsi }}</h6>
                                </div>
                                <div class="mt-3">
                                    <textarea 
                                        class="form-control border-0 shadow-sm" 
                                        wire:model.defer="jawaban_responden.{{ $item->id }}.jawaban_esai" 
                                        rows="4"
                                        placeholder="Tulis jawaban Anda di sini..."
                                    ></textarea>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- Confirmation Checkbox -->
                <div class="card border-0 bg-warning bg-opacity-10 rounded-3 mb-4">
                    <div class="card-body p-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="konfirmasi" wire:model="konfirmasi" style="width: 1.25em; height: 1.25em;">
                            <label class="form-check-label ms-2" for="konfirmasi">
                                <strong>Saya telah mengisi semua pertanyaan dengan jujur</strong>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button class="btn btn-primary btn-lg px-5" id="submit" wire:click.prevent="submit" disabled>
                        <i class="me-2" data-feather="send"></i>
                        Kirim Jawaban
                    </button>
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
