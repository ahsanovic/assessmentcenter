<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiEvent extends Model
{
    protected $table = 'absensi_event';

    protected $fillable = [
        'event_id',
        'judul',
        'hari',
        'tanggal',
        'sesi',
        'peserta_dari',
        'peserta_sampai',
        'jumlah_peserta_sesi',
        'baris_tambahan',
        'waktu_mulai',
        'waktu_selesai',
        'zona_waktu',
        'tempat',
        'created_by',
    ];

    protected function setTanggalAttribute($value): void
    {
        if (blank($value)) {
            $this->attributes['tanggal'] = null;

            return;
        }

        if ($value instanceof Carbon) {
            $this->attributes['tanggal'] = $value->format('Y-m-d');

            return;
        }

        try {
            $this->attributes['tanggal'] = Carbon::createFromFormat('d-m-Y', (string) $value)->format('Y-m-d');
        } catch (\Throwable) {
            $this->attributes['tanggal'] = date('Y-m-d', strtotime((string) $value));
        }
    }

    protected function getTanggalAttribute($value): ?string
    {
        return $value ? date('d-m-Y', strtotime($value)) : null;
    }

    public static function nextPesertaDari(int $eventId, ?int $excludeId = null): int
    {
        $query = static::query()->where('event_id', $eventId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $lastSampai = $query->max('peserta_sampai');

        return $lastSampai ? $lastSampai + 1 : 1;
    }

    public static function tanggalToDatabase(string $tanggal): string
    {
        return Carbon::createFromFormat('d-m-Y', $tanggal)->format('Y-m-d');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pesertaRangeLabel(): string
    {
        if ($this->peserta_dari && $this->peserta_sampai) {
            return 'No. '.$this->peserta_dari.' – '.$this->peserta_sampai;
        }

        return 'Semua Peserta';
    }

    public function sesiLabel(): string
    {
        $label = 'Sesi '.$this->sesi;

        if ($this->tanggal) {
            $label = $this->tanggal.' · '.$label;
        }

        if ($this->peserta_dari && $this->peserta_sampai) {
            $label .= ' (No. '.$this->peserta_dari.'–'.$this->peserta_sampai.')';
        }

        return $label;
    }
}
