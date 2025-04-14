<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class NomorLaporanForm extends Form
{
    #[Validate('required', message: 'harus dipilih')]
    public $event_id;

    #[Validate('required', message: 'harus diisi')]
    public $nomor;

     #[Validate('required|date_format:d-m-Y', message: [
        'required' => 'harus diisi',
        'date_format' => 'format tanggal tidak valid',
    ])]
    public $tanggal;
}
