<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class SettingsForm extends Form
{
    #[Validate('required', message: 'harus dipilih')]
    public $alat_tes_id;
    
    #[Validate('required|numeric', message: [
        'waktu.required' => 'harus diisi',
        'waktu.numeric' => 'harus angka'
    ])]
    public $waktu;
}
