<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Assessor extends Model implements AuthenticatableContract
{
    use \Illuminate\Auth\Authenticatable;

    protected $table = 'assessor';
    protected $guarded = ['id'];

    public function setInstansiAttribute($value)
    {
        $this->attributes['instansi'] = strtoupper($value);
    }

    public function setJabatanAttribute($value)
    {
        $this->attributes['jabatan'] = Str::title($value);
    }

    public function golPangkat()
    {
        return $this->belongsTo(RefGolPangkat::class, 'gol_pangkat_id', 'id');
    }

    public function event()
    {
        return $this->belongsToMany(Event::class, 'assessor_event', 'assessor_id', 'event_id')->withTimestamps();
    }

    public function peserta()
    {
        return $this->belongsToMany(Peserta::class, 'assessor_peserta', 'assessor_id', 'peserta_id')
            ->withPivot('event_id')
            ->withTimestamps();
    }
}
