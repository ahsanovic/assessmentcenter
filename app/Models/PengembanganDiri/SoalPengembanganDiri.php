<?php

namespace App\Models\PengembanganDiri;

use App\Models\PengembanganDiri\RefPengembanganDiri;
use Illuminate\Database\Eloquent\Model;

class SoalPengembanganDiri extends Model
{
    protected $table = 'soal_pengembangan_diri';
    protected $guarded = ['id'];

    public function jenisIndikator()
    {
        return $this->belongsTo(RefPengembanganDiri::class, 'jenis_indikator_id', 'id');
    }
}
