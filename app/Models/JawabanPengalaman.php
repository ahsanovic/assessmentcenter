<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanPengalaman extends Model
{
    protected $table = 'jawaban_pengalaman';
    protected $fillable = ['event_id', 'jawaban', 'peserta_id', 'pertanyaan_id'];

    public function pertanyaan()
    {
        return $this->belongsTo(RefPertanyaanPengalaman::class, 'pertanyaan_id', 'id');
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
