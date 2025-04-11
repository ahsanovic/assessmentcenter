<?php

namespace App\Models\PengembanganDiri;

use Illuminate\Database\Eloquent\Model;

class UjianPengembanganDiri extends Model
{
    protected $table = 'ujian_pengembangan_diri';
    protected $guarded = ['id'];
    protected $casts = ['waktu_tes_berakhir' => 'datetime'];
}
