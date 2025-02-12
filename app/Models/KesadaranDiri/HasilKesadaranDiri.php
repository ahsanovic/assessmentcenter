<?php

namespace App\Models\KesadaranDiri;

use Illuminate\Database\Eloquent\Model;

class HasilKesadaranDiri extends Model
{
    protected $table = 'hasil_kesadaran_diri';
    protected $guarded = ['id'];
    protected $casts = ['nilai' => 'array'];
}
