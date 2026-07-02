<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class PegawaiForm extends Form
{
    #[Validate('required|string|max:255', message: 'harus diisi')]
    public $nama;

    #[Validate('required|string|size:18', message: 'harus 18 digit')]
    public $nip;
}
