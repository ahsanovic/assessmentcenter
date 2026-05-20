<?php

namespace App\Models\Pspk;

use Illuminate\Database\Eloquent\Model;

class RefSaranPengembangan extends Model
{
    protected $table = 'ref_saran_pengembangan';

    protected $guarded = ['id'];

    public function levelPspk()
    {
        return $this->belongsTo(RefLevelPspk::class, 'level_pspk_id');
    }
}
