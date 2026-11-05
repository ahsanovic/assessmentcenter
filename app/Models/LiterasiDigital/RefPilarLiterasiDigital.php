<?php

namespace App\Models\LiterasiDigital;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RefPilarLiterasiDigital extends Model
{
    protected $table = 'ref_pilar_literasi_digital';

    protected $guarded = ['id'];

    public function soal(): HasMany
    {
        return $this->hasMany(SoalLiterasiDigital::class, 'pilar_id');
    }
}
