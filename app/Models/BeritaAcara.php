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
        'mekanisme_penkom',
        'jenis_penkom',
        'nama_kegiatan',
        'judul',
        'nomor_surat',
        'hari',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'zona_waktu',
        'pejabat',
        'pejabat_dinilai',
        'di_lingkungan_pemerintah',
        'ruang',
        'tempat',
        'jumlah_peserta_seharusnya',
        'jumlah_peserta_tidak_hadir',
        'jumlah_peserta_hadir',
        'nomor_tidak_hadir',
        'alasan_tidak_hadir',
        'catatan',
        'tanggal_penyerahan_rekap',
        'tanggal_penyerahan_laporan',
        'admin_nama',
        'admin_nip',
        'admin_pegawai_id',
        'panitia1_instansi',
        'tester_nama',
        'tester_nip',
        'tester_pegawai_id',
        'panitia2_instansi',
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

    protected function setTanggalPenyerahanRekapAttribute($value): void
    {
        $this->attributes['tanggal_penyerahan_rekap'] = blank($value) ? null : date('Y-m-d', strtotime($value));
    }

    protected function getTanggalPenyerahanRekapAttribute($value): ?string
    {
        return $value ? date('d-m-Y', strtotime($value)) : null;
    }

    protected function setTanggalPenyerahanLaporanAttribute($value): void
    {
        $this->attributes['tanggal_penyerahan_laporan'] = blank($value) ? null : date('Y-m-d', strtotime($value));
    }

    protected function getTanggalPenyerahanLaporanAttribute($value): ?string
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
            'mekanisme_penkom' => $this->mekanisme_penkom,
            'jenis_penkom' => $this->jenis_penkom,
            'nama_kegiatan' => $this->nama_kegiatan,
            'judul' => $this->judul,
            'nomor_surat' => $this->nomor_surat,
            'hari' => $this->hari,
            'tanggal' => $this->getRawOriginal('tanggal'),
            'waktu_mulai' => $this->waktu_mulai,
            'waktu_selesai' => $this->waktu_selesai,
            'zona_waktu' => $this->zona_waktu,
            'pejabat' => $this->pejabat,
            'pejabat_dinilai' => $this->pejabat_dinilai,
            'di_lingkungan_pemerintah' => $this->di_lingkungan_pemerintah,
            'ruang' => $this->ruang,
            'tempat' => $this->tempat,
            'jumlah_peserta_seharusnya' => $this->jumlah_peserta_seharusnya,
            'jumlah_peserta_tidak_hadir' => $this->jumlah_peserta_tidak_hadir,
            'jumlah_peserta_hadir' => $this->jumlah_peserta_hadir,
            'nomor_tidak_hadir' => $this->nomor_tidak_hadir,
            'alasan_tidak_hadir' => $this->alasan_tidak_hadir,
            'catatan' => $this->catatan,
            'tanggal_penyerahan_rekap' => $this->getRawOriginal('tanggal_penyerahan_rekap'),
            'tanggal_penyerahan_laporan' => $this->getRawOriginal('tanggal_penyerahan_laporan'),
            'admin_nama' => $this->admin_nama,
            'admin_nip' => $this->admin_nip,
            'panitia1_instansi' => $this->panitia1_instansi,
            'tester_nama' => $this->tester_nama,
            'tester_nip' => $this->tester_nip,
            'panitia2_instansi' => $this->panitia2_instansi,
            'admin_qrcode' => PegawaiQrCodeService::base64DataUri($this->adminPegawai?->qrcode_path),
            'tester_qrcode' => PegawaiQrCodeService::base64DataUri($this->testerPegawai?->qrcode_path),
        ];
    }
}
