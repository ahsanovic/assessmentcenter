<?php

namespace App\Models\BerpikirKritis;

use Illuminate\Database\Eloquent\Model;

class RefIndikatorBerpikirKritis extends Model
{
    protected $table = 'ref_indikator_berpikir_kritis';
    protected $guarded = ['id'];
    protected $casts = [
        'kualifikasi_deskripsi' => 'array',
    ];
}
