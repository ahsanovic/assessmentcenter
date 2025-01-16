<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RwKarir extends Model
{
    protected $table = 'riwayat_karir';
    protected $guarded = ['id'];

    public function scopeWherePesertaEvent($query, $pesertaId, $eventId)
    {
        return $query->where('peserta_id', $pesertaId)
            ->where('event_id', $eventId);
    }
}
