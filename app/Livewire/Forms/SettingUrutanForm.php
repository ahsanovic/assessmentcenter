<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class SettingUrutanForm extends Form
{
    #[Validate('required', message: 'harus dipilih')]
    public $alat_tes_id;
    
    #[Validate('required|numeric', message: [
        'urutan.required' => 'harus diisi',
        'urutan.numeric' => 'harus angka'
    ])]
    public $urutan;
}
