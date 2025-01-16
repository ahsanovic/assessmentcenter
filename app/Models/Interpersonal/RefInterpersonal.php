<?php

namespace App\Models\Interpersonal;

use Illuminate\Database\Eloquent\Model;

class RefInterpersonal extends Model
{
    protected $table = 'ref_interpersonal';
    protected $guarded = ['id'];
    protected $casts = [
        'kualifikasi' => 'array',
    ];

    public function setIndikatorNamaAttribute($value)
    {
        $this->attributes['indikator_nama'] = strtoupper($value);
    }
}
