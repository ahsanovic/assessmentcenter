<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class PendidikanRequestForm extends Form
{
    #[Validate('required', message: 'harus dipilih')]
    public $jenjang_pendidikan_id;
    
    #[Validate('required', message: 'harus diisi')]
    public $nama_sekolah;

    #[Validate('required|numeric|digits:4|min:1970', message: [
        'required' => 'harus diisi',
        'numeric' => 'harus angka',
        'digits' => 'tahun harus 4 digit',
        'min' => 'tahun tidak valid'
    ])]
    public $thn_masuk;

    #[Validate('required|numeric|digits:4|min:1970', message: [
        'required' => 'harus diisi',
        'numeric' => 'harus angka',
        'digits' => 'tahun harus 4 digit',
        'min' => 'tahun tidak valid'
    ])]
    public $thn_lulus;

    #[Validate('required', message: 'harus diisi')]
    public $jurusan;

    #[Validate('required|regex:/^\d+,\d{2}$/', message: [
        'required' => 'harus diisi',
        'regex' => 'harus berupa angka dengan format koma, contoh: 3,23'
    ])]
    public $ipk;
}
