<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RwPendidikan extends Model
{
    protected $table = 'riwayat_pendidikan';
    protected $guarded = ['id'];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id', 'id');
    }

    public function jenjangPendidikan()
    {
        return $this->belongsTo(RefJenjangPendidikan::class, 'jenjang_pendidikan_id', 'id');
    }

    public function scopeWherePesertaEvent($query, $pesertaId, $eventId)
    {
        return $query->where('peserta_id', $pesertaId)
            ->where('event_id', $eventId);
    }
}
