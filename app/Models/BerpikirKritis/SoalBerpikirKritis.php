<?php

namespace App\Models\BerpikirKritis;

use Illuminate\Database\Eloquent\Model;
use App\Models\BerpikirKritis\RefIndikatorBerpikirKritis;

class SoalBerpikirKritis extends Model
{
    protected $table = 'soal_berpikir_kritis';
    protected $guarded = ['id'];

    public function indikator()
    {
        return $this->belongsTo(RefIndikatorBerpikirKritis::class, 'indikator_nomor', 'id');
    }

    public function aspek()
    {
        return $this->belongsTo(RefAspekBerpikirKritis::class, 'aspek_id', 'id');
    }
}
