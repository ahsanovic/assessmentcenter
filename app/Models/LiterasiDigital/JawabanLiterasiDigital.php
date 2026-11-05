<?php

namespace App\Models\LiterasiDigital;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanLiterasiDigital extends Model
{
    protected $table = 'jawaban_literasi_digital';

    protected $guarded = ['id'];

    public function ujian(): BelongsTo
    {
        return $this->belongsTo(UjianLiterasiDigital::class, 'ujian_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(SoalLiterasiDigital::class, 'soal_id');
    }

    public function pilar(): BelongsTo
    {
        return $this->belongsTo(RefPilarLiterasiDigital::class, 'pilar_id');
    }
}
