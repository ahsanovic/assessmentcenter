<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefJenisPeserta extends Model
{
    protected $table = 'ref_jenis_peserta';
    protected $fillable = ['id', 'jenis_peserta'];
}
