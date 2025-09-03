<?php

namespace App\Models\Intelektual;

use App\Models\Peserta;
use Illuminate\Database\Eloquent\Model;

class UjianIntelektualSubTes1 extends Model
{
    protected $table = 'ujian_intelektual_subtes_1';
    protected $guarded = ['id'];
    protected $casts = ['waktu_tes_berakhir' => 'datetime'];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id', 'id');
    }
}
