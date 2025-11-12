<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Livewire\Attributes\Validate;

class SoalPspkForm extends Form
{
    #[Validate('required', message: 'harus dipilih')]
    public $level_pspk_id;

    #[Validate('required', message: 'harus dipilih')]
    public $aspek;

    #[Validate('required', message: 'harus diisi')]
    public $soal;

    #[Validate('required', message: 'harus diisi')]
    public $opsi_a;

    #[Validate('required|integer|between:1,3', message: [
        'poin_opsi_a.required' => 'harus diisi',
        'poin_opsi_a.integer' => 'harus angka',
        'poin_opsi_a.between' => 'harus antara 1 sampai 3'
    ])]
    public $poin_opsi_a;

    #[Validate('required', message: 'harus diisi')]
    public $opsi_b;

    #[Validate('required|integer|between:1,3', message: [
        'poin_opsi_b.required' => 'harus diisi',
        'poin_opsi_b.integer' => 'harus angka',
        'poin_opsi_b.between' => 'harus antara 1 sampai 3'
    ])]
    public $poin_opsi_b;

    #[Validate('required', message: 'harus diisi')]
    public $opsi_c;

    #[Validate('required|integer|between:1,3', message: [
        'poin_opsi_c.required' => 'harus diisi',
        'poin_opsi_c.integer' => 'harus angka',
        'poin_opsi_c.between' => 'harus antara 1 sampai 3'
    ])]
    public $poin_opsi_c;

    #[Validate('required', message: 'harus diisi')]
    public $opsi_d;

    #[Validate('required|integer|between:1,3', message: [
        'poin_opsi_d.required' => 'harus diisi',
        'poin_opsi_d.integer' => 'harus angka',
        'poin_opsi_d.between' => 'harus antara 1 sampai 3'
    ])]
    public $poin_opsi_d;

    #[Validate('required', message: 'harus diisi')]
    public $opsi_e;

    #[Validate('required|integer|between:1,3', message: [
        'poin_opsi_e.required' => 'harus diisi',
        'poin_opsi_e.integer' => 'harus angka',
        'poin_opsi_e.between' => 'harus antara 1 sampai 3'
    ])]
    public $poin_opsi_e;

    #[Validate('required', message: 'harus dipilih')]
    public $kunci_jawaban;
}
