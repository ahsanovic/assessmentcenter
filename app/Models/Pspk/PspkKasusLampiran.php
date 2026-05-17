<?php

namespace App\Models\Pspk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PspkKasusLampiran extends Model
{
    protected $table = 'pspk_kasus_lampiran';

    protected $guarded = ['id'];

    public function soalPspk(): HasMany
    {
        return $this->hasMany(SoalPspk::class, 'kasus_lampiran_id');
    }
}
