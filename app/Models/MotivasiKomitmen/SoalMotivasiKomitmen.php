<?php

namespace App\Models\MotivasiKomitmen;

use App\Models\MotivasiKomitmen\RefMotivasiKomitmen;
use Illuminate\Database\Eloquent\Model;

class SoalMotivasiKomitmen extends Model
{
    protected $table = 'soal_motivasi_komitmen';
    protected $guarded = ['id'];

    public function jenisIndikator()
    {
        return $this->belongsTo(RefMotivasiKomitmen::class, 'jenis_indikator_id', 'id');
    }
}
