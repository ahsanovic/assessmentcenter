<?php

namespace App\Models\Intelektual;

use Illuminate\Database\Eloquent\Model;

class RefIntelektual extends Model
{
    protected $table = 'ref_intelektual';
    protected $guarded = ['id'];
    protected $casts = [
        'kualifikasi' => 'array',
    ];

    public function setIndikatorAttribute($value)
    {
        $this->attributes['indikator'] = strtoupper($value);
    }
}
