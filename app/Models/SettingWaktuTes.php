<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingWaktuTes extends Model
{
    protected $table = 'setting_waktu_tes';
    protected $fillable = [
        'waktu',
        'created_at',
        'updated_at',
    ];
}
