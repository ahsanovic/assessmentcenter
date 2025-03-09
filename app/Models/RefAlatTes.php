<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefAlatTes extends Model
{
    protected $table = 'ref_alat_tes';
    protected $guarded = ['id'];

    public function event()
    {
        return $this->belongsToMany(Event::class, 'event_alat_tes', 'ref_alat_tes_id', 'event_id')->withTimestamps();
    }
}
