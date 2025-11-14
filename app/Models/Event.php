<?php

namespace App\Models;

use App\Models\BerpikirKritis\HasilBerpikirKritis;
use App\Models\BerpikirKritis\UjianBerpikirKritis;
use App\Models\CakapDigital\HasilCakapDigital;
use App\Models\CakapDigital\UjianCakapDigital;
use App\Models\Intelektual\HasilIntelektual;
use App\Models\Intelektual\UjianIntelektualSubTes1;
use App\Models\Intelektual\UjianIntelektualSubTes2;
use App\Models\Intelektual\UjianIntelektualSubTes3;
use App\Models\Interpersonal\HasilInterpersonal;
use App\Models\Interpersonal\UjianInterpersonal;
use App\Models\KecerdasanEmosi\HasilKecerdasanEmosi;
use App\Models\KecerdasanEmosi\UjianKecerdasanEmosi;
use App\Models\KesadaranDiri\HasilKesadaranDiri;
use App\Models\KesadaranDiri\UjianKesadaranDiri;
use App\Models\KompetensiTeknis\HasilKompetensiTeknis;
use App\Models\KompetensiTeknis\UjianKompetensiTeknis;
use App\Models\MotivasiKomitmen\HasilMotivasiKomitmen;
use App\Models\MotivasiKomitmen\UjianMotivasiKomitmen;
use App\Models\PengembanganDiri\HasilPengembanganDiri;
use App\Models\PengembanganDiri\UjianPengembanganDiri;
use App\Models\ProblemSolving\HasilProblemSolving;
use App\Models\ProblemSolving\UjianProblemSolving;
use App\Models\Pspk\HasilPspk;
use App\Models\Pspk\UjianPspk;
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

    public function metodeTes()
    {
        return $this->hasOne(RefMetodeTes::class, 'id', 'metode_tes_id');
    }

    public function nomorLaporan()
    {
        return $this->hasMany(NomorLaporan::class, 'event_id', 'id');
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
        return $this->belongsToMany(RefAlatTes::class, 'event_alat_tes', 'event_id', 'ref_alat_tes_id')->withTimestamps();
    }

    public function jawabanResponden()
    {
        return $this->hasMany(JawabanResponden::class, 'event_id', 'id');
    }

    public function logPelanggaran()
    {
        return $this->hasMany(LogPelanggaran::class, 'event_id', 'id');
    }

    public function assessor()
    {
        return $this->belongsToMany(Assessor::class, 'assessor_event', 'event_id', 'assessor_id')->withTimestamps();
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

    // ujian PSPK
    public function ujianPspk()
    {
        return $this->hasMany(UjianPspk::class, 'event_id', 'id');
    }

    public function pesertaTesPspk()
    {
        return $this->hasManyThrough(
            Peserta::class,
            UjianPspk::class,
            'event_id',
            'id',
            'id',
            'peserta_id'
        );
    }

    public function pesertaIdTesPspk()
    {
        return $this->ujianPspk()->select('peserta_id')->distinct();
    }

    // ujian kompetensi teknis
    public function ujianKompetensiTeknis()
    {
        return $this->hasMany(UjianKompetensiTeknis::class, 'event_id', 'id');
    }

    public function pesertaTesKompetensiTeknis()
    {
        return $this->hasManyThrough(
            Peserta::class,
            UjianKompetensiTeknis::class,
            'event_id',
            'id',
            'id',
            'peserta_id'
        );
    }

    public function pesertaIdTesKompetensiTeknis()
    {
        return $this->ujianKompetensiTeknis()->select('peserta_id')->distinct();
    }

    // ujian cakap digital
    public function ujianCakapDigital()
    {
        return $this->hasMany(UjianCakapDigital::class, 'event_id', 'id');
    }

    public function pesertaTesCakapDigital()
    {
        return $this->hasManyThrough(
            Peserta::class,
            UjianCakapDigital::class,
            'event_id',
            'id',
            'id',
            'peserta_id'
        );
    }

    public function pesertaIdTesCakapDigital()
    {
        return $this->ujianCakapDigital()->select('peserta_id')->distinct();
    }

    // ujian intelektual subtes 1
    public function ujianIntelektualSubTes1()
    {
        return $this->hasMany(UjianIntelektualSubTes1::class, 'event_id', 'id');
    }

    public function pesertaTesIntelektualSubTes1()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            UjianIntelektualSubTes1::class,    // Model perantara (UjianIntelektualSubTes1)
            'event_id',                   // Foreign key di UjianIntelektualSubTes1
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianIntelektualSubTes1
        );
    }

    public function pesertaIdTesIntelektualSubTes1()
    {
        return $this->ujianIntelektualSubTes1()->select('peserta_id')->distinct();
    }

    // ujian intelektual subtes 2
    public function ujianIntelektualSubTes2()
    {
        return $this->hasMany(UjianIntelektualSubTes2::class, 'event_id', 'id');
    }

    public function pesertaTesIntelektualSubTes2()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            UjianIntelektualSubTes2::class,    // Model perantara (UjianIntelektualSubTes2)
            'event_id',                   // Foreign key di UjianIntelektualSubTes2
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianIntelektualSubTes2
        );
    }

    public function pesertaIdTesIntelektualSubTes2()
    {
        return $this->ujianIntelektualSubTes2()->select('peserta_id')->distinct();
    }

    // ujian intelektual subtes 3
    public function ujianIntelektualSubTes3()
    {
        return $this->hasMany(UjianIntelektualSubTes3::class, 'event_id', 'id');
    }

    public function pesertaTesIntelektualSubTes3()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            UjianIntelektualSubTes3::class,    // Model perantara (UjianIntelektualSubTes3)
            'event_id',                   // Foreign key di UjianIntelektualSubTes3
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianIntelektualSubTes3
        );
    }

    public function pesertaIdTesIntelektualSubTes3()
    {
        return $this->ujianIntelektualSubTes3()->select('peserta_id')->distinct();
    }

    // ujian interpersonal
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

    // ujian kesadaran diri
    public function ujianKesadaranDiri()
    {
        return $this->hasMany(UjianKesadaranDiri::class, 'event_id', 'id');
    }

    public function pesertaTesKesadaranDiri()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            UjianKesadaranDiri::class,    // Model perantara (UjianKesadaranDiri)
            'event_id',                   // Foreign key di UjianKesadaranDiri
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianKesadaranDiri
        );
    }

    public function pesertaIdTesKesadaranDiri()
    {
        return $this->ujianKesadaranDiri()->select('peserta_id')->distinct();
    }

    // ujian berpikir kritis dan strategis
    public function ujianBerpikirKritis()
    {
        return $this->hasMany(UjianBerpikirKritis::class, 'event_id', 'id');
    }

    public function pesertaTesBerpikirKritis()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            UjianBerpikirKritis::class,    // Model perantara (UjianBerpikirKritis)
            'event_id',                   // Foreign key di UjianBerpikirKritis
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianBerpikirKritis
        );
    }

    public function pesertaIdTesBerpikirKritis()
    {
        return $this->ujianBerpikirKritis()->select('peserta_id')->distinct();
    }

    // ujian problem solving
    public function ujianProblemSolving()
    {
        return $this->hasMany(UjianProblemSolving::class, 'event_id', 'id');
    }

    public function pesertaTesProblemSolving()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            UjianProblemSolving::class,    // Model perantara (UjianProblemSolving)
            'event_id',                   // Foreign key di UjianProblemSolving
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianProblemSolving
        );
    }

    public function pesertaIdTesProblemSolving()
    {
        return $this->ujianProblemSolving()->select('peserta_id')->distinct();
    }

    // ujian kecerdasan emosi
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

    // ujian pengembangan diri
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

    // ujian motivasi dan komitmen
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

    /* HASIL TES */
    // hasil kompetensi teknis
    public function hasilKompetensiTeknis()
    {
        return $this->hasMany(HasilKompetensiTeknis::class, 'event_id', 'id');
    }

    public function pesertaHasilKompetensiTeknis()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            HasilKompetensiTeknis::class, // Model perantara (UjianKompetensiTeknis)
            'event_id',                   // Foreign key di UjianKompetensiTeknis
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianKompetensiTeknis
        );
    }

    public function pesertaIdHasilKompetensiTeknis()
    {
        return $this->hasilKompetensiTeknis()->select('peserta_id')->distinct();
    }

    // hasil PSPK
    public function hasilPspk()
    {
        return $this->hasMany(HasilPspk::class, 'event_id', 'id');
    }

    public function pesertaHasilPspk()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            HasilPspk::class,    // Model perantara (UjianPspk)
            'event_id',                   // Foreign key di UjianPspk
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianPspk
        );
    }

    public function pesertaIdHasilPspk()
    {
        return $this->hasilPspk()->select('peserta_id')->distinct();
    }

    // hasil cakap digital
    public function hasilCakapDigital()
    {
        return $this->hasMany(HasilCakapDigital::class, 'event_id', 'id');
    }

    public function pesertaHasilCakapDigital()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            HasilCakapDigital::class,    // Model perantara (UjianCakapDigital)
            'event_id',                   // Foreign key di UjianCakapDigital
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianCakapDigital
        );
    }

    public function pesertaIdHasilCakapDigital()
    {
        return $this->hasilCakapDigital()->select('peserta_id')->distinct();
    }

    // hasil intelektual
    public function hasilIntelektual()
    {
        return $this->hasMany(HasilIntelektual::class, 'event_id', 'id');
    }

    public function pesertaHasilIntelektual()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            HasilIntelektual::class,    // Model perantara (UjianIntelektual)
            'event_id',                   // Foreign key di UjianIntelektual
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianIntelektual
        );
    }

    public function pesertaIdHasilIntelektual()
    {
        return $this->hasilIntelektual()->select('peserta_id')->distinct();
    }

    // hasil interpersonal
    public function hasilInterpersonal()
    {
        return $this->hasMany(HasilInterpersonal::class, 'event_id', 'id');
    }

    public function pesertaHasilInterpersonal()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            HasilInterpersonal::class,    // Model perantara (UjianInterpersonal)
            'event_id',                   // Foreign key di UjianInterpersonal
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianInterpersonal
        );
    }

    public function pesertaIdHasilInterpersonal()
    {
        return $this->hasilInterpersonal()->select('peserta_id')->distinct();
    }

    // hasil kesadaran diri
    public function hasilKesadaranDiri()
    {
        return $this->hasMany(HasilKesadaranDiri::class, 'event_id', 'id');
    }

    public function pesertaHasilKesadaranDiri()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            HasilKesadaranDiri::class,    // Model perantara (UjianKesadaranDiri)
            'event_id',                   // Foreign key di UjianInterpersonal
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianKesadaranDiri
        );
    }

    public function pesertaIdHasilKesadaranDiri()
    {
        return $this->hasilKesadaranDiri()->select('peserta_id')->distinct();
    }

    // hasil berpikir kritis dan strategis
    public function hasilBerpikirKritis()
    {
        return $this->hasMany(HasilBerpikirKritis::class, 'event_id', 'id');
    }

    public function pesertaHasilBerpikirKritis()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            HasilBerpikirKritis::class,    // Model perantara (UjianBerpikirKritis)
            'event_id',                   // Foreign key di UjianBerpikirKritis
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianBerpikirKritis
        );
    }

    public function pesertaIdHasilBerpikirKritis()
    {
        return $this->hasilBerpikirKritis()->select('peserta_id')->distinct();
    }

    // hasil problem solving
    public function hasilProblemSolving()
    {
        return $this->hasMany(HasilProblemSolving::class, 'event_id', 'id');
    }

    public function pesertaHasilProblemSolving()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            HasilProblemSolving::class,    // Model perantara (UjianProblemSolving)
            'event_id',                   // Foreign key di UjianInterpersonal
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianProblemSolving
        );
    }

    public function pesertaIdHasilProblemSolving()
    {
        return $this->hasilProblemSolving()->select('peserta_id')->distinct();
    }

    // hasil kecerdasan emosi
    public function hasilKecerdasanEmosi()
    {
        return $this->hasMany(HasilKecerdasanEmosi::class, 'event_id', 'id');
    }

    public function pesertaHasilKecerdasanEmosi()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            HasilKecerdasanEmosi::class,    // Model perantara (UjianKecerdasanEmosi)
            'event_id',                   // Foreign key di UjianInterpersonal
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianKecerdasanEmosi
        );
    }

    public function pesertaIdHasilKecerdasanEmosi()
    {
        return $this->hasilKecerdasanEmosi()->select('peserta_id')->distinct();
    }

    // hasil pengembangan diri
    public function hasilPengembanganDiri()
    {
        return $this->hasMany(HasilPengembanganDiri::class, 'event_id', 'id');
    }

    public function pesertaHasilPengembanganDiri()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            HasilPengembanganDiri::class,    // Model perantara (UjianPengembanganDiri)
            'event_id',                   // Foreign key di UjianInterpersonal
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianPengembanganDiri
        );
    }

    public function pesertaIdHasilPengembanganDiri()
    {
        return $this->hasilPengembanganDiri()->select('peserta_id')->distinct();
    }

    // hasil motivasi komitmen
    public function hasilMotivasiKomitmen()
    {
        return $this->hasMany(HasilMotivasiKomitmen::class, 'event_id', 'id');
    }

    public function pesertaHasilMotivasiKomitmen()
    {
        return $this->hasManyThrough(
            Peserta::class,               // Model target (Peserta)
            HasilMotivasiKomitmen::class,    // Model perantara (UjianMotivasiKomitmen)
            'event_id',                   // Foreign key di UjianInterpersonal
            'id',                         // Foreign key di Peserta
            'id',                         // Local key di Event
            'peserta_id'                  // Local key di UjianMotivasiKomitmen
        );
    }

    public function pesertaIdHasilMotivasiKomitmen()
    {
        return $this->hasilMotivasiKomitmen()->select('peserta_id')->distinct();
    }
}
