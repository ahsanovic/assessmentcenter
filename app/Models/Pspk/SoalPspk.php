<?php

namespace App\Models\Pspk;

use App\Models\RefAspekPspk;
use Illuminate\Database\Eloquent\Model;

class SoalPspk extends Model
{
    public const JENIS_ANKAS = 1;

    public const JENIS_SJT = 2;

    protected $table = 'soal_pspk';

    protected $guarded = ['id'];

    public function aspek()
    {
        return $this->belongsTo(RefAspekPspk::class, 'aspek_id', 'id');
    }

    public function levelPspk()
    {
        return $this->belongsTo(RefLevelPspk::class, 'level_pspk_id', 'id');
    }

    public function kasusLampiran()
    {
        return $this->belongsTo(PspkKasusLampiran::class, 'kasus_lampiran_id');
    }

    public function perluPaketKasusPdf(): bool
    {
        return in_array((int) $this->level_pspk_id, [3, 4], true)
            && (int) $this->jenis_soal === self::JENIS_ANKAS;
    }
}
