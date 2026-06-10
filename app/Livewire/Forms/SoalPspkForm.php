<?php

namespace App\Livewire\Forms;

use App\Models\Pspk\SoalPspk;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SoalPspkForm extends Form
{
    #[Validate('required', message: 'harus dipilih')]
    public $level_pspk_id;

    public $jenis_soal;

    #[Validate('required', message: 'harus dipilih')]
    public $aspek;

    #[Validate('required', message: 'harus diisi')]
    public $soal;

    #[Validate('required', message: 'harus diisi')]
    public $opsi_a;

    public $poin_opsi_a;

    #[Validate('required', message: 'harus diisi')]
    public $opsi_b;

    public $poin_opsi_b;

    #[Validate('required', message: 'harus diisi')]
    public $opsi_c;

    public $poin_opsi_c;

    #[Validate('required', message: 'harus diisi')]
    public $opsi_d;

    public $poin_opsi_d;

    #[Validate('required', message: 'harus diisi')]
    public $opsi_e;

    public $poin_opsi_e;

    public $kunci_jawaban;

    public $kasus_lampiran_id;

    public bool $editing = false;

    public function perluPaketKasusPdf(): bool
    {
        return in_array((int) $this->level_pspk_id, [3, 4], true)
            && (int) $this->jenis_soal === SoalPspk::JENIS_ANKAS;
    }

    protected function poinRules(): array
    {
        return (int) $this->level_pspk_id === 3
            ? ['required', 'integer', 'between:0,3']
            : ['nullable', 'integer', 'between:0,3'];
    }

    public function rules()
    {
        $lv34 = in_array((int) $this->level_pspk_id, [3, 4], true);

        $jenisRules = $lv34
            ? ['required', 'in:'.SoalPspk::JENIS_ANKAS.','.SoalPspk::JENIS_SJT]
            : ['prohibited'];

        $kasusRules = ['prohibited'];
        if ($this->perluPaketKasusPdf()) {
            $kasusRules = [
                'required',
                Rule::exists('pspk_kasus_lampiran', 'id')->where(
                    fn ($q) => $q->where('level_pspk_id', $this->level_pspk_id)
                ),
            ];
        }

        return [
            'jenis_soal' => $jenisRules,
            'poin_opsi_a' => $this->poinRules(),
            'poin_opsi_b' => $this->poinRules(),
            'poin_opsi_c' => $this->poinRules(),
            'poin_opsi_d' => $this->poinRules(),
            'poin_opsi_e' => $this->poinRules(),
            'kunci_jawaban' => $this->editing
                ? ['nullable']
                : ['required_if:level_pspk_id,1,2'],
            'kasus_lampiran_id' => $kasusRules,
        ];
    }

    public function messages()
    {
        $poinMsgs = [];

        foreach (['a', 'b', 'c', 'd', 'e'] as $suffix) {
            $key = 'poin_opsi_'.$suffix;
            $poinMsgs[$key.'.required'] = 'harus diisi';
            $poinMsgs[$key.'.integer'] = 'harus angka';
            $poinMsgs[$key.'.between'] = 'harus antara 0 sampai 3';
        }

        return array_merge($poinMsgs, [
            'jenis_soal.required' => 'pilih jenis soal (Ankas atau SJT) untuk level 3 atau 4',
            'jenis_soal.in' => 'jenis soal tidak valid',
            'jenis_soal.prohibited' => 'jenis Ankas/SJT hanya berlaku untuk level 3 atau 4',
            'kunci_jawaban.required_if' => 'harus dipilih',
            'kasus_lampiran_id.required' => 'pilih paket analisa kasus (PDF). Buat paket di menu PSPK jika belum ada.',
            'kasus_lampiran_id.exists' => 'paket kasus tidak valid untuk level ini',
            'kasus_lampiran_id.prohibited' => 'paket kasus hanya untuk level 3–4 jenis Analisa Kasus',
        ]);
    }
}
