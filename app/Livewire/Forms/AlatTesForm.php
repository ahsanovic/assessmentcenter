<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class AlatTesForm extends Form
{
    #[Validate('required', message: 'harus diisi')]
    public $alat_tes;

    #[Validate('required', message: 'harus diisi')]
    public $definisi_aspek_potensi;
}
