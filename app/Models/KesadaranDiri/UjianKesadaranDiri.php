<?php

namespace App\Models\KesadaranDiri;

use Illuminate\Database\Eloquent\Model;

class UjianKesadaranDiri extends Model
{
    protected $table = 'ujian_kesadaran_diri';
    protected $guarded = ['id'];
    protected $casts = ['waktu_tes_berakhir' => 'datetime'];
}
