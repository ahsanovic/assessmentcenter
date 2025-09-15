<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefMetodeTes extends Model
{
    protected $table = 'ref_metode_tes';
    protected $fillable = ['metode_tes'];

    public function event()
    {
        return $this->belongsTo(Event::class, 'metode_tes_id', 'id');
    }
}
