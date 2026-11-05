<?php

namespace App\Models\LiterasiDigital;

use App\Models\Peserta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UjianLiterasiDigital extends Model
{
    protected $table = 'ujian_literasi_digital';

    protected $guarded = ['id'];

    protected $casts = [
        'waktu_tes_berakhir' => 'datetime',
    ];

    public function peserta(): BelongsTo
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function jawaban(): HasMany
    {
        return $this->hasMany(JawabanLiterasiDigital::class, 'ujian_id');
    }

    public function hasil(): HasOne
    {
        return $this->hasOne(HasilLiterasiDigital::class, 'ujian_id');
    }
}
