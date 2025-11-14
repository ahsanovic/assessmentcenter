<?php

namespace App\Models\Pspk;

use Illuminate\Database\Eloquent\Model;

class HasilPspk extends Model
{
    protected $table = 'hasil_pspk';
    protected $guarded = ['id'];
    protected $casts = [
        'deskripsi' => 'array',
        'nilai_capaian' => 'array',
    ];
}
