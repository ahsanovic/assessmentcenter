<?php

namespace App\Support;

use App\Models\JawabanPengalaman;
use App\Models\JawabanPenilaian;
use App\Models\Peserta;
use App\Models\RefPertanyaanPengalaman;
use App\Models\RefPertanyaanPenilaian;
use App\Models\RwKarir;
use App\Models\RwPelatihan;
use App\Models\RwPendidikan;
use Illuminate\Support\Facades\Auth;

class PortofolioProgress
{
    public static function forAuthPeserta(): array
    {
        $peserta = Peserta::wherePesertaEvent(
            Auth::guard('peserta')->user()->id,
            Auth::guard('peserta')->user()->event_id
        )->firstOrFail();

        return self::forPeserta($peserta);
    }

    public static function forPeserta(Peserta $peserta): array
    {
        $peserta->loadMissing('event');
        $metode = (int) ($peserta->event->metode_tes_id ?? 0);

        $sections = [];

        $sections[] = [
            'key' => 'biodata',
            'label' => 'Biodata',
            'done' => self::biodataLengkap($peserta),
            'icon' => 'user',
            'color' => 'primary',
            'route' => route('peserta.biodata'),
            'hint' => 'Lengkapi data pribadi, kontak, dan unggah foto',
        ];

        if ($metode === 1) {
            $sections[] = [
                'key' => 'pendidikan',
                'label' => 'Pendidikan',
                'done' => RwPendidikan::wherePesertaEvent($peserta->id, $peserta->event_id)->exists(),
                'icon' => 'book',
                'color' => 'success',
                'route' => route('peserta.pendidikan'),
                'hint' => 'Tambahkan minimal satu riwayat pendidikan formal',
            ];

            $sections[] = [
                'key' => 'pelatihan',
                'label' => 'Pelatihan',
                'done' => RwPelatihan::wherePesertaEvent($peserta->id, $peserta->event_id)->exists(),
                'optional' => true,
                'icon' => 'award',
                'color' => 'warning',
                'route' => route('peserta.pelatihan'),
                'hint' => 'Opsional — tambahkan jika ada riwayat pelatihan atau kursus',
            ];

            $sections[] = [
                'key' => 'karir',
                'label' => 'Karir',
                'done' => RwKarir::wherePesertaEvent($peserta->id, $peserta->event_id)->exists(),
                'icon' => 'briefcase',
                'color' => 'info',
                'route' => route('peserta.karir'),
                'hint' => 'Tambahkan minimal satu riwayat pekerjaan',
            ];

            $sections[] = [
                'key' => 'pengalaman',
                'label' => 'Pengalaman',
                'done' => self::semuaPertanyaanPengalamanTerjawab($peserta),
                'icon' => 'star',
                'color' => 'danger',
                'route' => route('peserta.pengalaman'),
                'hint' => 'Jawab seluruh pertanyaan pengalaman spesifik',
            ];

            $sections[] = [
                'key' => 'penilaian',
                'label' => 'Penilaian',
                'done' => self::semuaPertanyaanPenilaianTerjawab($peserta),
                'icon' => 'user-check',
                'color' => 'primary',
                'route' => route('peserta.penilaian'),
                'hint' => 'Jawab seluruh pertanyaan penilaian pribadi',
            ];
        }

        $wajib = collect($sections)->filter(fn (array $s) => ! ($s['optional'] ?? false));
        $done = $wajib->where('done', true)->count();
        $total = $wajib->count();
        $percent = $total > 0 ? (int) round(100 * $done / $total) : 0;

        return [
            'sections' => $sections,
            'done' => $done,
            'total' => $total,
            'percent' => $percent,
            'metode_tes_id' => $metode,
        ];
    }

    private static function biodataLengkap(Peserta $p): bool
    {
        $a = $p->getAttributes();

        $checks = [
            filled($a['nik'] ?? null),
            strlen(preg_replace('/\D/', '', (string) ($a['nik'] ?? ''))) === 16,
            filled($a['tempat_lahir'] ?? null),
            filled($a['tgl_lahir'] ?? null),
            filled($a['agama_id'] ?? null),
            filled($a['jk'] ?? null),
            filled($a['alamat'] ?? null),
            filled($a['no_hp'] ?? null),
            (bool) preg_match('/^\d{10,12}$/', (string) ($a['no_hp'] ?? '')),
            filled($a['unit_kerja'] ?? null),
            filled($a['instansi'] ?? null),
            filled($a['foto'] ?? null),
        ];

        if (in_array(false, $checks, true)) {
            return false;
        }

        if ((int) ($a['jenis_peserta_id'] ?? 0) === 1) {
            return filled($a['nip'] ?? null)
                && filled($a['gol_pangkat_id'] ?? null)
                && filled($a['jabatan'] ?? null);
        }

        return true;
    }

    private static function semuaPertanyaanPengalamanTerjawab(Peserta $peserta): bool
    {
        $pertanyaanIds = RefPertanyaanPengalaman::query()->orderBy('urutan')->pluck('id');
        if ($pertanyaanIds->isEmpty()) {
            return true;
        }

        $jawaban = JawabanPengalaman::wherePesertaEvent($peserta->id, $peserta->event_id)
            ->whereIn('pertanyaan_id', $pertanyaanIds)
            ->get()
            ->keyBy('pertanyaan_id');

        foreach ($pertanyaanIds as $pid) {
            $row = $jawaban->get($pid);
            if (! $row || ! self::teksJawabanTerisi($row->jawaban)) {
                return false;
            }
        }

        return true;
    }

    private static function semuaPertanyaanPenilaianTerjawab(Peserta $peserta): bool
    {
        $pertanyaanIds = RefPertanyaanPenilaian::query()->orderBy('urutan')->pluck('id');
        if ($pertanyaanIds->isEmpty()) {
            return true;
        }

        $jawaban = JawabanPenilaian::wherePesertaEvent($peserta->id, $peserta->event_id)
            ->whereIn('pertanyaan_id', $pertanyaanIds)
            ->get()
            ->keyBy('pertanyaan_id');

        foreach ($pertanyaanIds as $pid) {
            $row = $jawaban->get($pid);
            if (! $row || ! self::teksJawabanTerisi($row->jawaban)) {
                return false;
            }
        }

        return true;
    }

    private static function teksJawabanTerisi(?string $html): bool
    {
        $plain = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $html)));

        return $plain !== '';
    }
}
