<?php

namespace App\Models\Interpersonal;

use App\Models\Peserta;
use Illuminate\Database\Eloquent\Model;

class UjianInterpersonal extends Model
{
    protected $table = 'ujian_interpersonal';
    protected $guarded = ['id'];
    protected $casts = ['waktu_tes_berakhir' => 'datetime'];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id', 'id');
    }
}
