<?php

namespace App\Models;

use App\Models\Interpersonal\UjianInterpersonal;
use App\Models\KecerdasanEmosi\UjianKecerdasanEmosi;
use App\Models\MotivasiKomitmen\UjianMotivasiKomitmen;
use App\Models\PengembanganDiri\UjianPengembanganDiri;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'event';
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

    public function peserta()
    {
        return $this->hasMany(Peserta::class, 'event_id', 'id')->where('is_active', 'true');
    }

    public function jabatanDiuji()
    {
        return $this->belongsTo(RefJabatanDiuji::class, 'jabatan_diuji_id', 'id');
    }

    public function alatTes()
    {
        return $this->belongsToMany(RefAlatTes::class, 'event_alat_tes', 'event_id', 'ref_alat_tes_id');
    }

    public function assessor()
    {
        return $this->belongsToMany(Assessor::class, 'assessor_event', 'event_id', 'assessor_id');
    }

    public function assessorPeserta()
    {
        return $this->hasManyThrough(
            Peserta::class,
            'assessor_peserta',
            'event_id',
            'id',
            'id',
            'peserta_id'
        );
    }

    // interpersonal
    public function ujianInterpersonal()
    {
        return $this->hasMany(UjianInterpersonal::class, 'event_id', 'id');
    }

    public function pesertaTesInterpersonal()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            UjianInterpersonal::class,    // Model perantara (UjianInterpersonal)
            'event_id',                   // Foreign key di UjianInterpersonal
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianInterpersonal
        );
    }

    public function pesertaIdTesInterpersonal()
    {
        return $this->ujianInterpersonal()->select('peserta_id')->distinct();
    }


    // pengembangan diri
    public function ujianPengembanganDiri()
    {
        return $this->hasMany(UjianPengembanganDiri::class, 'event_id', 'id');
    }

    public function pesertaTesPengembanganDiri()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            UjianPengembanganDiri::class,    // Model perantara (UjianPengembanganDiri)
            'event_id',                   // Foreign key di UjianPengembanganDiri
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianPengembanganDiri
        );
    }

    public function pesertaIdTesPengembanganDiri()
    {
        return $this->ujianPengembanganDiri()->select('peserta_id')->distinct();
    }

    // kecerdasan emosi
    public function ujianKecerdasanEmosi()
    {
        return $this->hasMany(UjianKecerdasanEmosi::class, 'event_id', 'id');
    }

    public function pesertaTesKecerdasanEmosi()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            UjianKecerdasanEmosi::class,    // Model perantara (UjianKecerdasanEmosi)
            'event_id',                   // Foreign key di UjianKecerdasanEmosi
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianKecerdasanEmosi
        );
    }

    public function pesertaIdTesKecerdasanEmosi()
    {
        return $this->ujianKecerdasanEmosi()->select('peserta_id')->distinct();
    }

    // motivasi dan komitmen
    public function ujianMotivasiKomitmen()
    {
        return $this->hasMany(UjianMotivasiKomitmen::class, 'event_id', 'id');
    }

    public function pesertaTesMotivasiKomitmen()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            UjianMotivasiKomitmen::class,    // Model perantara (UjianMotivasiKomitmen)
            'event_id',                   // Foreign key di UjianMotivasiKomitmen
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianMotivasiKomitmen
        );
    }

    public function pesertaIdTesMotivasiKomitmen()
    {
        return $this->ujianMotivasiKomitmen()->select('peserta_id')->distinct();
    }
}
