<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class SoalKompetensiTeknisForm extends Form
{
    #[Validate('required', message: 'harus dipilih')]
    public $jenis_jabatan;

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

    #[Validate('required', message: 'harus dipilih')]
    public $kunci_jawaban;
}
