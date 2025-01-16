<?php

namespace App\Models;

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
}
