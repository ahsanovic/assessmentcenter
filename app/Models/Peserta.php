<?php

namespace App\Models;

use App\Models\BerpikirKritis\HasilBerpikirKritis;
use App\Models\BerpikirKritis\UjianBerpikirKritis;
use App\Models\Interpersonal\HasilInterpersonal;
use App\Models\Interpersonal\UjianInterpersonal;
use App\Models\KecerdasanEmosi\HasilKecerdasanEmosi;
use App\Models\KecerdasanEmosi\UjianKecerdasanEmosi;
use App\Models\KesadaranDiri\HasilKesadaranDiri;
use App\Models\KesadaranDiri\UjianKesadaranDiri;
use App\Models\MotivasiKomitmen\HasilMotivasiKomitmen;
use App\Models\MotivasiKomitmen\UjianMotivasiKomitmen;
use App\Models\PengembanganDiri\HasilPengembanganDiri;
use App\Models\PengembanganDiri\UjianPengembanganDiri;
use App\Models\ProblemSolving\HasilProblemSolving;
use App\Models\ProblemSolving\UjianProblemSolving;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Peserta extends Model implements AuthenticatableContract
{
    use \Illuminate\Auth\Authenticatable;

    protected $table = 'peserta';
    protected $guarded = ['id'];

    public function setNamaAttribute($value)
    {
        $this->attributes['nama'] = strtoupper($value);
    }

    public function setUnitKerjaAttribute($value)
    {
        $this->attributes['unit_kerja'] = strtoupper($value);
    }

    public function setInstansiAttribute($value)
    {
        $this->attributes['instansi'] = strtoupper($value);
    }

    public function setJabatanAttribute($value)
    {
        $this->attributes['jabatan'] = Str::title($value);
    }

    protected function getTglLahirAttribute($value)
    {
        return date('d-m-Y', strtotime($value));
    }

    protected function setTglLahirAttribute($value)
    {
        $this->attributes['tgl_lahir'] = date('Y-m-d', strtotime($value));
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }

    public function golPangkat()
    {
        return $this->belongsTo(RefGolPangkat::class, 'gol_pangkat_id', 'id');
    }

    public function assessor()
    {
        return $this->belongsToMany(Assessor::class, 'assessor_peserta', 'peserta_id', 'assessor_id')
            ->withPivot('event_id')
            ->withTimestamps();
    }

    public function pendidikan()
    {
        return $this->hasMany(RwPendidikan::class, 'peserta_id', 'id');
    }

    public function agama()
    {
        return $this->belongsTo(RefAgama::class, 'agama_id', 'id');
    }

    public function jawabanResponden()
    {
        return $this->hasMany(JawabanResponden::class, 'peserta_id', 'id');
    }

    public function logPelanggaran()
    {
        return $this->hasMany(LogPelanggaran::class, 'peserta_id', 'id');
    }

    public function scopeWherePesertaEvent($query, $pesertaId, $eventId)
    {
        return $query->where('id', $pesertaId)
            ->where('event_id', $eventId);
    }

    public function ujianInterpersonal()
    {
        return $this->hasOne(UjianInterpersonal::class, 'peserta_id');
    }

    public function ujianKecerdasanEmosi()
    {
        return $this->hasOne(UjianKecerdasanEmosi::class, 'peserta_id');
    }

    public function ujianBerpikirKritis()
    {
        return $this->hasOne(UjianBerpikirKritis::class, 'peserta_id');
    }

    public function ujianPengembanganDiri()
    {
        return $this->hasOne(UjianPengembanganDiri::class, 'peserta_id');
    }

    public function ujianMotivasiKomitmen()
    {
        return $this->hasOne(UjianMotivasiKomitmen::class, 'peserta_id');
    }

    public function ujianKesadaranDiri()
    {
        return $this->hasOne(UjianKesadaranDiri::class, 'peserta_id');
    }

    public function ujianProblemSolving()
    {
        return $this->hasOne(UjianProblemSolving::class, 'peserta_id');
    }


    public function hasilInterpersonal()
    {
        return $this->hasOne(HasilInterpersonal::class, 'peserta_id');
    }

    public function hasilKesadaranDiri()
    {
        return $this->hasOne(HasilKesadaranDiri::class, 'peserta_id');
    }

    public function hasilBerpikirKritis()
    {
        return $this->hasOne(HasilBerpikirKritis::class, 'peserta_id');
    }

    public function hasilProblemSolving()
    {
        return $this->hasOne(HasilProblemSolving::class, 'peserta_id');
    }

    public function hasilPengembanganDiri()
    {
        return $this->hasOne(HasilPengembanganDiri::class, 'peserta_id');
    }

    public function hasilKecerdasanEmosi()
    {
        return $this->hasOne(HasilKecerdasanEmosi::class, 'peserta_id');
    }

    public function hasilMotivasiKomitmen()
    {
        return $this->hasOne(HasilMotivasiKomitmen::class, 'peserta_id');
    }

    public function nilaiJpm()
    {
        return $this->hasOne(NilaiJpm::class, 'peserta_id');
    }

    public function jenisPeserta()
    {
        return $this->belongsTo(RefJenisPeserta::class, 'jenis_peserta_id', 'id');
    }

    public function getIsPortofolioLengkapAttribute()
    {
        $asn = $this->jenis_peserta_id == 1;
        $non_asn = $this->jenis_peserta_id == 2;

        $common_fields = $this->tempat_lahir &&
            $this->tgl_lahir &&
            $this->jk &&
            $this->agama_id &&
            $this->alamat &&
            $this->no_hp &&
            $this->foto;

        if ($asn) {
            return $common_fields && $this->nip && $this->gol_pangkat_id;
        } else if ($non_asn) {
            return $common_fields && $this->nik;
        }

        return false;
    }
}
