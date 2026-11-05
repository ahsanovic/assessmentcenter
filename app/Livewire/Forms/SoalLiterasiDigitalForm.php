<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class SoalLiterasiDigitalForm extends Form
{
    #[Validate('required|exists:ref_pilar_literasi_digital,id', message: 'harus dipilih')]
    public $pilar_id;

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

    #[Validate('required|in:A,B,C,D,E', message: 'harus dipilih')]
    public $kunci_jawaban;

    #[Validate('required|in:true,false', message: 'harus dipilih')]
    public $is_active = 'true';
}
