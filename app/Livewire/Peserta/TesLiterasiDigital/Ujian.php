<?php

namespace App\Livewire\Peserta\TesLiterasiDigital;

use App\Models\LiterasiDigital\JawabanLiterasiDigital;
use App\Models\LiterasiDigital\SoalLiterasiDigital;
use App\Models\LiterasiDigital\UjianLiterasiDigital;
use App\Services\LiterasiDigital\LiterasiDigitalUjianService;
use App\Traits\PelanggaranTrait;
use App\Traits\TimerTrait;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Literasi Digital'])]
class Ujian extends Component
{
    use PelanggaranTrait, TimerTrait;

    public $soal;

    public $jml_soal;

    public $id_soal;

    public $nomor_soal = [];

    public $jawaban_user = [];

    public $jawaban_kosong;

    public $id_ujian;

    public $timer;

    public function mount($id)
    {
        $this->id_soal = (int) $id;

        $data = UjianLiterasiDigital::query()
            ->where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->first();

        if (! $data) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Data ujian tidak ditemukan. Silakan mulai tes terlebih dahulu.',
            ]);

            return $this->redirect(route('peserta.tes-literasi-digital.home'), navigate: true);
        }

        if ($data->is_finished === 'true') {
            return $this->redirect(route('peserta.tes-literasi-digital.hasil'), navigate: true);
        }

        $this->muatLembar($data);

        if ($this->id_soal < 1 || $this->id_soal > $this->jml_soal) {
            return $this->redirect(route('peserta.tes-literasi-digital.ujian', ['id' => 1]), navigate: true);
        }

        $this->soal = SoalLiterasiDigital::find($this->nomor_soal[$this->id_soal - 1]);
        $this->id_ujian = $data->id;
        $this->timerTest('Literasi Digital');
        $this->hitungKosong();
    }

    public function render()
    {
        return view('livewire.peserta.tes-literasi-digital.ujian', [
            'nomor_sekarang' => $this->id_soal,
            'jawaban_user' => $this->jawaban_user,
            'jawaban_kosong' => $this->jawaban_kosong,
            'jml_soal' => $this->jml_soal,
            'soal' => $this->soal,
        ]);
    }

    public function saveAndNext($nomor_soal, $jawaban = null, LiterasiDigitalUjianService $ujianService)
    {
        $data = $this->ujianBerlangsung();

        if (! $data) {
            $this->skipRender();

            return null;
        }

        $this->muatLembar($data);
        $jawabanBaru = $jawaban ?? ($this->jawaban_user[$nomor_soal - 1] ?? '0');
        $ujianService->simpanJawaban($data, (int) $nomor_soal, $jawabanBaru);
        $this->muatLembar($data->fresh());

        $target = $nomor_soal < $this->jml_soal ? $nomor_soal + 1 : $nomor_soal;
        $this->skipRender();
        $payload = $this->soalPayload($target);

        if ((int) $nomor_soal === (int) $this->jml_soal) {
            $payload['prompt_soal_terakhir'] = true;
            $payload['semua_terjawab'] = (int) $this->jawaban_kosong === 0;
            $payload['jawaban_kosong'] = (int) $this->jawaban_kosong;
            $payload['soal_belum_dijawab'] = $this->indeksPertamaBelumDijawab();
        }

        return $payload;
    }

    public function navigate($id)
    {
        if ($id < 1 || $id > $this->jml_soal) {
            $this->skipRender();

            return null;
        }

        $this->skipRender();

        return $this->soalPayload((int) $id);
    }

    public function finish(LiterasiDigitalUjianService $ujianService)
    {
        try {
            $data = $this->ujianBerlangsung();

            if (! $data) {
                return $this->redirect(route('peserta.tes-literasi-digital.hasil'));
            }

            $ujianService->selesai($data);
            $this->dispatch('clear-flags-browser');

            return $this->redirect(route('peserta.tes-literasi-digital.hasil'));
        } catch (\Throwable $th) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan',
            ]);
        }
    }

    private function ujianBerlangsung(): ?UjianLiterasiDigital
    {
        return UjianLiterasiDigital::query()
            ->where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->where('is_finished', 'false')
            ->first();
    }

    private function muatLembar(UjianLiterasiDigital $ujian): void
    {
        $baris = JawabanLiterasiDigital::query()
            ->where('ujian_id', $ujian->id)
            ->orderBy('urutan')
            ->get(['soal_id', 'jawaban', 'urutan']);

        $this->nomor_soal = $baris->pluck('soal_id')->all();
        $this->jawaban_user = $baris->map(fn ($item) => $item->jawaban ?: '0')->all();
        $this->jml_soal = count($this->nomor_soal);
        $this->hitungKosong();
    }

    private function hitungKosong(): void
    {
        $this->jawaban_kosong = collect($this->jawaban_user)->filter(fn ($jawaban) => $jawaban == '0')->count();
    }

    private function indeksPertamaBelumDijawab(): int
    {
        foreach ($this->jawaban_user as $idx => $jawaban) {
            if ((string) $jawaban === '0' || $jawaban === '' || $jawaban === null) {
                return $idx + 1;
            }
        }

        return 1;
    }

    private function soalPayload(int $id): array
    {
        $this->id_soal = $id;
        $this->soal = SoalLiterasiDigital::find($this->nomor_soal[$id - 1]);
        $jawaban = (string) ($this->jawaban_user[$id - 1] ?? '');
        $selected = ($jawaban !== '' && $jawaban !== '0') ? $jawaban : '';

        return [
            'nomor' => $id,
            'teks' => $this->soal?->soal,
            'opsi_a' => $this->soal?->opsi_a,
            'opsi_b' => $this->soal?->opsi_b,
            'opsi_c' => $this->soal?->opsi_c,
            'opsi_d' => $this->soal?->opsi_d,
            'opsi_e' => $this->soal?->opsi_e,
            'selected' => $selected,
            'jawaban_user' => array_values($this->jawaban_user),
            'jawaban_kosong' => (int) $this->jawaban_kosong,
            'url' => route('peserta.tes-literasi-digital.ujian', ['id' => $id]),
        ];
    }
}
