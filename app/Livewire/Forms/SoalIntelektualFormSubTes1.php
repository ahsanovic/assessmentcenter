<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class SoalIntelektualFormSubTes1 extends Form
{
    #[Validate('required', message: 'harus dipilih')]
    public $model_id;

    #[Validate('required', message: 'harus diisi')]
    public $soal;

    #[Validate('required', message: 'harus diisi')]
    public $opsi_a;

    #[Validate('required', message: 'harus diisi')]
    public $opsi_b;

    #[Validate('required', message: 'harus diisi')]
    public $opsi_c;

    #[Validate('required', message: 'harus diisi')]
    public $opsi_d;

    #[Validate('required', message: 'harus diisi')]
    public $opsi_e;

    #[Validate('required', message: 'harus dipilih')]
    public $kunci_jawaban;
}
