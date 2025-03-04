<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = 'setting_tes';
    protected $guarded = ['id'];

    public function alatTes()
    {
        return $this->belongsTo(RefAlatTes::class, 'alat_tes_id', 'id');
    }
}
