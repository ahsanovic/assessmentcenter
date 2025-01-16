<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class SoalKecerdasanEmosiForm extends Form
{
    #[Validate('required', message: 'harus dipilih')]
    public $jenis_indikator_id;

    #[Validate('required', message: 'harus diisi')]
    public $soal;
    
    #[Validate('required', message: 'harus diisi')]
    public $opsi_a;
    
    #[Validate('required', message: 'harus diisi')]
    public $opsi_b;

    #[Validate('required', message: 'harus diisi')]
    public $opsi_c;

    #[Validate('required', message: 'harus diisi')]
    #[Validate('numeric', message: ['harus angka'])]
    public $poin_opsi_a;
    
    #[Validate('required', message: 'harus diisi')]
    #[Validate('numeric', message: 'harus angka')]
    public $poin_opsi_b;

    #[Validate('required', message: 'harus diisi')]
    #[Validate('numeric', message: 'harus angka')]
    public $poin_opsi_c;
}
