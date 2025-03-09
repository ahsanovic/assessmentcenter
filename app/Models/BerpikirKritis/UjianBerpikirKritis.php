<?php

namespace App\Models\BerpikirKritis;

use Illuminate\Database\Eloquent\Model;

class UjianBerpikirKritis extends Model
{
    protected $table = 'ujian_berpikir_kritis';
    protected $guarded = ['id'];
    protected $casts = ['waktu_tes_berakhir' => 'datetime'];
}
