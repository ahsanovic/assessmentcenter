<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class KuesionerForm extends Form
{
    #[Validate('required', message: 'harus diisi')]
    public $deskripsi;

    #[Validate('required', message: 'harus dipilih')]
    public $is_esai;
    
    #[Validate('required', message: 'harus dipilih')]
    public $is_active;
}
