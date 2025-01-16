<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RwPelatihan extends Model
{
    protected $table = 'riwayat_pelatihan';
    protected $guarded = ['id'];

    protected function setTglMulaiAttribute($value)
    {
        $this->attributes['tgl_mulai'] = date('Y-m-d', strtotime($value));
    }

    protected function setTglSelesaiAttribute($value)
    {
        $this->attributes['tgl_selesai'] = date('Y-m-d', strtotime($value));
    }

    protected function getTglMulaiAttribute($value)
    {
        return date('d-m-Y', strtotime($value));
    }

    protected function getTglSelesaiAttribute($value)
    {
        return date('d-m-Y', strtotime($value));
    }

    public function scopeWherePesertaEvent($query, $pesertaId, $eventId)
    {
        return $query->where('peserta_id', $pesertaId)
            ->where('event_id', $eventId);
    }
}
