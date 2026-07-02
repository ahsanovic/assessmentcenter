<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefPegawai extends Model
{
    protected $table = 'ref_pegawai';

    protected $fillable = [
        'nama',
        'nip',
        'qrcode_path',
    ];

    public function label(): string
    {
        return "{$this->nama} — {$this->nip}";
    }

    public function hasQrCode(): bool
    {
        return filled($this->qrcode_path);
    }
}
