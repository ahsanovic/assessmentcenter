<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class SettingWaktuTesForm extends Form
{
    #[Validate('required|numeric', message: [
        'waktu.required' => 'harus diisi',
        'waktu.numeric' => 'harus angka'
    ])]
    public $waktu;
    
    #[Validate('required', message: [
        'is_active.required' => 'harus dipilih'
    ])]
    public $is_active;
}
