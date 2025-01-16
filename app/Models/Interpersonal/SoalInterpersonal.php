<?php

namespace App\Models\Interpersonal;

use Illuminate\Database\Eloquent\Model;
use App\Models\Interpersonal\RefInterpersonal;

class SoalInterpersonal extends Model
{
    protected $table = 'soal_interpersonal';
    protected $guarded = ['id'];

    public function jenisIndikator()
    {
        return $this->belongsTo(RefInterpersonal::class, 'jenis_indikator_id', 'id');
    }
}
