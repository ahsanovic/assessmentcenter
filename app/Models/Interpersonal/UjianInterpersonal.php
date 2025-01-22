<?php

namespace App\Models\Interpersonal;

use App\Models\Peserta;
use Illuminate\Database\Eloquent\Model;

class UjianInterpersonal extends Model
{
    protected $table = 'ujian_interpersonal';
    protected $guarded = ['id'];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id', 'id');
    }
}
