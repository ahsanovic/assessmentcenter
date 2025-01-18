<?php

namespace App\Models\MotivasiKomitmen;

use Illuminate\Database\Eloquent\Model;

class RefMotivasiKomitmen extends Model
{
    protected $table = 'ref_motivasi_komitmen';
    protected $guarded = ['id'];
    protected $casts = [
        'kualifikasi' => 'array',
    ];

    public function setIndikatorNamaAttribute($value)
    {
        $this->attributes['indikator_nama'] = strtoupper($value);
    }
}
