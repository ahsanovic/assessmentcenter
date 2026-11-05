<?php

namespace App\Models\LiterasiDigital;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SoalLiterasiDigital extends Model
{
    protected $table = 'soal_literasi_digital';

    protected $guarded = ['id'];

    public function pilar(): BelongsTo
    {
        return $this->belongsTo(RefPilarLiterasiDigital::class, 'pilar_id');
    }

    public function jawaban(): HasMany
    {
        return $this->hasMany(JawabanLiterasiDigital::class, 'soal_id');
    }
}
