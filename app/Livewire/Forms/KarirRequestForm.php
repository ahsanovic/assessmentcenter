<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class KarirRequestForm extends Form
{
    #[Validate('required', message: 'harus diisi')]
    public $bulan_mulai;

    #[Validate('required', message: 'harus diisi')]
    public $bulan_selesai;

    #[Validate('required|numeric|digits:4|min:1990', message: [
        'required' => 'harus diisi',
        'numeric' => 'harus angka',
        'digits' => 'tahun harus 4 digit',
        'min' => 'tahun tidak valid'
    ])]
    public $tahun_mulai;

    #[Validate('required|numeric|digits:4|min:1990', message: [
        'required' => 'harus diisi',
        'numeric' => 'harus angka',
        'digits' => 'tahun harus 4 digit',
        'min' => 'tahun tidak valid'
    ])]
    public $tahun_selesai;

    #[Validate('required', message: 'harus diisi')]
    public $instansi;

    #[Validate('required', message: 'harus diisi')]
    public $jabatan;

    #[Validate('required', message: 'harus diisi')]
    public $uraian_tugas;
}
