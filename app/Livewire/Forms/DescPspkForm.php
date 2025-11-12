<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class DescPspkForm extends Form
{
    #[Validate('required', message: 'harus dipilih')]
    public $level_pspk;

    #[Validate('required', message: 'harus dipilih')]
    public $aspek;

    #[Validate('required', message: 'harus diisi')]
    public $deskripsi_min;

    #[Validate('required', message: 'harus diisi')]
    public $deskripsi;

    #[Validate('required', message: 'harus diisi')]
    public $deskripsi_plus;
}
