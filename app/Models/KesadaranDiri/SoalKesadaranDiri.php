<?php

namespace App\Models\KesadaranDiri;

use Illuminate\Database\Eloquent\Model;
use App\Models\KesadaranDiri\RefKesadaranDiri;

class SoalKesadaranDiri extends Model
{
    protected $table = 'soal_kesadaran_diri';
    protected $guarded = ['id'];

    public function jenisIndikator()
    {
        return $this->belongsTo(RefKesadaranDiri::class, 'jenis_indikator_id', 'id');
    }
}
