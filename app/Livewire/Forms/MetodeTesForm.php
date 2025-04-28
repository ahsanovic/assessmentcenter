<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class MetodeTesForm extends Form
{
    #[Validate('required', message: 'harus diisi')]
    public $metode_tes;
}
