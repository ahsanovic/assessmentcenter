<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogPelanggaran extends Model
{
    protected $table = 'log_pelanggaran';
    protected $fillable = [
        'id',
        'user_id',
        'event_id',
        'keterangan'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
