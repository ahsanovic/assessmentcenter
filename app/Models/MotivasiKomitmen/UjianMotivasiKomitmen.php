<?php

namespace App\Models\MotivasiKomitmen;

use Illuminate\Database\Eloquent\Model;

class UjianMotivasiKomitmen extends Model
{
    protected $table = 'ujian_motivasi_komitmen';
    protected $guarded = ['id'];
    protected $casts = ['waktu_tes_berakhir' => 'datetime'];
}
