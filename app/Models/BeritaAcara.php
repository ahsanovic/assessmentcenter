<?php

namespace App\Models;

use App\Services\Pegawai\PegawaiQrCodeService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeritaAcara extends Model
{
    protected $table = 'berita_acara';

    protected $fillable = [
        'event_id',
        'judul',
        'hari',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'pejabat',
        'di_lingkungan_pemerintah',
        'ruang',
        'jumlah_peserta_seharusnya',
        'jumlah_peserta_tidak_hadir',
        'jumlah_peserta_hadir',
        'nomor_tidak_hadir',
        'catatan',
        'admin_nama',
        'admin_nip',
        'admin_pegawai_id',
        'tester_nama',
        'tester_nip',
        'tester_pegawai_id',
        'created_by',
    ];

    protected function setTanggalAttribute($value): void
    {
        if (blank($value)) {
            $this->attributes['tanggal'] = null;

            return;
        }

        $this->attributes['tanggal'] = date('Y-m-d', strtotime($value));
    }

    protected function getTanggalAttribute($value): ?string
    {
        return $value ? date('d-m-Y', strtotime($value)) : null;
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function adminPegawai(): BelongsTo
    {
        return $this->belongsTo(RefPegawai::class, 'admin_pegawai_id');
    }

    public function testerPegawai(): BelongsTo
    {
        return $this->belongsTo(RefPegawai::class, 'tester_pegawai_id');
    }

    public function toPdfPayload(): array
    {
        $this->loadMissing(['adminPegawai', 'testerPegawai']);

        return [
            'event' => $this->event,
            'judul' => $this->judul,
            'hari' => $this->hari,
            'tanggal' => $this->getRawOriginal('tanggal'),
            'waktu_mulai' => $this->waktu_mulai,
            'waktu_selesai' => $this->waktu_selesai,
            'pejabat' => $this->pejabat,
            'di_lingkungan_pemerintah' => $this->di_lingkungan_pemerintah,
            'ruang' => $this->ruang,
            'jumlah_peserta_seharusnya' => $this->jumlah_peserta_seharusnya,
            'jumlah_peserta_tidak_hadir' => $this->jumlah_peserta_tidak_hadir,
            'jumlah_peserta_hadir' => $this->jumlah_peserta_hadir,
            'nomor_tidak_hadir' => $this->nomor_tidak_hadir,
            'catatan' => $this->catatan,
            'admin_nama' => $this->admin_nama,
            'admin_nip' => $this->admin_nip,
            'tester_nama' => $this->tester_nama,
            'tester_nip' => $this->tester_nip,
            'admin_qrcode' => PegawaiQrCodeService::base64DataUri($this->adminPegawai?->qrcode_path),
            'tester_qrcode' => PegawaiQrCodeService::base64DataUri($this->testerPegawai?->qrcode_path),
        ];
    }
}
