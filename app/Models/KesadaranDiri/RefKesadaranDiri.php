<?php

namespace App\Models\KesadaranDiri;

use Illuminate\Database\Eloquent\Model;

class RefKesadaranDiri extends Model
{
    protected $table = 'ref_kesadaran_diri';
    protected $guarded = ['id'];
    protected $casts = [
        'kualifikasi' => 'array',
    ];
}
