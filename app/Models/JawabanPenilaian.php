<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanPenilaian extends Model
{
    protected $table = 'jawaban_penilaian';
    protected $fillable = ['event_id', 'peserta_id', 'pertanyaan_id', 'jawaban'];

    public function pertanyaan()
    {
        return $this->belongsTo(RefPertanyaanPenilaian::class, 'pertanyaan_id', 'id');
    }

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id', 'id');
    }

    public function scopeWherePesertaEvent($query, $pesertaId, $eventId)
    {
        return $query->where('peserta_id', $pesertaId)
            ->where('event_id', $eventId);
    }
}
