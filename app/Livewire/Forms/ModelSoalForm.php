<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class ModelSoalForm extends Form
{
    #[Validate('required', message: 'harus diisi')]
    public $jenis;

    public $is_active;
}
