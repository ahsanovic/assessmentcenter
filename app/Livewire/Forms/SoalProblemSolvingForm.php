<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class SoalProblemSolvingForm extends Form
{
    #[Validate('required', message: 'harus dipilih')]
    public $aspek_id;
    
    #[Validate('required', message: 'harus dipilih')]
    public $indikator_nomor;

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

    #[Validate('required', message: 'harus diisi')]
    #[Validate('numeric', message: ['harus angka'])]
    public $poin_opsi_a;
    
    #[Validate('required', message: 'harus diisi')]
    #[Validate('numeric', message: 'harus angka')]
    public $poin_opsi_b;

    #[Validate('required', message: 'harus diisi')]
    #[Validate('numeric', message: 'harus angka')]
    public $poin_opsi_c;
    
    #[Validate('required', message: 'harus diisi')]
    #[Validate('numeric', message: 'harus angka')]
    public $poin_opsi_d;
    
    #[Validate('required', message: 'harus diisi')]
    #[Validate('numeric', message: 'harus angka')]
    public $poin_opsi_e;
}
