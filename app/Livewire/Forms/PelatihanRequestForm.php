<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class PelatihanRequestForm extends Form
{
    #[Validate('required', message: 'harus diisi')]
    public $nama_institusi;

    #[Validate('required|date_format:d-m-Y', message: [
        'required' => 'harus diisi',
        'date_format' => 'format tanggal tidak valid',
    ])]
    public $tgl_mulai;

    #[Validate('required|date_format:d-m-Y', message: [
        'required' => 'harus diisi',
        'date_format' => 'format tanggal tidak valid',
    ])]
    public $tgl_selesai;

    #[Validate('required', message: 'harus diisi')]
    public $subjek_pelatihan;
}
