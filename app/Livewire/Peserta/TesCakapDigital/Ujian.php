<?php

namespace App\Livewire\Peserta\TesCakapDigital;

use App\Models\CakapDigital\HasilCakapDigital;
use App\Models\CakapDigital\RefDescCakapDigital;
use App\Models\CakapDigital\SoalCakapDigital;
use App\Models\CakapDigital\UjianCakapDigital;
use App\Traits\PelanggaranTrait;
use App\Traits\TimerTrait;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.peserta.app', ['title' => 'Tes Cakap Digital'])]
class Ujian extends Component
{
    use TimerTrait, PelanggaranTrait;

    public $soal;
    public $jml_soal;
    public $id_soal;
    public $nomor_soal;
    public $jawaban_user = [];
    public $jawaban_kosong;
    public $id_ujian;
    public $timer;
    public $current_sequence;
    public $flagged = [];

    #[On('updateFlagsFromBrowser')]
    public function updateFlagsFromBrowser($flags)
    {
        $this->flagged = $flags;
    }

    public function toggleFlag($nomor)
    {
        $this->dispatch('toggle-flag-in-browser', nomor: $nomor);
        $this->dispatch('request-flags-sync');
    }

    public function mount($id)
    {
        $this->dispatch('load-flags-from-browser');
        $this->id_soal = $id;

        $data = UjianCakapDigital::select('id', 'soal_id', 'jawaban', 'created_at')
            ->where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->first();

        if (!$data) {
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Data ujian tidak ditemukan. Silakan mulai tes terlebih dahulu.'
            ]);
            return $this->redirect(route('peserta.tes-cakap-digital.home'), navigate: true);
        }

        $this->nomor_soal = explode(',', $data->soal_id);
        $this->jawaban_user = explode(',', $data->jawaban);
        $this->soal = SoalCakapDigital::find($this->nomor_soal[$this->id_soal - 1]);
        $this->jml_soal = SoalCakapDigital::count();
        $this->id_ujian = $data->id;
        $this->timerTest('Cakap Digital');

        for ($i = 0, $j = 0; $i < $this->jml_soal; $i++) {
            if ($this->jawaban_user[$i] == '0') {
                $j = $j + 1;
                $this->jawaban_kosong = $j;
            }
        }
    }

    public function render()
    {
        return view('livewire.peserta.tes-cakap-digital.ujian', [
            'nomor_sekarang' => $this->id_soal,
            'jawaban' => $this->jawaban_user,
            'jawaban_kosong' => $this->jawaban_kosong,
            'jml_soal' => $this->jml_soal,
            'soal' => $this->soal,
        ]);
    }

    public function saveAndNext($nomor_soal, $jawaban = null)
    {
        $index_array = $nomor_soal - 1;
        $data = UjianCakapDigital::where('peserta_id', activePesertaId())
            ->where('event_id', activeEventId())
            ->where('is_finished', 'false')
            ->first();

        if (!$data) {
            $this->skipRender();
            return null;
        }

        $jawaban_user = explode(',', $data->jawaban);
        $jawaban_baru = $jawaban ?? ($this->jawaban_user[$index_array] ?? '0');
        $jawaban_baru = ($jawaban_baru === '' || $jawaban_baru === null) ? '0' : $jawaban_baru;

        $jawaban_user[$index_array] = $jawaban_baru;
        $data->jawaban = implode(',', $jawaban_user);
        $data->save();

        $this->jawaban_user = $jawaban_user;
        $this->jawaban_kosong = collect($this->jawaban_user)->filter(fn ($j) => $j == '0')->count();

        $target = $nomor_soal < $this->jml_soal ? $nomor_soal + 1 : $nomor_soal;

        $this->skipRender();

        $payload = $this->soalPayload($target);

        // Popup di soal terakhir: baik semua terjawab maupun masih ada yang kosong
        if ((int) $nomor_soal === (int) $this->jml_soal) {
            $payload['prompt_soal_terakhir'] = true;
            $payload['semua_terjawab'] = (int) $this->jawaban_kosong === 0;
            $payload['jawaban_kosong'] = (int) $this->jawaban_kosong;
            $payload['soal_belum_dijawab'] = $this->indeksPertamaBelumDijawab();
        }

        return $payload;
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

    public function navigate($id)
    {
        if ($id < 1 || $id > $this->jml_soal) {
            $this->skipRender();
            return null;
        }

        $this->skipRender();

        return $this->soalPayload((int) $id);
    }

    private function soalPayload(int $id): array
    {
        $this->id_soal = $id;
        $this->soal = SoalCakapDigital::find($this->nomor_soal[$id - 1]);

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
            'url' => route('peserta.tes-cakap-digital.ujian', ['id' => $id]),
        ];
    }

    public function finish()
    {
        try {
            $data = UjianCakapDigital::findOrFail($this->id_ujian);

            $this->recalculateScores($data);
            $data->refresh();

            // scoring kategori
            $kategori_literasi = $this->_getKategori($data->nilai_literasi);
            $kategori_emerging = $this->_getKategori($data->nilai_emerging);

            // jpm
            $jpm_literasi = ($data->nilai_literasi / 60) * 100;
            $jpm_emerging = ($data->nilai_emerging / 60) * 100;

            // kesimpulan
            $kesimpulan_literasi = $this->_getKategoriJpm($jpm_literasi);
            $kesimpulan_emerging = $this->_getKategoriJpm($jpm_emerging);

            // deskripsi
            $deskripsi_literasi = RefDescCakapDigital::where('kompetensi', 'Literasi Digital')->first();
            $deskripsi_emerging = RefDescCakapDigital::where('kompetensi', 'Emerging Skill')->first();

            // Mapping kolom berdasarkan kesimpulan
            $map = [
                'Kurang Optimal' => 'kurang_optimal',
                'Cukup Optimal'  => 'cukup_optimal',
                'Optimal'        => 'optimal',
            ];

            // Ambil teks deskripsi
            $deskripsi_literasi_text = $deskripsi_literasi->{$map[$kesimpulan_literasi] ?? null} ?? null;
            $deskripsi_emerging_text = $deskripsi_emerging->{$map[$kesimpulan_emerging] ?? null} ?? null;

            HasilCakapDigital::updateOrCreate(
                [
                    'event_id' => activeEventId(),
                    'peserta_id' => activePesertaId(),
                    'ujian_id' => $data->id,
                ],
                [
                    'nilai_literasi' => $data->nilai_literasi,
                    'kategori_literasi' => $kategori_literasi,
                    'nilai_emerging' => $data->nilai_emerging,
                    'kategori_emerging' => $kategori_emerging,
                    'jpm_literasi' => $jpm_literasi,
                    'jpm_emerging' => $jpm_emerging,
                    'kesimpulan_literasi' => $kesimpulan_literasi,
                    'kesimpulan_emerging' => $kesimpulan_emerging,
                    'deskripsi_literasi' => $deskripsi_literasi_text,
                    'deskripsi_emerging' => $deskripsi_emerging_text,
                ]
            );

            // change status ujian to true (finish)
            $data->is_finished = true;
            $data->save();

            // Bersihkan localStorage via JS
            $this->dispatch('clear-flags-browser');

            return $this->redirect(route('peserta.tes-cakap-digital.hasil'));
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan'
            ]);
        }
    }

    private function recalculateScores(UjianCakapDigital $data): void
    {
        $soalIds = explode(',', (string) $data->soal_id);
        $jawabanUser = explode(',', (string) $data->jawaban);
        $soals = SoalCakapDigital::whereIn('id', $soalIds)->get()->keyBy('id');

        $nilaiLiterasi = 0;
        $nilaiEmerging = 0;

        foreach ($soalIds as $idx => $soalId) {
            $jawaban = $jawabanUser[$idx] ?? '0';
            $soal = $soals[$soalId] ?? null;

            if (!$soal || $jawaban === '0' || $soal->kunci_jawaban != $jawaban) {
                continue;
            }

            if ($idx < 60) {
                $nilaiLiterasi++;
            } else {
                $nilaiEmerging++;
            }
        }

        $data->nilai_literasi = $nilaiLiterasi;
        $data->nilai_emerging = $nilaiEmerging;
        $data->save();
    }

    private function _getKategoriJpm($nilai_jpm)
    {
        if ($nilai_jpm < 78) {
            $kategori = 'Kurang Optimal';
        } else if ($nilai_jpm >= 78 && $nilai_jpm < 90) {
            $kategori = 'Cukup Optimal';
        } else if ($nilai_jpm >= 90) {
            $kategori = 'Optimal';
        }

        return $kategori;
    }

    private function _getKategori($nilai)
    {
        $kategori_map = [
            [0, 4, '1-'],
            [5, 8, '1'],
            [9, 12, '1+'],
            [13, 16, '2-'],
            [17, 20, '2'],
            [21, 24, '2+'],
            [25, 28, '3-'],
            [29, 32, '3'],
            [33, 36, '3+'],
            [37, 40, '4-'],
            [41, 44, '4'],
            [45, 48, '4+'],
            [49, 52, '5-'],
            [53, 56, '5'],
            [57, 60, '5+'],
        ];

        foreach ($kategori_map as [$min, $max, $label]) {
            if ($nilai >= $min && $nilai <= $max) {
                return $label;
            }
        }

        return null;
    }
}
