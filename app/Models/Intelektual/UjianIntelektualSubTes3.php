<?php

namespace App\Models\Intelektual;

use App\Models\Peserta;
use Illuminate\Database\Eloquent\Model;

class UjianIntelektualSubTes3 extends Model
{
    protected $table = 'ujian_intelektual_subtes_3';
    protected $guarded = ['id'];
    protected $casts = ['waktu_tes_berakhir' => 'datetime'];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id', 'id');
    }
}
